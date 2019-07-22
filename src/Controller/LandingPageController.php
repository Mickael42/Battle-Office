<?php

namespace App\Controller;

use App\Entity\Order;
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
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findAll();


        //form order instantiation
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);


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
