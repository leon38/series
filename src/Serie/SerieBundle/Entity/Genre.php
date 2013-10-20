<?php
namespace Serie\SerieBundle\Entity;

use Serie\AdminBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="genre")
 * @ORM\Entity(repositoryClass="Serie\SerieBundle\Entity\Repository\GenreRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Genre {
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
     * @var $series
     *
     * @ORM\ManyToMany(targetEntity="Serie", mappedBy="genres")
     **/
    protected $series;

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
     * Set title
     *
     * @param string $title
     * @return Genre
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
     * @return Genre
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
     * Add series
     *
     * @param \Serie\SerieBundle\Entity\Serie $series
     * @return Genre
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

    public function __toString()
    {
        return $this->title;
    }

    public function getUrl() {
        return '<a href="/genres/'.$this->alias.'>Preview</a>';
    }
}