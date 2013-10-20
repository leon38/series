<?php
namespace Serie\SerieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="actor")
 * @ORM\Entity(repositoryClass="Serie\SerieBundle\Entity\Repository\ActorRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Actor {
	
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $firstname
     *
     * @ORM\Column(name="firstname", type="string")
     **/
    protected $firstname;

    /**
     * @var string $lastname
     *
     * @ORM\Column(name="lastname", type="string")
     **/
    protected $lastname;

    /**
     * @var string $alias
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="alias", type="string", unique=true)
     */
    protected $alias;

    /**
     * @var string $feature
     *
     * @ORM\Column(name="feature", type="string", nullable=true)
     */
    protected $feature;

    /**
     * @var date $birthdayDate
     *
     * @ORM\Column(name="birthdayDate", type="date", nullable=true)
     */
    protected $birthdayDate;

    /**
     * @var string $resume
     *
     * @ORM\Column(name="resume", type="text", nullable=true)
     */
    protected $resume;

    /**
     * @var $series
     *
     * @ORM\ManyToMany(targetEntity="Serie", mappedBy="actors")
     **/
    protected $series;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    private $temp;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->feature)) {
            // store the old name to delete after the update
            $this->temp = $this->feature;
            $this->feature = null;
        } else {
            $this->feature = 'initial';
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
            $filename = $this->getAlias();
            $this->feature = $filename.'.'.$this->getFile()->guessExtension();
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
        $this->getFile()->move($this->getUploadRootDir(), $this->feature);

        // check if we have an old image
        if (isset($this->temp) && $this->temp != '' && $this->temp != $this->feature) {
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
        return null === $this->feature
            ? null
            : $this->getUploadRootDir().'/'.$this->feature;
    }

    public function getWebPath()
    {
        return null === $this->feature
            ? null
            : $this->getUploadDir().'/'.$this->feature;
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
        return 'uploads/actors';
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->series = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set firstname
     *
     * @param string $firstname
     * @return Actor
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
     * @return Actor
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
     * Add series
     *
     * @param \Serie\SerieBundle\Entity\Serie $series
     * @return Actor
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

    /**
     * Set feature
     *
     * @param string $feature
     * @return Actor
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    
        return $this;
    }

    /**
     * Get feature
     *
     * @return string 
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * Set birthdayDate
     *
     * @param \DateTime $birthdayDate
     * @return Actor
     */
    public function setBirthdayDate($birthdayDate)
    {
        $this->birthdayDate = $birthdayDate;
    
        return $this;
    }

    /**
     * Get birthdayDate
     *
     * @return \DateTime 
     */
    public function getBirthdayDate()
    {
        return $this->birthdayDate;
    }

    /**
     * Get birthdayDate
     *
     * @return string 
     */
    public function getBirthdayDateToString()
    {
        return $this->birthdayDate->format('m/d/Y');
    }

    /**
     * Get birthdayDate
     *
     * @return string 
     */
    public function getNames()
    {
        return $this->firstname.' '.$this->lastname;
    }

    /**
     * Set resume
     *
     * @param string $resume
     * @return Actor
     */
    public function setResume($resume)
    {
        $this->resume = $resume;
    
        return $this;
    }

    /**
     * Get resume
     *
     * @return string 
     */
    public function getResume()
    {
        return $this->resume;
    }

    public function __toString()
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function getFile() {
        return $this->file;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Actor
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    
        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    public function getSeriesToString()
    {
        $series = array();
        foreach ($this->series as $serie) {
            $series[] = '<a href="/serie/'.$serie->getAlias().'" target="_blank">'.$serie->getTitle().'</a>';
        }
        return implode(', ', $series);
    }
}