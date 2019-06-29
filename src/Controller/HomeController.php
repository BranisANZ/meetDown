<?php

namespace App\Controller;

use App\Entity\Conference;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $allConf = $this->getDoctrine()
            ->getRepository(Conference::class)
            ->findSixLastConf();
        dump($allConf);
        return $this->render('home/index.html.twig', [
            'action' => 'HomeController',
            'conferences' => $allConf
        ]);
    }

    /**
     * @Route("/recherche", name="search_conference")
     */
    public function searchBar(Request $request){
        if ($request->getMethod() == "GET"){
            $data = $request->query->get("search");
            $searchConf = $this->getDoctrine()
                ->getRepository(Conference::class)
                ->findByLike($data);
        }

        return $this->render('home/index.html.twig', [
            'action' => 'SearchBar',
            'conferences' => $searchConf
        ]);
    }




}
