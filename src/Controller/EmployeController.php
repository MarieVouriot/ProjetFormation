<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Employe;
use App\Form\EmployeType;
use App\Form\EmployeLoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class EmployeController extends AbstractController
{
    /**
     * @Route("/employe", name="employe")
     */
    public function index()
    {
        return $this->render('employe/index.html.twig', [
            'controller_name' => 'EmployeController',
        ]);
    }

    /**
     * @Route("/ajoutEmploye", name="ajout_employe")
     */
    public function AjoutEmploye(Request $request, $employe= null)
    {
        if($employe == null){
            $employe = new Employe();
        }
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($employe);
                    $em->flush();
                    return $this->redirectToRoute('connexion_employe');
        }

        return $this->render('employe/editer.html.twig', array('form'=>$form->createView()));
    }

    /**
    * @Route("/connexion", name="connexion_employe")
    */
    public function Connexion(Request $request, $employe= null)
    {        
        $form = $this->createForm(EmployeLoginType::class, $employe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $login = $form->get('login')->getData();
            $mdp = $form->get('mdp')->getData();
            $employe = $this->getDoctrine()->getRepository(Employe::class)->ConnexionEmploye($login, $mdp);
            
            if($employe == null)
            {
                return $this->redirectToRoute('ajout_employe');
            }
            else
            {
                $id = $employe->getId();
                $session = new Session();
                $session->set('employeId', $id);
                // 2 = admin
                if($employe->getStatut() == 2){
                    return $this->redirectToRoute('affiche_formations');
                }
                else
                {
                    return $this->redirectToRoute('affiche_formations_inscription');
                }
            }
            
        }

        return $this->render('employe/editer2.html.twig', array('form'=>$form->createView()));
        
    }

}
