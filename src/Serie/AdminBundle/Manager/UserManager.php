<?php
namespace Serie\AdminBundle\Manager;

use Serie\AdminBundle\Entity\User;

class UserManager {
	
	/**
	 * Envoi le mail avec le lien de confirmation à l'utilisateur
	 * @param  User 			$user 		Utilisateur dont on doit finaliser l'inscription
	 * @param  ServiceContainer	$container  Permet d'accéder à tous les services
	 * @return boolean
	 */
	public static function processSubscription($user, $container, $settings)
	{

		$link = $settings->getDomainName();
		$link .= 'signup/confirmation/';
		$link .= $user->getSalt();
		$message = \Swift_Message::newInstance()
        ->setSubject($settings->getTitle().' : subscription confirmation')
        ->setFrom('no-reply@netflix.com')
        ->setTo($user->getEmail())
        ->setBody(
            $container->renderView(
                '::newsletter.html.twig',
                array('link' => $link)
            ), 'text/html'
        );
    	$container->get('mailer')->send($message);
	}
}
