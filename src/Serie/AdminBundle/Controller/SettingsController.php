<?php
namespace Serie\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Serie\AdminBundle\Entity\Settings;
use Serie\AdminBundle\Form\SettingsType;

class SettingsController extends Controller
{

	public $title = 'Settings';

	/**
	 * Affiche la page de l'admin
	 * @return array       tableau avec toutes les variables utiles
	 *
	 * @Route("/settings", name="settings")
	 * @Template("::admin.html.twig")
	 */
    public function indexAction()
    {

    	$query = $this->getDoctrine()
    				  ->getRepository('SerieAdminBundle:Settings')
    				  ->getAllSettingsQuery();

    	$i=0;			  
    	$columns[$i]['name'] = 'Title';
    	$columns[$i]['entity'] = 'title';
    	$i++;
    	$columns[$i]['name'] = 'URL'; 
    	$columns[$i]['entity'] = 'domainName'; 
    	  			  

    	$nb_elem = 10;
    	$page = 1;

    	$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query,
				$page,
				$nb_elem
		);	



        if(count($pagination) > 0) {
            return array('title' => $this->title,
            			 'columns' => $columns, 
            			 'datas' => $pagination, 
            			 'pagination' => $pagination
            			 );
        } else {
            return array('title' => $this->title,
                         'columns' => $columns, 
                         'datas' => $pagination, 
                         'pagination' => $pagination,
                         'link_new' => 'newSettings'
                         );
        }
    }

    /**
     * Ajout une série
     * @param  int $id Identifiant de la série
     * @return array   tableau avec les variables
     *
     * @Route("/settings/new", name="newSettings")
     * @Template("::edit.html.twig")
     */
    public function newAction(Request $request)
    {
    	$settings = new Settings();
    	$form = $form = $this->createForm(new SettingsType(), $settings, array(
		    'action' => $this->generateUrl('newSettings'),
		    'method' => 'POST',
		    'attr' => array('class' => 'form-horizontal', 'role' => 'form')));

    	if ($request->isMethod('POST')) {
	        $form->submit($request);

	        if ($form->isValid()) {
	        	$em = $this->getDoctrine()->getEntityManager();
	        	$em->persist($settings);
	        	$em->flush();
	        	$this->get('session')->getFlashBag()->add(
		            'success',
		            'The new settings\'s been saved' 
		        );
	        	if ($form->get('apply')->isClicked()) {
	        		return $this->redirect($this->generateUrl('editSettings', array('id' => $settings->getId())));
	        	}
	        	return $this->redirect($this->generateUrl('settings'));
			    
	        }
	    }
    	
    	return array('title' => 'New Settings', 'form' => $form->createView()); 
    }

}
