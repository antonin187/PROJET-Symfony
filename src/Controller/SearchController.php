<?php

namespace App\Controller;

use App\Form\SearchSocietyType;
use App\Repository\CategoryRepository;
use App\Repository\SocietyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class SearchController extends AbstractController
{
    /**
     * @Route("/searchsociety", name="searchsoc")
     */
    public function searchSociety(Request $request, SocietyRepository $societyRepository, CategoryRepository
    $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        $searchSocietyForm = $this->createForm(SearchSocietyType::class);

        $societies=[];
        if ($searchSocietyForm->handleRequest($request)->isSubmitted() && $searchSocietyForm->isValid())
        {
            $criteria = $searchSocietyForm->getData();
//            dd($criteria);
            $societies = $societyRepository->searchSociety($criteria);
            if (count($societies) == 0)
            {
                $societies = $societyRepository->findAll();
            }
        } else {
            $societies=$societyRepository->findAll();
        }

        return $this->render('home.html.twig', [
            'categories' => $categories,
            'searchForm' => $searchSocietyForm->createView(),
            'societies' => $societies
        ]);
    }
}