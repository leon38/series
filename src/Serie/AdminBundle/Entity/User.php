<?php
namespace Serie\AdminBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Serie\SerieBundle\Entity\Serie;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=40, nullable=false)
     */
    private $password;

    /**
     * @var string $firstname
     *
     * @ORM\Column(name="firstname", type="string", nullable=false)
     **/
    protected $firstname;

    /**
     * @var string $lastname
     *
     * @ORM\Column(name="lastname", type="string", nullable=false)
     **/
    protected $lastname;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", nullable=false)
     * @Assert\Email
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     **/
    public $avatar;

    /**
     * @var array $roles
     *
     * @ORM\ManyToMany(targetEntity="Role", mappedBy="users")
     */
    protected $roles;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @var \DateTime $lastLogin
     *
     * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    protected $lastLogin;

    /**
     * @var boolean $enabled
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    protected $enabled;

    /**
     * @var \DateTime $added
     *
     * @ORM\Column(name="added", type="datetime")
     * @Assert\Datetime()
     */
    protected $added;

    /**
     * @var int $series
     *
     * @ORM\ManyToMany(targetEntity="Serie\SerieBundle\Entity\Serie", inversedBy="users")
     * @ORM\JoinTable(name="users_series")
     */
    protected $series;


    private $temp;

    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->avatar)) {
            // store the old name to delete after the update
            $this->temp = $this->avatar;
            $this->avatar = null;
        } else {
            $this->avatar = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->avatar = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->avatar);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }


  	public function getAbsolutePath()
  	{
    	return null === $this->avatar
            ? null
            : $this->getUploadRootDir().'/'.$this->avatar;
    }

    public function getWebPath()
    {
        return null === $this->avatar
            ? null
            : $this->getUploadDir().'/'.$this->avatar;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/users';
    }

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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    
        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->getWebPath();
    }

    public function getRoles()
    {
        return $this->roles->toArray();
        
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getPassword()
    {
        return $this->password;
    }


    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    
    /**
     * Serializes the content of the current User object
     * @return string
     */
    public function serialize()
    {
        return \json_encode(
                array($this->username, $this->password, $this->salt,
                         $this->id));
    }

    /**
     * Unserializes the given string in the current User object
     * @param serialized
     */
    public function unserialize($serialized)
    {
        list($this->username, $this->password, $this->salt,
                         $this->id) = \json_decode(
                $serialized);
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set lastLogin
     *
     * @param \Datetime $lastLogin
     * @return User
     */
    public function setLastLogin(\Datetime $lastLogin)
    {
        $this->lastLogin = $lastLogin;
    
        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \Datetime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set added
     *
     * @param \Datetime $added
     * @return User
     */
    public function setAdded(\Datetime $added)
    {
        $this->added = $added;
    
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setAddedValue() 
    {
        $this->added = new \DateTime('now');
    }   

    /**
     * Get added
     *
     * @return \Datetime 
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Add roles
     *
     * @param \Serie\AdminBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\Serie\AdminBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;
    
        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Serie\AdminBundle\Entity\Role $roles
     */
    public function removeRole(\Serie\AdminBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Add series
     *
     * @param \Serie\SerieBundle\Entity\Serie $series
     * @return User
     */
    public function addSerie(\Serie\SerieBundle\Entity\Serie $series)
    {
        $this->series[] = $series;
    
        return $this;
    }

    /**
     * Remove series
     *
     * @param \Serie\SerieBundle\Entity\Serie $series
     */
    public function removeSerie(\Serie\SerieBundle\Entity\Serie $series)
    {
        $this->series->removeElement($series);
    }

    /**
     * Get series
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSeries()
    {
        return $this->series;
    }

    public function getAvatarImage() 
    {
        return $this->getWebPath();
    }
}