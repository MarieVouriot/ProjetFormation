<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Entity\Produit;
use App\Entity\Inscription;
use App\Entity\Employe;
use App\Form\FormationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function index()
    {
        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }

    /**
    * @Route("/afficheLesFormationsInscription", name="affiche_formations_inscription")
    */
    public function AfficheLesFormationsInscription()
    {
        $formations = $this->getDoctrine()->getRepository(Formation::class)->findFormationDepartement();
        if(!$formations){
            $message = "Pas de formation, revenez plus tard.";
        }
        else{
            $message = null;
        }
        return $this->render('inscription/listeFormation.html.twig',array('ensFormations'=>$formations, 'message'=>$message));       
        
    }

    /**
    * @Route("/afficheLesInscriptions", name="affiche_inscription")
    */
    public function AfficheLesInscriptions()
    {
        $inscriptions = $this->getDoctrine()->getRepository(Inscription ::class)->findInscritpionAttente();
        if(!$inscriptions){
            $message = "Pas d'inscription";
        }
        else{
            $message = null;
        }
        return $this->render('inscription/listeInscription.html.twig',array('ensInscriptions'=>$inscriptions, 'message'=>$message));       
        
    }

    /**
    * @Route("/accepterInscription/{id}", name="accepter_inscription")
    */
    public function AccepterInscription($id)
    {
        // statut 0 = en attente
        // statut 1 = valider
        // statut 2 = refuser
        $inscription = $this->getDoctrine()->getRepository(Inscription ::class)->find($id);
        $inscription->setStatut(1);
        $em = $this->getDoctrine()->getManager();
        $em->flush(); // rafraichir

        return $this->redirectToRoute('affiche_inscription');
    }

    /**
    * @Route("/refuserInscription/{id}", name="refuser_inscription")
    */
    public function RefuserInscription($id)
    {
        // statut 0 = en attente
        // statut 1 = valider
        // statut 2 = refuser
        $inscription = $this->getDoctrine()->getRepository(Inscription ::class)->find($id);
        $inscription->setStatut(2);
        $em = $this->getDoctrine()->getManager();
        $em->flush(); // rafraichir

        return $this->redirectToRoute('affiche_inscription');
    }

    /**
    * @Route("/creerInscription", name="create_inscription")
    */
    public function CreerInscription()
    {
        // Castaing -- enlever l'id de creerInscription + récuperer l'id de la formation depuis verif_inscription
        $formationId = $this->get('session')->get('formationId');
        $formation = $this->getDoctrine()->getRepository(Formation ::class)->find($formationId);


        $employeId = $this->get('session')->get('employeId');
        $employe = $this->getDoctrine()->getRepository(Employe ::class)->find($employeId);

        $inscription = new Inscription();
        $inscription->setFormation($formation);
        $inscription->setEmploye($employe);
        $inscription->setStatut(0);

        $em = $this->getDoctrine()->getManager();
        $em->persist($inscription);
        $em->flush();

        return $this->redirectToRoute('affiche_formations_inscription');
    }

    // Castaing -- création de la fonction verificationInscription

    /**
    * @Route("/verificationInscription/{id}", name="verif_inscription")
    */
    public function VerificationInscription($id)
    {

        $formation = $this->getDoctrine()->getRepository(Formation ::class)->find($id);
        $id = $formation;
        $session = new Session();
        $session->set('formationId', $id);

        $employeId = $this->get('session')->get('employeId');
        $employe = $this->getDoctrine()->getRepository(Employe ::class)->find($employeId);

        if($employe->getId() == $employeId && $formation->getId() == 4){

            return $this->redirectToRoute('create_inscription');
        }
        else{
            
            return $this->redirectToRoute('affiche_formations_inscription');
        }

        
    }
  
}
