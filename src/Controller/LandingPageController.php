<?php

namespace App\Controller;

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

            //set the payement method and the statut
            $paymentMethod = $request->request->get('order')['payment_method'];
            $order->setPaymentMethod($paymentMethod);
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
            $response = $client->request('POST', '/order', ['json' => [
                
                    "order"=> [
                      "id"=> $order->getId(),
                      "product"=> $order->getProduct()->getName(),
                      "payment_method"=> $order->getPaymentMethod(),
                      "status"=> "WAITING",
                      "client"=> [
                        "firstname"=> $order->getClient()->getFirstname(),
                        "lastname"=> $order->getClient()->getLastname(),
                        "email"=> $order->getClient()->getEmail(),
                      ],
                      "addresses"=> [
                        "billing"=> [
                          "address_line1"=> $order->getClient()->getAddress(),
                          "address_line2"=> $order->getClient()->getAddress(),
                          "city"=> $order->getClient()->getCity(),
                          "zipcode"=> $order->getClient()->getZipCode(),
                          "country"=> $order->getClient()->getCountry(),
                          "phone"=> $order->getClient()->getPhone(),
                        ],
                        "shipping"=> [
                          "address_line1"=> $order->getClient()->getAddress(),
                          "address_line2"=> $order->getClient()->getAddress(),
                          "city"=> $order->getClient()->getCity(),
                          "zipcode"=> $order->getClient()->getZipCode(),
                          "country"=> $order->getClient()->getCountry(),
                          "phone"=> $order->getClient()->getPhone(),
                        ]
                      ]
                    ]
                  ]
            ]);

            dd($response->getBody()->getContents());
                


            return $this->redirectToRoute('confirmation');
        }



        return $this->render('landing_page/index_new.html.twig', [

            'form' => $form->createView(),
            'products' => $products,
        ]);
    }
    /**
     * @Route("/confirmation", name="confirmation")
     */
    public function confirmation()
    {
        return $this->render('landing_page/confirmation.html.twig', []);
    }
}
