<?php
namespace Serie\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Serie\AdminBundle\Entity\User;
use Serie\AdminBundle\Entity\Role;


class LoadUsers implements FixtureInterface, ContainerAwareInterface
{

	/**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

	/**
	 * @{@inheritdoc}
	 */
	public function load(ObjectManager $manager)
	{
		$role = new Role();
		$role->setName('ROLE_ADMIN');
		

		$user = new User();
		$user->setUsername('admin');

		$factory = $this->container->get('security.encoder_factory');
		$encoder = $factory->getEncoder($user);
		$user->setSalt(md5(uniqid()));
		$password = $encoder->encodePassword('admin', $user->getSalt());
		$user->setPassword($password);

		$user->setFirstname('Damien');
		$user->setLastname('Corona');
		$user->setEmail('damien.Corona@aliceadsl.fr');
		$user->addRole($role);
		$user->setEnabled(1);
		$user->setLastLogin(new \DateTime('now'));
		$user->setAdded(new \DateTime('now'));

		$role->addUser($user);
		$manager->persist($user);
		$manager->persist($role);

		$manager->flush();
	}
}