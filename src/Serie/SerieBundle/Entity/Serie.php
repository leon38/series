<?php
namespace Serie\SerieBundle\Entity;

use Serie\AdminBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="serie")
 * @ORM\Entity(repositoryClass="Serie\SerieBundle\Entity\Repository\SerieRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Serie {
	
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
     * @var string $alias
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="alias", type="string", unique=true)
     */
    protected $alias;

    /**
     * @var int $nbSeasons
     *
     * @ORM\Column(name="nbSeasons", type="integer")
     */
    protected $nbSeasons;

    /**
     * @var int $nbEpisodes
     *
     * @ORM\Column(name="nbEpisodes", type="integer")
     */
    protected $nbEpisodes;

    /**
     * @var date $startDate
     *
     * @ORM\Column(name="startDate", type="date")
     */
    protected $startDate;

    /**
     * @var date $endDate
     *
     * @ORM\Column(name="endDate", type="date", nullable=true) 
     */
    protected $endDate;

    /**
     * @var string $channel
     *
     * @ORM\Column(name="channel", type="string")
     */
    protected $channel;

    /**
     * @var string nationality
     *
     * @ORM\Column(name="nationality", type="string")
     */
    protected $nationality;

    /**
     * @var string $formatEpisode
     *
     * @ORM\Column(name="formatEpisode", type="string")
     */
    protected $formatEpisode;

    /**
     * @var decimal $rating
     *
     * @ORM\Column(name="rating", type="decimal")
     */
    protected $rating;

    /**
     * @var text $synopsis
     *
     * @ORM\Column(name="synopsis", type="text")
     */
    protected $synopsis;

    /**
     * @var int $users
     *
     * @ORM\ManyToMany(targetEntity="Serie\AdminBundle\Entity\User", mappedBy="series")
     */
    protected $users;

    /**
     * @var array $images
     *
     * @ORM\Column(name="images", type="array")
     */
    protected $images;

    /**
     * @var array $feature
     *
     * @ORM\Column(name="feature", type="string")
     */
    protected $feature;

    /**
     * @var $actors
     *
     * @ORM\ManyToMany(targetEntity="Actor", inversedBy="series")
     */
    protected $actors;

    /**
     * @var $genres
     *
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="series")
     * @ORM\OrderBy({"title" = "ASC"})
     */
    protected $genres;

    /**
     * @var date $dateAdded
     *
     * @ORM\Column(name="dateAdded", type="date")
     */
    protected $dateAdded;

    /**
     * @var date $dateUpdated
     *
     * @ORM\Column(name="dateUpdated", type="date")
     */
    protected $dateUpdated; 

    /**
     * @var bool $onHome
     *
     * @ORM\Column(name="onHome", type="boolean")
     */
    protected $onHome;

    /**
     * @var string $bigFeature
     *
     * @ORM\Column(name="bigFeature", type="string", nullable=true)
     */
    protected $bigFeature;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @Assert\File(maxSize="100000000")
     */
    private $bigFile;

    private $temp;

    private $bigTemp;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rating = 0;
        $this->onHome = false;
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
        if (isset($this->feature)) {
            // store the old name to delete after the update
            $this->temp = $this->feature;
            $this->feature = null;
        } else {
            $this->feature = 'initial';
        }
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setBigFile(UploadedFile $file = null)
    {
        $this->bigFile = $file;
        // check if we have an old image path
        if (isset($this->bigFeature)) {
            // store the old name to delete after the update
            $this->temp = $this->bigFeature;
            $this->bigFeature = null;
        } else {
            $this->bigFeature = 'initial';
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

        if (null !== $this->getBigFile()) {
            // do whatever you want to generate a unique name
            $filename = $this->getAlias();
            $this->bigFeature = $filename.'-big.'.$this->getBigFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
         

        if (null !== $this->getFile()) {
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

        if (null !== $this->getBigFile()) {
            $this->getBigFile()->move($this->getUploadRootDir(), $this->bigFeature);

            // check if we have an old image
            if (isset($this->bigTemp) && $this->bigTemp != '' && $this->bigTemp != $this->bigFeature) {
                // delete the old image
                unlink($this->getUploadRootDir().'/'.$this->bigTemp);
                // clear the temp image path
                $this->temp = null;
            }
            $this->bigFile = null;
        }

    }




    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }

        if($bigFile = $this->getBigAbsolutePath()) {
            unlink($bigFile);
        }
    }


    public function getBigAbsolutePath()
    {
        return null === $this->bigFeature
            ? null
            : $this->getUploadRootDir().'/'.$this->bigFeature;
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

    public function getBigWebPath()
    {
        return null === $this->feature
            ? null
            : $this->getUploadDir().'/'.$this->bigFeature;
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
        return 'uploads/series';
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
     * Set title
     *
     * @param string $title
     * @return Serie
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
     * Set alias
     *
     * @param string $alias
     * @return Serie
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

    /**
     * Set nbSeasons
     *
     * @param integer $nbSeasons
     * @return Serie
     */
    public function setNbSeasons($nbSeasons)
    {
        $this->nbSeasons = $nbSeasons;
    
        return $this;
    }

    /**
     * Get nbSeasons
     *
     * @return integer 
     */
    public function getNbSeasons()
    {
        return $this->nbSeasons;
    }

    /**
     * Set nbEpisodes
     *
     * @param integer $nbEpisodes
     * @return Serie
     */
    public function setNbEpisodes($nbEpisodes)
    {
        $this->nbEpisodes = $nbEpisodes;
    
        return $this;
    }

    /**
     * Get nbEpisodes
     *
     * @return integer 
     */
    public function getNbEpisodes()
    {
        return $this->nbEpisodes;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Serie
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Serie
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    
        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        
        return $this->endDate;
    }

    /**
     * Set channel
     *
     * @param string $channel
     * @return Serie
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    
        return $this;
    }

    /**
     * Get channel
     *
     * @return string 
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     * @return Serie
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    
        return $this;
    }

    /**
     * Get nationality
     *
     * @return string 
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set formatEpisode
     *
     * @param string $formatEpisode
     * @return Serie
     */
    public function setFormatEpisode($formatEpisode)
    {
        $this->formatEpisode = $formatEpisode;
    
        return $this;
    }

    /**
     * Get formatEpisode
     *
     * @return string 
     */
    public function getFormatEpisode()
    {
        return $this->formatEpisode;
    }

    /**
     * Set rating
     *
     * @param float $rating
     * @return Serie
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    
        return $this;
    }

    /**
     * Get rating
     *
     * @return float 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set images
     *
     * @param array $images
     * @return Serie
     */
    public function setImages($images)
    {
        $this->images = $images;
    
        return $this;
    }

    /**
     * Get images
     *
     * @return array 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add users
     *
     * @param \Serie\SerieBundle\Entity\User $users
     * @return Serie
     */
    public function addUser(\Serie\SerieBundle\Entity\User $users)
    {
        $this->users[] = $users;
    
        return $this;
    }

    /**
     * Remove users
     *
     * @param \Serie\SerieBundle\Entity\User $users
     */
    public function removeUser(\Serie\SerieBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set synopsis
     *
     * @param string $synopsis
     * @return Serie
     */
    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;
    
        return $this;
    }

    /**
     * Get synopsis
     *
     * @return string 
     */
    public function getSynopsis()
    {
        return $this->synopsis;
    }

    /**
     * Add actors
     *
     * @param \Serie\SerieBundle\Entity\Actor $actors
     * @return Serie
     */
    public function addActor(\Serie\SerieBundle\Entity\Actor $actors)
    {
        $this->actors[] = $actors;
    
        return $this;
    }

    /**
     * Remove actors
     *
     * @param \Serie\SerieBundle\Entity\Actor $actors
     */
    public function removeActor(\Serie\SerieBundle\Entity\Actor $actors)
    {
        $this->actors->removeElement($actors);
    }

    /**
     * Get actors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActors()
    {
        return $this->actors;
    }

    /**
     * Set feature
     *
     * @param array $feature
     * @return Serie
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    
        return $this;
    }

    /**
     * Get feature
     *
     * @return array 
     */
    public function getFeature()
    {
        return $this->getWebPath();
    }

    /**
     * Add genres
     *
     * @param \Serie\SerieBundle\Entity\Genre $genres
     * @return Serie
     */
    public function addGenre(\Serie\SerieBundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;
    
        return $this;
    }

    /**
     * Remove genres
     *
     * @param \Serie\SerieBundle\Entity\Genre $genres
     */
    public function removeGenre(\Serie\SerieBundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGenres()
    {
        return $this->genres;
    }

    public function genresToString()
    {
        $genres = array();
        foreach ($this->genres as $genre) {
            $genres[] = $genre;
        }
        return implode(', ', $genres);
    }

    public function getGenresFront()
    {
        $genres = array();
        foreach ($this->genres as $genre) {
            $genres[] = '<a href="'.$genre->getAlias().'">'.$genre.'</a>';
        }
        return implode(' | ', $genres); 
    }

    public function setGenres($genres)
    {
        $this->genres = $genres;
 
        return $this;
    }

    public function getFile() {
        return $this->file;
    }

    public function getBigFile() {
        return $this->bigFile;
    }

    public function getUrl() {
        return '<a href="/serie/'.$this->alias.'" target="_blank">Preview</a>';
    }

    public function getStartDateToString()
    {
        return $this->startDate->format('m/d/Y');
    }

    public function getEndDateToString()
    {
        if ($this->endDate == new \DateTime('1893-01-01'))
            return '';
        return $this->endDate->format('m/d/Y');
    }

    public function getActorsLink()
    {
        $actors = array();
        foreach ($this->actors as $actor) {
            $actors[] = '<a href="/actor/'.$actor->getAlias().'">'.$actor->getNames().'</a>';
        }
        return implode(', ',$actors);
    }

    /**
     * @ORM\PrePersist
     */
    public function setDateAddedValue() 
    {
        $this->dateAdded = new \DateTime('now');
    }   

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setDateUpdatedValue() 
    {
        $this->dateUpdated = new \DateTime('now');
    }


    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return Serie
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    
        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime 
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     * @return Serie
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    
        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime 
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Set onHome
     *
     * @param boolean $onHome
     * @return Serie
     */
    public function setOnHome($onHome)
    {
        $this->onHome = $onHome;
    
        return $this;
    }

    /**
     * Get onHome
     *
     * @return boolean 
     */
    public function getOnHome()
    {
        return $this->onHome;
    }

    /**
     * Set bigFeature
     *
     * @param string $bigFeature
     * @return Serie
     */
    public function setBigFeature($bigFeature)
    {
        $this->bigFeature = $bigFeature;
    
        return $this;
    }

    /**
     * Get bigFeature
     *
     * @return string 
     */
    public function getBigFeature()
    {
        return $this->getBigWebPath();
    }
}