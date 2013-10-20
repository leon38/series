<?php
namespace Serie\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Serie\SerieBundle\Entity\Genre;
use Serie\SerieBundle\Form\GenreType;

class GenreController extends Controller
{
	/**
	 * Affiche la page de l'admin
	 * @return array       tableau avec toutes les variables utiles
	 *
	 * @Route("/genres", name="genres")
	 * @Template("::admin.html.twig")
	 */
    public function indexAction()
    {

    	$query = $this->getDoctrine()
    				  ->getRepository('SerieSerieBundle:Genre')
    				  ->getAllGenresQuery();

    	$i=0;			  
    	$columns[$i]['name'] = 'Title';
    	$columns[$i]['entity'] = 'title'; 
        $i++;
        $columns[$i]['name'] = 'Alias';
        $columns[$i]['entity'] = 'alias'; 			  

    	$nb_elem = 10;
    	$page = 1;

    	$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query,
				$page,
				$nb_elem
		);	


        return array('title' => 'Genres',
        			 'columns' => $columns, 
        			 'datas' => $pagination, 
        			 'pagination' => $pagination, 
        			 'link_new' => 'newGenre',
        			 'link_edit' => 'editGenre', 
        			 'link_delete' => 'deleteGenre'
        			 );
    }

    /**
     * Ajout une série
     * @param  int $id Identifiant de la série
     * @return array   tableau avec les variables
     *
     * @Route("/genres/new", name="newGenre")
     * @Template("::edit.html.twig")
     */
    public function newAction(Request $request)
    {
    	$show = new Genre();
    	$form = $form = $this->createForm(new GenreType(), $show, array(
		    'action' => $this->generateUrl('newGenre'),
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
		            'The new genre\'s been saved' 
		        );
	        	if ($form->get('apply')->isClicked()) {
	        		return $this->redirect($this->generateUrl('editGenre', array('id' => $show->getId())));
	        	}
	        	return $this->redirect($this->generateUrl('genres'));
			    
	        }
	    }
    	
    	return array('title' => 'New genre', 'form' => $form->createView()); 
    }

    /**
     * Edite une série
     * @param  int $id Identifiant de la série
     * @return array   tableau avec les variables
     *
     * @Route("/genres/edit/{id}", name="editGenre")
     * @Template("::edit.html.twig")
     */
    public function editAction($id, Request $request)
    {
    	$genre = $this->getDoctrine()->getRepository('SerieSerieBundle:Genre')->find($id);
    	$form = $form = $this->createForm(new GenreType(), $genre, array(
		    'action' => $this->generateUrl('editGenre', array('id' => $id)),
		    'method' => 'POST',
		    'attr' => array('class' => 'form-horizontal', 'role' => 'form')));

    	if ($request->isMethod('POST')) {
	        $form->submit($request);

	        if ($form->isValid()) {
	        	$em = $this->getDoctrine()->getEntityManager();
	        	$em->persist($genre);
	        	$em->flush();
	        	$this->get('session')->getFlashBag()->add(
		            'success',
		            'The changes\'s been saved' 
		        );
	        	if ($form->get('apply')->isClicked()) {
	        		return $this->redirect($this->generateUrl('editShow', array('id' => $genre->getId())));
	        	}
	        	return $this->redirect($this->generateUrl('genres'));
			    
	        }
	    }
    	
    	return array('title' => 'Edit '.$genre->getTitle(),'form' => $form->createView()); 
    }

    /**
     * Supprime une série
     * @param  int $id Identifiant de la serie
     * @return array   Tableau avec les variables
     *
     * @Route("/genres/delete/{id}", name="deleteGenre")
     * @Template("::admin.html.twig")
     */
    public function deleteAction($id)
    {
    	$genre = $this->getDoctrine()->getRepository('SerieSerieBundle:Genre')->find($id);
    	$em = $this->getDoctrine()->getEntityManager();
    	$em->remove($genre);
    	$em->flush();
    	$this->get('session')->getFlashBag()->add(
		            'success',
		            'The genre\'s been removed' 
		        );
    	return $this->redirect($this->generateUrl('genres'));
    }
}
