<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/order-management")
 */
class OrderManagementController extends AbstractController
{
    /**
     * @Route("/", name="app_order_management", methods={"GET"})
     */
    public function index(OrderRepository $orderRepository): Response {
        
        $orders = $orderRepository->findAll();

        return $this->render('admin/order_management/index.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/{id}/show", name="app_order_management_show", methods={"GET"})
     */
    public function edit(Order $order): Response
    {
        return $this->renderForm('admin/order_management/details.html.twig', [
            'order' => $order,
            'display_button' => false
        ]);
    }

}
