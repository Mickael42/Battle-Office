<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Client;
use App\Entity\Product;
use App\Form\OrderType;
use App\Manager\OrderManager;
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

            return $this->redirectToRoute('landing_page');
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
