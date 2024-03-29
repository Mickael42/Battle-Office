<?php

namespace App\Controller;

use Exception;
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


    if ($form->isSubmitted()) {

      //get the product choosen by the client and set all infos about the product in the order class
      $idProduct = $request->get('product');
      $repository = $this->getDoctrine()->getRepository(Product::class);
      $product = $repository->findOneBy(['id' => $idProduct]);

      $order->setProduct($product);
      $order->setAmmount($product->getReducePrice());



      if (isset($request->get('order')['addressOrder']) || isset($request->get('order')['addressComplementOrder']) || isset($request->get('order')['cityOrder']) || isset($request->get('order')['zipCodeOrder']) || isset($request->get('order')['countryOrder'])) {

        $order->setAddressOrder($order->getClient()->getAddress());
        $order->setAddressComplementOrder($order->getClient()->getAddressComplement());
        $order->setCityOrder($order->getClient()->getCity());
        $order->setZipCodeOrder($order->getClient()->getZipCode());
        $order->setCountryOrder($order->getClient()->getCountry());
      }



      $order->setPaymentMethod($request->request->get('order')['payment_method']);
      $order->setStatut("WAITING");


      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($order);
      $entityManager->flush();


      if ($request->request->get('order')['payment_method'] == 'stripe') {

        return $this->redirectToRoute('paymentStripe', [
          'id' => $order->getId(),
        ]);
      }
    }
    return $this->render('landing_page/index_new.html.twig', [
      'form' => $form->createView(),
      'products' => $products,
    ]);
  }


  /**
   * @Route("/paymentStripe/{id}", name="paymentStripe")
   * 
   */
  public function paymentStripe(Orders $order)

  {
    /*     $orderId = $request->query->get('id');
    $repository = $this->getDoctrine()->getRepository(Orders::class);
    $order = $repository->findOneBy(['id' => $orderId]); */

    return $this->render('landing_page/paymentStripe.html.twig', [
      'id' => $order->getId()
    ]);
  }

  /**
   * @Route("/payment/{id}", name="payment")
   */
  public function payment(Orders $order, Request $request)
  {

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
              "address_line2" => $order->getClient()->getAddressComplement(),
              "city" => $order->getClient()->getCity(),
              "zipcode" => $order->getClient()->getZipCode(),
              "country" => $order->getClient()->getCountry(),
              "phone" => $order->getClient()->getPhone(),
            ],
            "shipping" => [
              "address_line1" => $order->getClient()->getAddress(),
              "address_line2" => $order->getClient()->getAddressComplement(),
              "city" => $order->getClient()->getCity(),
              "zipcode" => $order->getClient()->getZipCode(),
              "country" => $order->getClient()->getCountry(),
              "phone" => $order->getClient()->getPhone(),
            ]
          ]
        ]
      ],
      'headers' => [
        'User-Agent' => 'micka',
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



    //////////////STRIPE///////////////////////


    //get Stripe token
    $tokenStripe = $request->request->get('stripeToken');

    //Create customer in stripe
    \Stripe\Stripe::setApiKey('sk_test_DgvPJmAQjodeTkrNKaWvdqGq005l2TAOTD');

    $customer = Customer::create(array([
      "name" => $order->getClient()->getLastname(),
      "email" => $order->getClient()->getEmail(),
      "source" => $tokenStripe,

    ]));


    try {
      $charge = \Stripe\Charge::create([
        'amount' => $order->getAmmount() * 100,
        'currency' => 'eur',
        'receipt_email' => $order->getClient()->getEmail(),
        "customer" => $customer->id,
      ]);
    } catch (Exception $e) {
      $this->addFlash('error',"Il y a une erreur avec la carte de paiement, veuillez réessayer");
      return $this->redirectToRoute('paymentStripe', [
        "id" => $order->getId(),
      ]);
    }

    $order->setStatut('PAID');
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($order);
    $entityManager->flush();


    ///////////////END STRIPE///////////////////////


    // updating the order status
    $client = new Client([
      // Base URI is used with relative requests
      'base_uri' => 'https://api-commerce.simplon-roanne.com/',
      // You can set any number of default request options.
      'timeout'  => 2.0,
      'headers' => ['Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX'],

    ]);

    $response = $client->request('POST', '/order/' . $apiId . '/status', [
      'json' => [
        "status" => $order->getStatut(),
      ],
      'headers' => [
        'User-Agent' => 'micka',
      ]
    ]);


    //get the response and convert the json array into an php array
    $json_source = $response->getBody()->getContents();
    $json_data = json_decode($json_source, true);
    $status = $json_data['success'];


    //set the new status and flush the objet $order into the data base
    $order->setStatut($status);
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    return $this->redirectToRoute('confirmation', [
      'id' => $order->getId(),
    ]);
  }

  /**
   * @Route("/confirmation/{id}", name="confirmation")
   */
  public function confirmation(Orders $order, \Swift_Mailer $mailer)
  {
    //get the order id and sellect all the info in the data base

    /*     $id = $request->query->get('id');
    $repository = $this->getDoctrine()->getRepository(Orders::class);
    $order = $repository->findOneBy(['id' => $id]); */



    //create the email with all the variables about the customer
    $message = (new \Swift_Message('Hello Email'))
      ->setFrom('battleoffice@example.com')
      ->setTo($order->getClient()->getEmail())
      ->setBody(
        $this->renderView(
          'emails/confirmation.html.twig',
          [
            'name' => $order->getClient()->getFirstname(),
            'product' => $order->getProduct()
          ]
        ),
        'text/html'
      );



    $mailer->send($message);

    return $this->render('landing_page/confirmation.html.twig', []);
  }
}
