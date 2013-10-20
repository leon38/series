<?php

namespace Serie\SerieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

	/**
	 * Affiche la page d'accueil
	 * @return array       tableau de variables
	 *
	 * @Route("home", name="home")
	 * @Template()
	 */
    public function indexAction()
    {

    	$shows = $this->getDoctrine()->getRepository('SerieSerieBundle:Serie')->getLastShows(10);

    	$showsHome = $this->getDoctrine()->getRepository('SerieSerieBundle:Serie')->findBy(array('onHome' => 1)); 

        return array('title' => 'Netflix', 'showsHome' => $showsHome, 'shows' => $shows);
    }

    /**
     * Affiche une serie
     * @param  string $alias Alias de la serie
     * @return array
     *
     * @Route("/serie/{alias}", name="serie")
     * @Template("SerieSerieBundle:Serie:serie.html.twig")
     */
    public function serieAction($alias)
    {
    	$show = $this->getDoctrine()->getRepository('SerieSerieBundle:Serie')->getSerieByAlias($alias);
    	return array('title' => $show->getTitle(), 'show' => $show);
    }
}
