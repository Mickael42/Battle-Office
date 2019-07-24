<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Customer;
use App\Entity\Orders;
use GuzzleHttp\Client;
use App\Entity\Product;
use App\Form\OrderType;
use App\Manager\OrderManager;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class LandingPageController extends Controller
{
  /**
   * @Route("/", name="landing_page")
   * @throws \Exception
   */
  public function index(Request $request)
  {
    //get all informations  about products
    $repository = $this->getDoctrine()->getRepository(Product::class);
    $products = $repository->findAll();


    //form order instantiation
    $order = new Orders();
    $form = $this->createForm(OrderType::class, $order);




    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      //get the product choosen by the client and set all infos about the product in the order class
      $idProduct = $request->get('product');
      $repository = $this->getDoctrine()->getRepository(Product::class);
      $product = $repository->findOneBy(['id' => $idProduct]);
      $order->setProduct($product);
      $order->setAmmount($product->getReducePrice());

      //set the payement method and the statut

      if ($request->request->get('stripeToken')) {
        $order->setPaymentMethod('lpmonetico');
      } else {
        $order->setPaymentMethod('lppaypal');
      };

      $order->setStatut("WAITING");

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($order);
      $entityManager->flush();

      $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'https://api-commerce.simplon-roanne.com/',
        // You can set any number of default request options.
        'timeout'  => 2.0,
        'headers' => ['Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX'],

      ]);
      $response = $client->request('POST', '/order', [
        'json' => [

          "order" => [
            "id" => $order->getId(),
            "product" => $order->getProduct()->getName(),
            "payment_method" => $order->getPaymentMethod(),
            "status" => "WAITING",
            "client" => [
              "firstname" => $order->getClient()->getFirstname(),
              "lastname" => $order->getClient()->getLastname(),
              "email" => $order->getClient()->getEmail(),
            ],
            "addresses" => [
              "billing" => [
                "address_line1" => $order->getClient()->getAddress(),
                "address_line2" => $order->getClient()->getAddress(),
                "city" => $order->getClient()->getCity(),
                "zipcode" => $order->getClient()->getZipCode(),
                "country" => $order->getClient()->getCountry(),
                "phone" => $order->getClient()->getPhone(),
              ],
              "shipping" => [
                "address_line1" => $order->getClient()->getAddress(),
                "address_line2" => $order->getClient()->getAddress(),
                "city" => $order->getClient()->getCity(),
                "zipcode" => $order->getClient()->getZipCode(),
                "country" => $order->getClient()->getCountry(),
                "phone" => $order->getClient()->getPhone(),
              ]
            ]
          ]
        ]
      ]);

      //get the response and convert the json array into an php array
      $json_source = $response->getBody()->getContents();
      $json_data = json_decode($json_source, true);
      $apiId = $json_data['order_id'];

      //set the apiId and flush the objet $order into the data base
      $order->setOrderApiId($apiId);
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->flush();


      return $this->redirectToRoute('payment', [
        'id' => $order->getOrderApiId(),
        'stripeToken' => $request->request->get('stripeToken'),
      ]);
    }

    return $this->render('landing_page/index_new.html.twig', [

      'form' => $form->createView(),
      'products' => $products,
    ]);
  }
  /**
   * @Route("/payment", name="payment")
   */
  public function payment(Request $request)
  {

    $apiId = $request->query->get('id');
    $repository = $this->getDoctrine()->getRepository(Orders::class);
    $order = $repository->findOneBy(['orderApiId' => $apiId]);

    //get Stripe token
    $tokenStripe = $request->query->get('stripeToken');



    ///////////////STRIPE///////////////////////
    //Create customer in stripe
    \Stripe\Stripe::setApiKey('sk_test_DgvPJmAQjodeTkrNKaWvdqGq005l2TAOTD');

    $customer = Customer::create(array([
      "name" => $order->getClient()->getLastname(),
      "email" => $order->getClient()->getEmail(),
      "source" => $tokenStripe,
    ]));


    $charge = \Stripe\Charge::create([
      'amount' => $order->getAmmount() * 100,
      'currency' => 'eur',
      'receipt_email' => $order->getClient()->getEmail(),
      "customer" => $customer->id,
    ]);

    $client = new Client([
      // Base URI is used with relative requests
      'base_uri' => 'https://api-commerce.simplon-roanne.com/',
      // You can set any number of default request options.
      'timeout'  => 2.0,
      'headers' => ['Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX'],

    ]);

    $response = $client->request('POST', '/order/'. $apiId .'/status', [
      'json' => [
        [
          "status" => "PAID"
        ]
      ]

    ]);

    dd($response->getBody()->getContents());


    return $this->render('landing_page/confirmation.html.twig', []);
  }

  /**
   * @Route("/confirmation", name="confirmation")
   */
  public function confirmation()
  {
    return $this->render('landing_page/confirmation.html.twig', []);
  }
}
