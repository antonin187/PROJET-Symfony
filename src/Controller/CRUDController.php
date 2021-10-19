<?php

namespace App\Controller;

use App\Entity\Society;
use App\Form\SearchSocietyType;
use App\Form\SocietyCreateType;
use App\Repository\CategoryRepository;
use App\Repository\SocietyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CRUDController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function adminSocieties(CategoryRepository $categoryRepository, SocietyRepository $societyRepository,
                                   Request $request)
    {
        $categories = $categoryRepository->findAll();
        $societies = $societyRepository->findAll();

//        FORMULAIRE DE RECHERCHE
        $searchSocietyForm = $this->createForm(SearchSocietyType::class);
        if ($searchSocietyForm->handleRequest($request)->isSubmitted() && $searchSocietyForm->isValid())
        {
            $criteria = $searchSocietyForm->getData();
//            dd($criteria);
            $societies = $societyRepository->findByName($criteria);
            if (count($societies) == 0)
            {
                $this->addFlash("notfound", "Votre recherche ne correspond à aucune société...");
                $societies = $societyRepository->findAll();
            }
            return $this->redirectToRoute('home.html.twig', [
                'categories' => $categories,
                'searchForm' => $searchSocietyForm->createView(),
                'societies' => $societies
            ]);
        }
        return $this->render("admin.html.twig", [
            'categories' => $categories,
            'societies' => $societies,
            'searchForm' => $searchSocietyForm->createView()
        ]);
    }

    /**
     * @Route("/createSociety/", name="create")
     */
    public function createSociety(EntityManagerInterface $en, CategoryRepository $categoryRepository, Request
                                  $request, SocietyRepository $societyRepository)
    {
        $newSociety = new Society();
        $categories = $categoryRepository->findAll();


        $form = $this->createForm(SocietyCreateType::class, $newSociety);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form['picture']->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $move = $imageFile->move(
                        $this->getParameter('profile_images'),
                        $newFilename
                    );
                    if (!$move){
                        throw new FileException('Erreur lors du chargement de l\'image');
                    }

                } catch (FileException $e) {
                    echo 'Erreur reçue : '.$e->getMessage();
                }

                $newSociety->setPicture($newFilename);
            }

            $en->persist($newSociety);
            $en->flush();
            return $this->redirectToRoute('home');
        }


//        FORMULAIRE DE RECHERCHE
        $societies =[];
        $searchSocietyForm = $this->createForm(SearchSocietyType::class);
        if ($searchSocietyForm->handleRequest($request)->isSubmitted() && $searchSocietyForm->isValid())
        {
            $criteria = $searchSocietyForm->getData();
//            dd($criteria);
            $societies = $societyRepository->findByName($criteria);
            if (count($societies) == 0)
            {
                $this->addFlash("notfound", "Votre recherche ne correspond à aucune société...");
                $societies = $societyRepository->findAll();
            }
            return $this->render('home.html.twig', [
                'categories' => $categories,
                'searchForm' => $searchSocietyForm->createView(),
                'societies' => $societies
            ]);
        }
        return $this->render('admin/createSociety.html.twig', [
            'formCreateSociety' => $form->createView(),
            'categories' => $categories,
            'searchForm' => $searchSocietyForm->createView(),
            'societies' => $societies,
        ]);
    }

    /**
     * @Route("/read/{id}", name="read")
     */
    public function readSociety($id, CategoryRepository $categoryRepository, SocietyRepository $societyRepository,
                                Request $request)
    {
        $categories = $categoryRepository->findAll();
        $society = $societyRepository->find($id);

//        FORMULAIRE DE RECHERCHE
        $societies =[];
        $searchSocietyForm = $this->createForm(SearchSocietyType::class);
        if ($searchSocietyForm->handleRequest($request)->isSubmitted() && $searchSocietyForm->isValid())
        {
            $criteria = $searchSocietyForm->getData();
//            dd($criteria);
            $societies = $societyRepository->findByName($criteria);
            if (count($societies) == 0)
            {
                $this->addFlash("notfound", "Votre recherche ne correspond à aucune société...");
                $societies = $societyRepository->findAll();
            }
            return $this->render('home.html.twig', [
                'categories' => $categories,
                'searchForm' => $searchSocietyForm->createView(),
                'societies' => $societies
            ]);
        }


        return $this->render("admin/readSociety.html.twig", [
            'categories' => $categories,
            'society' => $society,
            'searchForm' => $searchSocietyForm->createView(),
            'societies' => $societies,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteSociety($id, SocietyRepository $societyRepository, EntityManagerInterface $en) {

        $theSociety = $societyRepository->find($id);
        $en->remove($theSociety);
        $en->flush();

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/update/{id}", name="update")
     */
    public function updateSociety($id, CategoryRepository $categoryRepository, SocietyRepository $societyRepository,
                                  EntityManagerInterface $en, Request $request) {

        $categories = $categoryRepository->findAll();
        $society = $societyRepository->find($id);

        $form = $this->createForm(SocietyCreateType::class, $society);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form['picture']->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $move = $imageFile->move(
                        $this->getParameter('profile_images'),
                        $newFilename
                    );
                    if (!$move){
                        throw new FileException('Erreur lors du chargement de l\'image');
                    }

                } catch (FileException $e) {
                    echo 'Erreur reçue : '.$e->getMessage();
                }

                $society->setPicture($newFilename);
            }

            $en->persist($society);
            $en->flush();
            return $this->redirectToRoute('home');
        }

//        FORMULAIRE DE RECHERCHE
        $societies =[];
        $searchSocietyForm = $this->createForm(SearchSocietyType::class);
        if ($searchSocietyForm->handleRequest($request)->isSubmitted() && $searchSocietyForm->isValid())
        {
            $criteria = $searchSocietyForm->getData();
//            dd($criteria);
            $societies = $societyRepository->searchSociety($criteria);
            if (count($societies) == 0)
            {
                $this->addFlash("notfound", "Votre recherche ne correspond à aucune société...");
                $societies = $societyRepository->findAll();
            }
            return $this->render('home.html.twig', [
                'categories' => $categories,
                'searchForm' => $searchSocietyForm->createView(),
                'societies' => $societies
            ]);
        }

        return $this->render('admin/editSociety.html.twig', [
            'formUpdateSociety' => $form->createView(),
            'categories' => $categories,
            'society' => $society,
            'searchForm' => $searchSocietyForm->createView(),
            'societies' => $societies,
        ]);

    }
}