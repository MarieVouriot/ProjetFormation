<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Entity\Produit;
use App\Form\FormationType;
use Symfony\Component\HttpFoundation\Request;

class FormationController extends AbstractController
{
    /**
     * @Route("/formation", name="formation")
     */
    public function index()
    {
        return $this->render('formation/index.html.twig', [
            'controller_name' => 'FormationController',
        ]);
    }

    /**
    * @Route("/afficheLesFormations", name="affiche_formations")
    */
    public function afficheLesFormations()
    {
        $formations = $this->getDoctrine()->getRepository(Formation::class)->findall();
        if(!$formations){
            $message = "Pas de formation, revenez plus tard.";
        }
        else{
            $message = null;
        }
        return $this->render('formation/listeformation.html.twig',array('ensFormations'=>$formations, 'message'=>$message));
    }

     /**
     * @Route("/ajoutFormation", name="ajout_formation")
     */
    public function AjoutFormation(Request $request, $formation= null)
    {
        if($formation == null){
            $formation = new Formation();
        }
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($formation); // ajouter
                    $em->flush();
                    return $this->redirectToRoute('affiche_formations');
        }

        return $this->render('formation/editer.html.twig', array('form'=>$form->createView()));
    }

    /**
     * @Route("/suppFormation/{id}", name="supp_formation")
     */
    public function suppFormation($id){
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($formation); // supprimer
        $manager->flush();
        
        return $this->redirectToRoute('affiche_formations');
    }

}
