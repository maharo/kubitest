<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/brand")
 */
class BrandAdminController extends AbstractController
{
    /**
     * @Route("/", name="app_brand_index", methods={"GET"})
     */
    public function index(BrandRepository $brandRepository): Response
    {
        return $this->render('admin/brand/index.html.twig', [
            'brands' => $brandRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_brand_new", methods={"GET", "POST"})
     */
    public function new(Request $request, BrandRepository $brandRepository): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandRepository->add($brand, true);

            return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_brand_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandRepository->add($brand, true);

            return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_brand_delete", methods={"POST"})
     */
    public function delete(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            $brandRepository->remove($brand, true);
        }

        return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
    }
}
