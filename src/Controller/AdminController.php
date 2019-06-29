<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\RateConfUser;
use App\Entity\User;
use App\Form\EditConferenceType;
use App\Form\EditUserType;
use App\Form\UserRegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin", name="admin__")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $topten = $em->getRepository(RateConfUser::class)->getTopTen();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'topTen' => $topten
        ]);
    }


    /**
     * @Route("/conference", name="conference")
     */
    public function showConférence(Request $request){
        $em = $this->getDoctrine()->getManager();
        $conference = new Conference();
        $conferenceForm = $this->createForm(EditConferenceType::class, $conference);
        $conferenceForm->handleRequest($request);
        if ($conferenceForm->isSubmitted() && $conferenceForm->isValid()) {
            $file = $conferenceForm->get('image')->getData();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('conferenceImg_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            $conference->setImage($fileName);

            $conference = $conferenceForm->getData();
            $em->persist($conference);
            $em->flush();
           /* $this->addFlash("success", "L'ajout à bien été prise en compte");
            return $this->redirectToRoute('admin__conference');*/
        }
        $conferences = $this->getDoctrine()
            ->getRepository(Conference::class)
            ->findAll();

        return $this->render('admin/conference/show.html.twig', [
            'controller_name' => 'AdminController',
            'conferences' => $conferences,
            'formConf' => $conferenceForm->createView()
        ]);

    }

    /**
     * @Route("/conference/edit/{id}", name="conference_edit")
     */
    public function editConférence(Request $request,Conference $conference){
        $em = $this->getDoctrine()->getManager();
        $conferenceForm = $this->createForm(EditConferenceType::class, $conference);
        $conferenceForm->handleRequest($request);
        if ($conferenceForm->isSubmitted() && $conferenceForm->isValid()) {
            $conference = $conferenceForm->getData();
            $em->persist($conference);
            $em->flush();
            $this->addFlash("success", "La modification à bien été prise en compte");
            return $this->redirectToRoute('admin__conference');
        }
        return $this->render('admin/conference/edit.html.twig', [
            'controller_name' => 'AdminController',
            'formConf' => $conferenceForm->createView()
        ]);
    }

    /**
     * @Route("/conference/del/{id}", name="conference_del")
     */
    public function delConference(Conference $conference){
        $em = $this->getDoctrine()->getManager();
        $em->remove($conference);
        $em->flush();
        $this->addFlash("success", "La conférence à bien été supprimé !");
        return $this->redirectToRoute('admin__conference');
    }


    /**
     * @Route("/user", name="user")
     */
    public function showUser(Request $request,UserPasswordEncoderInterface $encoder){
        $em = $this->getDoctrine()->getManager();
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();
        dump($users);
        $user = new User();
        $userForm = $this->createForm(EditUserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();
            $plainPassword = $user->getPassword();
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "L'utilisateur à bien été créée !");
            return $this->redirectToRoute('admin__user');
        }
        return $this->render('admin/user/show.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
            'formUser' => $userForm->createView()
        ]);
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit")
     */
    public function editUser(Request $request,User $user){
        $em = $this->getDoctrine()->getManager();
        $userForm = $this->createForm(EditUserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "La modification à bien été prise en compte");
            return $this->redirectToRoute('admin__user');
        }
        return $this->render('admin/user/edit.html.twig', [
            'controller_name' => 'AdminController',
            'formUser' => $userForm->createView()
        ]);
    }

    /**
     * @Route("/user/del/{id}", name="user_del")
     */
    public function delUser(User $user){
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        $this->addFlash("success", "L'utilisateur à bien été supprimé !");
        return $this->redirectToRoute('admin__user');
    }


    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }


}
