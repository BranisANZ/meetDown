<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\RateConfUser;
use App\Form\AddRateConfType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class ConferenceController extends AbstractController
{

    /**
     * Liste les conférence que l'utilisateur à noté
     * @Route("/conference/voted", name="conference_voted")
     */
    public function conferenceVoted()
    {
        $confVoted = $this->getDoctrine()
            ->getRepository(RateConfUser::class)
            ->findBy(['user' => $this->getUser()]);
        dump($confVoted);
        $arrayConf = [];
        foreach ($confVoted as $value){
            $arrayConf[] = $value->getConference();
        }
        dump($arrayConf);
        $arrayConf = array_unique($arrayConf);
        return $this->render('home/index.html.twig', [
            'action' => 'ConferenceVoted',
            'conferences' => $arrayConf
        ]);
    }

    /**
     * Liste les conférence que l'utilisateur n'a pas noté
     * @Route("/conference/noVoted", name="conference_no_voted")
     */
    public function conferenceNoVoted()
    {
        $confVoted = $this->getDoctrine()
            ->getRepository(RateConfUser::class)
            ->findBy(['user' => $this->getUser()]);
        dump($confVoted);
        $arrayConfVotedByUser = [];
        foreach ($confVoted as $value){
            $arrayConfVotedByUser[] = $value->getConference();
        }
        dump($arrayConfVotedByUser);

        $arrayConfVotedByUser = array_unique($arrayConfVotedByUser);
        dump($arrayConfVotedByUser);
        $confVoted = $this->getDoctrine()
            ->getRepository(Conference::class)
            ->findAll();

        $arrayConfNoVotedByUser = [];
        // Parcours les conférences et vérifie si elles ont été voté par l'utilisateur
        foreach ($confVoted as $value){
            if (!in_array($value,$arrayConfVotedByUser)){
                $arrayConfNoVotedByUser[] = $value;
            }
        }

        dump($arrayConfNoVotedByUser);

        return $this->render('home/index.html.twig', [
            'action' => 'ConferenceNoVoted',
            'conferences' => $arrayConfNoVotedByUser
        ]);
    }

    /**
     * @Route("/conference/{id}", name="detail_conference",requirements={"id"="\d+"})
     */
    public function detailsConf(Request $request, $id)
    {
        $conference = $this->getDoctrine()
            ->getRepository(Conference::class)
            ->findOneBy(['id' => $id]);

        $rating = null;
        if($this->checkRate($id) == true){
            $rating = $this->getDoctrine()
                ->getRepository(RateConfUser::class)
                ->findAverageByConf($id);
        }
        dump($rating);

        $isVoted = false;
        if($this->isVoted($id) == true){
            $isVoted = true;
        }

        $rate = new RateConfUser();
        $rateForm = $this->createForm(AddRateConfType::class, $rate);
        $rateForm->handleRequest($request);
        if ($rateForm->isSubmitted() && $rateForm->isValid()) {
            $rate = $rateForm->getData();
            $this->addRate($rate,$conference);
            return $this->redirectToRoute('detail_conference', ['id' => $conference->getId()]);
        }

        return $this->render('conference/index.html.twig', [
            'controller_name' => 'ConferenceController',
            'conference' => $conference,
            'rating' => $rating,
            'isVoted' => $isVoted,
            'formRate' => $rateForm->createView()
        ]);
    }

    /**
     * @Route("/conference/searchBar", name="search_bar")
     */
    public function searchBarAjax()
    {
        $conference = $this->getDoctrine()
            ->getRepository(Conference::class)
            ->findAll();

        dump($conference);
        dump(json_encode($conference));

        return "";
        return new Response(json_encode($conference));

    }



    public function addRate($rate,$conference){
        $entityManager = $this->getDoctrine()->getManager();
        $rate->setConference($conference)
            ->setUser($this->getUser())
        ;
        $entityManager->persist($rate);
        $entityManager->flush();
        $this->addFlash("success", "Votre note à bien été prise en compte !");

    }

    public function isVoted($idConf){
        $entityManager = $this->getDoctrine()->getManager();
        $confVoted =$entityManager->getRepository(RateConfUser::class)
            ->findBy(['conference' => $idConf, 'user' => $this->getUser()])
        ;
        if (count($confVoted) > 0){
            return true;
        }
        return false;
    }

    public function checkRate($idConf){
        $entityManager = $this->getDoctrine()->getManager();
        $rate =$entityManager->getRepository(RateConfUser::class)
                            ->findBy(['conference' => $idConf])
        ;
        if(count($rate) > 0){
            return true;
        }
        return false;

    }
}
