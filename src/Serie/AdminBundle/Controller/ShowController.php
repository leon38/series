<?php
namespace Serie\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Serie\SerieBundle\Entity\Serie;
use Serie\SerieBundle\Form\SerieType;

class ShowController extends Controller
{

	public $title = 'Shows';

	/**
	 * Affiche la page de l'admin
	 * @return array       tableau avec toutes les variables utiles
	 *
	 * @Route("/shows", name="shows")
	 * @Template()
	 */
    public function indexAction()
    {

    	$query = $this->getDoctrine()
    				  ->getRepository('SerieSerieBundle:Serie')
    				  ->getAllShowsQuery();

    	$i=0;			  
    	$columns[$i]['name'] = 'Title';
    	$columns[$i]['entity'] = 'title';
    	$i++;
    	$columns[$i]['name'] = 'Genres'; 
    	$columns[$i]['entity'] = 'genresToString'; 
    	$i++;
    	$columns[$i]['name'] = 'Format'; 
    	$columns[$i]['entity'] = 'formatEpisode';
    	$i++;
    	$columns[$i]['name'] = '# Seasons'; 
    	$columns[$i]['entity'] = 'nbSeasons';
    	$i++;
    	$columns[$i]['name'] = '# Episodes'; 
    	$columns[$i]['entity'] = 'nbEpisodes'; 
    	$i++;
    	$columns[$i]['name'] = 'Rating'; 			  
    	$columns[$i]['entity'] = 'rating';
    	$i++;
    	$columns[$i]['name'] = 'Preview'; 			  
    	$columns[$i]['entity'] = 'url';  			  

    	$nb_elem = 10;
    	$page = 1;

    	$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query,
				$page,
				$nb_elem
		);	


        return array('title' => $this->title,
        			 'columns' => $columns, 
        			 'datas' => $pagination, 
        			 'pagination' => $pagination, 
        			 'link_new' => 'newShow',
        			 'link_edit' => 'editShow', 
        			 'link_delete' => 'deleteShow'
        			 );
    }

    /**
     * Ajout une série
     * @param  int $id Identifiant de la série
     * @return array   tableau avec les variables
     *
     * @Route("/shows/new", name="newShow")
     * @Template("::edit.html.twig")
     */
    public function newAction(Request $request)
    {
    	$show = new Serie();
    	$form = $form = $this->createForm(new SerieType(), $show, array(
		    'action' => $this->generateUrl('newShow'),
		    'method' => 'POST',
		    'attr' => array('class' => 'form-horizontal', 'role' => 'form')));

    	if ($request->isMethod('POST')) {
	        $form->submit($request);

	        if ($form->isValid()) {
	        	$em = $this->getDoctrine()->getEntityManager();
	        	$em->persist($show);
	        	$em->flush();
	        	$this->get('session')->getFlashBag()->add(
		            'success',
		            'The new show\'s been saved' 
		        );
	        	if ($form->get('apply')->isClicked()) {
	        		return $this->redirect($this->generateUrl('editShow', array('id' => $show->getId())));
	        	}
	        	return $this->redirect($this->generateUrl('shows'));
			    
	        }
	    }
    	
    	return array('title' => 'New Show', 'form' => $form->createView()); 
    }

    /**
     * Edite une série
     * @param  int $id Identifiant de la série
     * @return array   tableau avec les variables
     *
     * @Route("/shows/edit/{id}", name="editShow")
     * @Template("::edit.html.twig")
     */
    public function editAction($id, Request $request)
    {
    	$show = $this->getDoctrine()->getRepository('SerieSerieBundle:Serie')->find($id);
    	$form = $form = $this->createForm(new SerieType(), $show, array(
		    'action' => $this->generateUrl('editShow', array('id' => $id)),
		    'method' => 'POST',
		    'attr' => array('class' => 'form-horizontal', 'role' => 'form')));

    	if ($request->isMethod('POST')) {
	        $form->submit($request);

	        if ($form->isValid()) {
	        	$em = $this->getDoctrine()->getEntityManager();
	        	$em->persist($show);
	        	$em->flush();
	        	$this->get('session')->getFlashBag()->add(
		            'success',
		            'The change\'s been saved' 
		        );
	        	if ($form->get('apply')->isClicked()) {
	        		return $this->redirect($this->generateUrl('editShow', array('id' => $show->getId())));
	        	}
	        	return $this->redirect($this->generateUrl('shows'));
			    
	        }
	    }
    	
    	return array('title' => 'Edit Show : '.$show->getTitle(), 'form' => $form->createView()); 
    }

    /**
     * Supprime une série
     * @param  int $id Identifiant de la serie
     * @return array   Tableau avec les variables
     *
     * @Route("/shows/delete/{id}", name="deleteShow")
     * @Template("AdminBundle:Show:index.html.twig")
     */
    public function deleteAction($id)
    {
    	$show = $this->getDoctrine()->getRepository('SerieSerieBundle:Serie')->find($id);
    	$em = $this->getDoctrine()->getEntityManager();
    	$em->remove($show);
    	$em->flush();
    	$this->get('session')->getFlashBag()->add(
		            'success',
		            'The show\'s been removed' 
		        );
    	return $this->redirect($this->generateUrl('shows'));
    }
}
