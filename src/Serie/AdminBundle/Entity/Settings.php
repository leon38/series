<?php
namespace Serie\AdminBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Serie\SerieBundle\Entity\Serie;

/**
 * @ORM\Entity
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="Serie\AdminBundle\Entity\Repository\SettingsRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Settings
{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string")
     */
    protected $title;

    /**
     * @var string $domainName
     *
     * @ORM\Column(name="domainName", type="string")
     * @Assert\Url()
     */
    protected $domainName;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Settings
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set domainName
     *
     * @param string $domainName
     * @return Settings
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;
    
        return $this;
    }

    /**
     * Get domainName
     *
     * @return string 
     */
    public function getDomainName()
    {
        return $this->domainName;
    }
}