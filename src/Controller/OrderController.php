<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Form\CreateOrderType;
use App\Form\ProductOrderType;
use App\Form\SubmitOrderType;
use App\Repository\ProductRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class OrderController extends AbstractController
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }


    /**
     * @Route("/", name="app_home", methods={"GET", "POST"})
     */
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
    ): Response {
        $user = $this->getUser();
        $order = $this->orderService->getOrCreateOrder($user);

        $products = $productRepository->findAll();
        $forms = $this->createProductForms($products);

        $submitOrderForm = $this->createForm(SubmitOrderType::class);
        $submitOrderForm->handleRequest($request);

        $createOrderForm = $this->createForm(CreateOrderType::class);
        $createOrderForm->handleRequest($request);

        if ($submitOrderForm->isSubmitted() && $submitOrderForm->isValid()) {
            if ($order->getOrderItems() && $order->getOrderItems()->count() > 0 ) {
                $order->setValidatedAt(new \DateTimeImmutable());
                $entityManager->flush();

                $this->addFlash('success', 'Votre commande est enregistrée');
            } else {
                $this->addFlash('error', 'Votre commande n\'a pas pu être enregistrée');
            }
            return $this->redirectToRoute('app_home');
        }


        if ($createOrderForm->isSubmitted() && $createOrderForm->isValid()) {
            $order = $this->orderService->createOrder($user);            
            return $this->redirectToRoute('app_home');
        }

        if ($request->isMethod('POST')) {
            $this->handleProductOrderForm($request, $entityManager, $order, $productRepository);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('order/index.html.twig', [
            'products' => $products,
            'forms' => $forms,
            'order' => $order,
            'submitOrderForm' => $submitOrderForm->createView(),
            'createOrderForm' => $createOrderForm->createView()
        ]);
    }


    private function createProductForms(array $products): array
    {
        $forms = [];

        foreach ($products as $product) {
            $form = $this->createForm(ProductOrderType::class, null, [
                'product_id' => $product->getId(),
                'action' => $this->generateUrl('app_home'),
                'method' => 'POST',
            ])->createView();

            $forms[$product->getId()] = $form;
        }

        return $forms;
    }

    private function handleProductOrderForm(
        Request $request,
        EntityManagerInterface $entityManager,
        Order $order,
        ProductRepository $productRepository
    ): void {
        $form = $this->createForm(ProductOrderType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $product = $productRepository->find($data['productId']);

            if (!$product) {
                $this->addFlash('danger', 'Produit introuvable.');
                return;
            }

            if ($data['quantity'] > $product->getStock()) {
                $this->addFlash('danger', 'Pas assez de stock pour ce produit');
                return;
            }

            $existingOrderItem = null;
            foreach ($order->getOrderItems() as $orderItem) {
                if ($orderItem->getProduct()->getId() === $product->getId()) {
                    $existingOrderItem = $orderItem;
                    break;
                }
            }


            if ($existingOrderItem) {
                $newQuantity = $existingOrderItem->getQuantity() + $data['quantity'];
                $existingOrderItem->setQuantity($newQuantity);
                $this->addFlash('success', 'La quantité dans la commande a été mise à jour');
            } else {
                $orderItem = $this->createOrderItem($order, $product, $data['quantity']);
                $entityManager->persist($orderItem);
                $this->addFlash('success', 'Produit ajouté dans la commande');
            }
            
            $entityManager->flush();
        }
    }


    private function createOrderItem(Order $order, Product $product, int $quantity): OrderItem
    {
        $orderItem = new OrderItem();
        $orderItem->setOrderForm($order);
        $orderItem->setProduct($product);
        $orderItem->setQuantity($quantity);
        $orderItem->setCreatedAt(new \DateTimeImmutable());
        $orderItem->setPrice($product->getPrice());

        return $orderItem;
    }
}
