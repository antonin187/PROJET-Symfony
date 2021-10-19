<?php

namespace App\Controller;

use App\Entity\Society;
use App\Entity\Category;
use App\Form\SearchSocietyType;
use App\Form\SocietyCreateType;
use App\Repository\CategoryRepository;
use App\Repository\SocietyRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function homePage(CategoryRepository $categoryRepository, SocietyRepository $societyRepository,
Request $request)
    {
        $categories = $categoryRepository->findAll();
        $societies = $societyRepository->findAll();

//        FORMULAIRE DE RECHERCHE
        $searchSocietyForm = $this->createForm(SearchSocietyType::class);

        if ($searchSocietyForm->handleRequest($request)->isSubmitted() && $searchSocietyForm->isValid())
        {
            $criteria = $searchSocietyForm->getData();
            $societies = $societyRepository->findByName($criteria);
            if (count($societies) == 0)
            {
                $this->addFlash("notfound", "Votre recherche ne correspond à aucune société...");
                $societies = $societyRepository->findAll();
            }
        }

        return $this->render("home.html.twig", [
            'categories' => $categories,
            'searchForm' => $searchSocietyForm->createView(),
            'societies' => $societies
        ]);
    }



    /**
     * @Route("/category/{id}", name="category")
     */
    public function categoryPage($id, CategoryRepository $categoryRepository, SocietyRepository $societyRepository,
                                 Request $request)
    {
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->find($id);
        $societies = $societyRepository->findBy(['category'=>$id]);


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
            return $this->render('home.html.twig', [
                'categories' => $categories,
                'searchForm' => $searchSocietyForm->createView(),
                'societies' => $societies
            ]);
        }
        return $this->render("category.html.twig", [
            'categories' => $categories,
            'category' => $category,
            'searchForm' => $searchSocietyForm->createView(),
            'societies' => $societies,
        ]);
    }
    /**
     * @Route("/society/{id}", name="society")
     */
    public function societyPage($id, CategoryRepository $categoryRepository, SocietyRepository $societyRepository,
                                Request $request)
    {
        $categories = $categoryRepository->findAll();
        $society = $societyRepository->find($id);
        $societies=[];
//        FORMULAIRE DE RECHERCHE
        $searchSocietyForm = $this->createForm(SearchSocietyType::class);
        if ($searchSocietyForm->handleRequest($request)->isSubmitted() && $searchSocietyForm->isValid()) {
            $criteria = $searchSocietyForm->getData();
//            dd($criteria);
            $societies = $societyRepository->findByName($criteria);
            if (count($societies) == 0) {
                $this->addFlash("notfound", "Votre recherche ne correspond à aucune société...");
                $societies = $societyRepository->findAll();
            }
            return $this->render('home.html.twig', [
                'categories' => $categories,
                'searchForm' => $searchSocietyForm->createView(),
                'societies' => $societies
            ]);
        }
        return $this->render("society.html.twig", [
            'categories' => $categories,
            'society' => $society,
            'searchForm' => $searchSocietyForm->createView(),
            'societies' => $societies,
        ]);
        }


}
