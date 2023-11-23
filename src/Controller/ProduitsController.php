<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\CategoriesType;
use App\Form\FilterSearchType;
use App\Form\ProduitsType;
use App\Repository\ProduitsRepository;
use App\Services\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produits')]
class ProduitsController extends AbstractController
{
    #[Route('/', name: 'app_produits_index', methods: ['GET'])]
    public function index(ProduitsRepository $produitsRepository): Response
    {
        return $this->render('produits/index.html.twig', [
            'produits' => $produitsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produits_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produits();
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the main product entity
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produits/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/produits_search', name: 'app_produits_search',methods: ['GET', 'POST'])]
    public function produitsCategorie(
        ProduitsRepository $produitsRepository,
        Request $request): Response
    {
//        $produits = $produitsRepository->findAll();
        $form = $this->createForm(CategoriesType::class);
        $form->handleRequest($request);
//        $data = $form->getData();

        if ($form->isSubmitted() && $form->isValid()){
            $category = $form->get('name')->getData();
            $produits = $produitsRepository->findByCategory($category);

            return $this->render('produits/produits_categorie.html.twig', [
                'produits' => $produits,
                'form' => $form,
            ]);
        }

        return $this->render('produits/produits_categorie.html.twig', [
            'produits' => $produitsRepository->findAll(),
            'form' => $form,
        ]);
    }

    #[Route('/produits_filter', name: 'app_produits_filter', methods: ['GET'])]
    public function showProduits(
        ProduitsRepository $produitsRepository,
        Request $request): Response
    {
        $form = $this->createForm(FilterSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $word = $data['word'];
            return $this->render('produits/show_products.html.twig', [
                'produits' => $produitsRepository->getByName($word),
                'form' => $form,
            ]);
        }

        return $this->render('produits/show_products.html.twig', [
            'produits' => $produitsRepository->findAll(),
//            'produits' => $produitsRepository->getByName($word),
            'form' => $form,
        ]);
    }

    #[Route('/produits_all_filters', name: 'app_produits_all_filters', methods: ['GET', 'POST'])]
    public function searchProductsByAllFilters(
        ProduitsRepository $produitsRepository,
        Request $request): Response
    {
        $formFilterSearch = $this->createForm(FilterSearchType::class);
        $formFilterSearch->handleRequest($request);

        $formCategories = $this->createForm(CategoriesType::class);
        $formCategories->handleRequest($request);

        if ($formFilterSearch->isSubmitted() && $formFilterSearch->isValid()){
            $data = $formFilterSearch->getData();
            $word = $data['word'];

            $category = $formCategories->get('name')->getData();

            return $this->render('produits/all_filters.html.twig', [
                'produitsByWord' => $produitsRepository->getByName($word),
                'formFilterSearch' => $formFilterSearch,
                'formCategories' => $formCategories,
            ]);
        }

        if ($formCategories->isSubmitted() && $formCategories->isSubmitted() ){
            $data = $formCategories->getData();
            $category = $formCategories->get('name')->getData();

            return $this->render('produits/all_filters.html.twig', [
                'produitsByCategory' => $produitsRepository->findByCategory($category),
                'formCategories' => $formCategories,
                'formFilterSearch' => $formFilterSearch,
            ]);
        }

        return $this->render('produits/all_filters.html.twig', [
            'produits' => $produitsRepository->findAll(),
//            'produits' => $produitsRepository->getByName($word),
            'formFilterSearch' => $formFilterSearch,
            'formCategories' => $formCategories,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produits_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produits $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produits/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/messages', name: 'app_message_produits', methods: ['GET'])]
    public function showMessage(MessageService $messageService): Response
    {
        $message = $messageService->showMessageService();
//        $this->container->get->('App\Services\MessageseService');

        return $this->render('produits/message_produits.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/show/{id}', name: 'app_produits_show', methods: ['GET'])]
    public function show(Produits $produit): Response
    {
        return $this->render('produits/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_produits_delete', methods: ['POST'])]
    public function delete(Request $request, Produits $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/details-produit/{id}', name: 'app_details_produit', methods: ['GET'])]
    public function detailsProduit(ProduitsRepository $produitsRepository, int $id): Response
    {
        return $this->render('produits/show.html.twig', [
//            'produit' => $produitsRepository->find($id),
            'produit' => $produitsRepository->findOneBy(['id' => $id], ['price' => 'ASC'])
        ]);
    }
}
