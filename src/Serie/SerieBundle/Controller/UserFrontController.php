<?php
namespace Serie\SerieBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;

use Serie\AdminBundle\Entity\User;
use Serie\AdminBundle\Entity\Role;

use Serie\SerieBundle\Form\UserFrontType;
use Serie\AdminBundle\Manager\UserManager;


class UserFrontController extends Controller
{
	/**
	 * Ajoute un utilisateur dans la base
	 * @param Request $request
	 *
	 * @Route("/signup", name="newUserFront")
	 * @Template("SerieSerieBundle:User:signup.html.twig")
	 */
	public function addAction(Request $request)
	{
		$user = new User();
		$form = $this->createForm(new UserFrontType(), $user, array(
			'action' => $this->generateUrl('newUserFront'),
			'method' => 'POST',
			'attr' => array('class' => 'form-horizontal', 'role' => 'form')
		));

		if ($request->isMethod('POST')) {
	        $form->submit($request);

	        if ($form->isValid()) {
	        	$em = $this->getDoctrine()->getManager();
	        	$user->setEnabled(0);

	        	$factory = $this->get('security.encoder_factory');
				$encoder = $factory->getEncoder($user);
				$user->setSalt(md5(uniqid()));
				$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
				$user->setPassword($password);

	        	$role = $this->getDoctrine()->getRepository('SerieAdminBundle:Role')->findBy(array('name' => 'ROLE_USER'));
	        	$role = current($role);
	        	$user->addRole($role);
	        	$em->persist($user);

	        	$role->addUser($user);
	        	$em->flush();

	        	UserManager::processSubscription($user,$this, $this->getDoctrine()->getRepository('SerieAdminBundle:Settings')->find(1));

	        	return array('title' => 'Thank you', 'message' => 'Thank you for registering to Netflix.<br />You will receive an email to confirm your registration.');
	        }
	    }
	    return array('title' => 'Join Netflix today', 'form' => $form->createView());
	}

	/**
	 * Confirme l'inscription d'un utilisateur
	 * @param  String $key Clé pour connaître l'utilisateur
	 * @return Template      
	 *
	 * @Route("/signup/confirmation/{key}", name="confirmRegistration")
	 * @Template("SerieSerieBundle:User:confirm.html.twig")
	 */
	public function confirmAction($key)
	{
		$user = $this->getDoctrine()->getRepository('SerieAdminBundle:User')->findBy(array('salt' => $key));
		if(is_array($user)) {
			$user = current($user);
			if(!is_null($user)) {
				$user->setEnabled(1);
			}
		}
		return array('titre' => 'Confirm registraton', 'message' => 'Your account is now enabled.<br />You can now sign in <a href="/login">here</a>');

	}


	/**
	 * Met à jour le profil d'un utilisateur
	 * @return array form
	 *
	 * @Route("/user/profile", name="profile")
	 * @Template("SerieSerieBundle:User:signup.html.twig")
	 */
	public function profilAction(Request $request) 
	{
		if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
        	throw new AccessDeniedException();
    	}

		$user = $this->get('security.context')->getToken()->getUser();
		$form = $this->createForm(new UserFrontType(), $user, array(
			'action' => $this->generateUrl('profile'),
			'method' => 'POST',
			'attr' => array('class' => 'form-horizontal', 'role' => 'form'),
			'profile' => true
		));
		if ($request->isMethod('POST')) {
	        $form->submit($request);

	        if ($form->isValid()) {
	        	$em = $this->getDoctrine()->getManager();
	        	$em->persist($user);
	        	$em->flush();
	        	$session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('success', 'Your profile has been updated.');
	        }
	    }
	    return array('title' => 'Update your profile', 'form' => $form->createView());
	}


	/**
     * @Route("/user/login", name="loginFront")
     * @Template("SerieSerieBundle:User:login.html.twig")
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'SerieSerieBundle:User:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                'title'			=> 'Login in Netflix'
            )
        );
    }

    /**
     * @Route("/user/login_check", name="front_login_check")
     *
     */
    public function loginCheckAction()
    {
        return $this->redirect($this->generateUrl('dashboard'));
    }

    /**
     * @Route("/user/logout", name="front_logout")
     *
     */
    public function logoutAction()
    {
        return array('test' => null);
    }


}