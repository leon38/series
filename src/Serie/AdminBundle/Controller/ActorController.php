<?php
namespace Serie\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Serie\SerieBundle\Entity\Actor;
use Serie\SerieBundle\Form\ActorType;

class ActorController extends Controller
{
	/**
	 * Affiche la page de l'admin
	 * @return array       tableau avec toutes les variables utiles
	 *
	 * @Route("/actors/{page}", name="actors", defaults={"page": 1})
	 * @Template("::admin.html.twig")
	 */
    public function indexAction($page)
    {

    	$query = $this->getDoctrine()
    				  ->getRepository('SerieSerieBundle:Actor')
    				  ->getAllActorsQuery();

    	$i=0;			  
    	$columns[$i]['name'] = 'Name';
    	$columns[$i]['entity'] = 'names'; 
        $i++;
        $columns[$i]['name'] = 'Birthday';
        $columns[$i]['entity'] = 'birthdayDateToString'; 			  
        $i++;
        $columns[$i]['name'] = 'Shows';
        $columns[$i]['entity'] = 'seriesToString';

    	$nb_elem = 10;

    	$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query,
				$page,
				$nb_elem
		);	


        return array('title' => 'Actors',
        			 'columns' => $columns, 
        			 'datas' => $pagination, 
        			 'pagination' => $pagination, 
        			 'link_new' => 'newActor',
        			 'link_edit' => 'editActor', 
        			 'link_delete' => 'deleteActor'
        			 );
    }

    /**
     * Ajout une série
     * @param  int $id Identifiant de la série
     * @return array   tableau avec les variables
     *
     * @Route("/actor/new", name="newActor")
     * @Template("::edit.html.twig")
     */
    public function newAction(Request $request)
    {
    	$actor = new Actor();
    	$form = $this->createForm(new ActorType(), $actor, array(
		    'action' => $this->generateUrl('newActor'),
		    'method' => 'POST',
		    'attr' => array('class' => 'form-horizontal', 'role' => 'form')));

    	if ($request->isMethod('POST')) {
	        $form->submit($request);

	        if ($form->isValid()) {
	        	$em = $this->getDoctrine()->getEntityManager();
	        	$em->persist($actor);
	        	$em->flush();
	        	$this->get('session')->getFlashBag()->add(
		            'success',
		            'The new Actor\'s been saved' 
		        );
	        	if ($form->get('apply')->isClicked()) {
	        		return $this->redirect($this->generateUrl('editActor', array('id' => $actor->getId())));
	        	}
	        	return $this->redirect($this->generateUrl('actors'));
			    
	        }
	    }
    	
    	return array('title' => 'New Actor', 'form' => $form->createView()); 
    }

    /**
     * Edite une série
     * @param  int $id Identifiant de la série
     * @return array   tableau avec les variables
     *
     * @Route("/actor/edit/{id}", name="editActor")
     * @Template("::edit.html.twig")
     */
    public function editAction($id, Request $request)
    {
    	$actor = $this->getDoctrine()->getRepository('SerieSerieBundle:Actor')->find($id);
    	$form = $form = $this->createForm(new ActorType(), $actor, array(
		    'action' => $this->generateUrl('editActor', array('id' => $id)),
		    'method' => 'POST',
		    'attr' => array('class' => 'form-horizontal', 'role' => 'form')));

    	if ($request->isMethod('POST')) {
	        $form->submit($request);

	        if ($form->isValid()) {
	        	$em = $this->getDoctrine()->getEntityManager();
	        	$em->persist($actor);
	        	$em->flush();
	        	$this->get('session')->getFlashBag()->add(
		            'success',
		            'The changes\'s been saved' 
		        );
	        	if ($form->get('apply')->isClicked()) {
	        		return $this->redirect($this->generateUrl('editShow', array('id' => $actor->getId())));
	        	}
	        	return $this->redirect($this->generateUrl('actors'));
			    
	        }
	    }
    	
    	return array('title' => 'Edit Show : '.$actor->getFirstname().' '.$actor->getLastname(), 'form' => $form->createView()); 
    }

    /**
     * Supprime une série
     * @param  int $id Identifiant de la serie
     * @return array   Tableau avec les variables
     *
     * @Route("/actor/delete/{id}", name="deleteActor")
     * @Template("::admin.html.twig")
     */
    public function deleteAction($id)
    {
    	$actor = $this->getDoctrine()->getRepository('SerieSerieBundle:Actor')->find($id);
    	$em = $this->getDoctrine()->getEntityManager();
    	$em->remove($actor);
    	$em->flush();
    	$this->get('session')->getFlashBag()->add(
		            'success',
		            'The actor\'s been removed' 
		        );
    	return $this->redirect($this->generateUrl('actors'));
    }
}
