<?php

namespace App\Controller;

use App\Entity\Photos;
use App\Entity\Produits;
use App\Form\CategoriesType;
use App\Form\FilterSearchType;
use App\Form\ProduitsType;
use App\Repository\PhotosRepository;
use App\Repository\ProduitsRepository;
use App\Services\MessageService;
use App\Services\SimpleUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        SimpleUploadService $simpleUploadService,
    ): Response
    {
        $produit = new Produits();
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            $photos = $request->files->get('produits')['photos'] ?? null;
//            dd($photos);
            $photos = $request->files->all();

            if ($photos == null){
                $this->addFlash('danger', 'Each product must have at least one photo');
                return $this->redirectToRoute('app_produits_new');
            } else {
                $images = $photos['produits']['photos'];
//                dd($images);
                foreach ($images as $image){
                    $new_photos = new Photos();
                    $image_new = $image['name'];
                    $new_photo = $simpleUploadService->uploadImage($image_new);
                    $new_photos->setName($new_photo);
                    $produit->addPhoto($new_photos);

                    //If we want to use slug instead of id, we create a column slug and use it instead of id
//                    $separator = '-';
//                    $slug = trim($slugger->slug($form->get('name')->getData(), $separator)->lower());
//                    $produit->setSlug($slug);

                    //TODO Maybe I don't need here
                    // Persist the Photos entity
//                    $entityManager->persist($new_photos);
//                    $entityManager->flush();
                }
            }

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
            'formFilterSearch' => $formFilterSearch,
            'formCategories' => $formCategories,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produits_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Produits $produit,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        EventDispatcherInterface $dispatcher,
        PhotosRepository $photosRepository,
        SimpleUploadService $simpleUploadService): Response
    {
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            $photos = $request->files->get('produits')['photos'] ?? null;
            $photos = $request->files->all();

            if ($photos == null){
                $this->addFlash('danger', 'Each product must have at least one photo');
                return $this->redirectToRoute('app_produits_edit' , ['id' => $produit->getId()]);
            } else {
                $images = $photos['produits']['photos'];
//                dd($images);
                foreach ($images as $image){
                    $new_photos = new Photos();
                    $image_new = $image['name'];
                    $new_photo = $simpleUploadService->uploadImage($image_new);
                    $new_photos->setName($new_photo);
                    $produit->addPhoto($new_photos);

                    //If we want to use slug instead of id, we create a column slug and use it instead of id
//                    $separator = '-';
//                    $slug = trim($slugger->slug($form->get('name')->getData(), $separator)->lower());
//                    $produit->setSlug($slug);
                }
            }

            // Persist the Photos entity
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produits_index');
        }

        return $this->render('produits/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'photos' => $photosRepository->findBy(['produits' => $produit])
        ]);
    }

    #[Route('/messages', name: 'app_message_produits', methods: ['GET'])]
    public function showMessage(MessageService $messageService): Response
    {
        $message = $messageService->showMessageService();

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
            'produit' => $produitsRepository->findOneBy(['id' => $id], ['price' => 'ASC'])
        ]);
    }

    #[Route('/produit/{id}/delete-image/{imageId}', name: 'app_delete_image_produit', methods: ['GET', 'POST'])]
    public function deleteImageProduit(
        Produits $produit,
        EntityManagerInterface $entityManager,
        int $imageId,
        PhotosRepository $photosRepository): Response
    {
        $photoId = $photosRepository->find($imageId);
        if ($photoId && $photoId->getProduits() === $produit)
        {
            $entityManager->remove($photoId);
            $entityManager->flush();

            $this->addFlash('success', 'Your photo is successfully deleted');

            return $this->redirectToRoute('app_produits_edit', ['id' => $produit->getId()]);
        } else {
            $this->addFlash('danger', 'Error happened while deleting the photo ');
            return $this->redirectToRoute('app.produits');
        }

//        $data = json_decode($request->getContent(), true);
//
//        if($this->isCsrfTokenValid("delete" . $photos->getId(), $data['_token']))
//        {
//            $photo_name = $photos->getName();
//
//            if($simpleUploadService->deleteImage($photo_name))
//            {
//                $entityManager->remove($photos);
//
//                $entityManager->flush();
//
//                $this->addFlash('success', 'Your photo is successfully deleted');
//                return new JsonResponse(['success' => 'Your photo is successfully deleted'], 200);
//            }
//        }
//        return new JsonResponse(['error' => 'Invalid Token'], 400);
    }
}
