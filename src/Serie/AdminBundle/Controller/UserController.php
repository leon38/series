<?php
namespace Serie\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Serie\AdminBundle\Entity\User;
use Serie\AdminBundle\Entity\Role;

class UserController extends Controller
{
	/**
	 * Affiche la page de l'admin
	 * @return array       tableau avec toutes les variables utiles
	 *
	 * @Route("/admin/dashboard")
	 */
    public function indexAction()
    {

        return array();
    }

    /**
     * Create user
     * @return array
     *
     * @Route("/user/create", name="createUser")
     */
    public function createUserAction() 
    {
    	$role = $this->getDoctrine()->getRepository('SerieAdminBundle:Role')->find(4);

    	$user = new User();
		$user->setUsername('admin');

		$factory = $this->container->get('security.encoder_factory');
		$encoder = $factory->getEncoder($user);
		$user->setSalt(md5(uniqid()));
		$password = $encoder->encodePassword('admin', $user->getSalt());
		$user->setPassword($password);

		$user->setFirstname('Damien');
		$user->setLastname('Corona');
		$user->setEmail('damien.corona@aliceadsl.fr');
		$user->addRole($role);
		$user->setEnabled(1);
		$user->setLastLogin(new \DateTime('now'));
		$user->setAdded(new \DateTime('now'));

		$manager = $this->getDoctrine()->getEntityManager();
		$manager->persist($user);

		$role->addUser($user);
		$manager->flush();
		return array();
    }
}
