<?php

namespace App\Controller;

use App\Entity\OrderItem;
use App\Form\OrderItemType;
use App\Repository\OrderItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order/item")
 */
class OrderItemController extends AbstractController
{
  

    /**
     * @Route("/{id}/edit", name="app_order_item_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, OrderItem $orderItem, OrderItemRepository $orderItemRepository): Response
    {
        $form = $this->createForm(OrderItemType::class, $orderItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderItemRepository->add($orderItem, true);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('order_item/edit.html.twig', [
            'order_item' => $orderItem,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_order_item_delete", methods={"POST"})
     */
    public function delete(Request $request, OrderItem $orderItem, OrderItemRepository $orderItemRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$orderItem->getId(), $request->request->get('_token'))) {
            $orderItemRepository->remove($orderItem, true);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}