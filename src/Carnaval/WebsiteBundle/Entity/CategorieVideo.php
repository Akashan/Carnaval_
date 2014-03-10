<?php

namespace Carnaval\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategorieVideo
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Carnaval\WebsiteBundle\Entity\CategorieVideoRepository")
 */
class CategorieVideo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Carnaval\WebsiteBundle\Entity\Video", mappedBy="videos")
     */
    private $videos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        // Rappelez-vous, on a un attribut qui doit contenir un ArrayCollection, on doit l'initialiser dans le constructeur
        $this->videos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return CategorieVideo
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return CategorieVideo
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }


    /**
     * Add liens
     *
     * @param \Carnaval\WebsiteBundle\Entity\Video $videos
     * @return CategorieVideo
     */
    public function addVideo(\Carnaval\WebsiteBundle\Entity\Video $video)
    {
        $this->videos[] = $video;

        return $this;
    }

    /**
     * Remove liens
     *
     * @param \Carnaval\WebsiteBundle\Entity\Video $videos
     */
    public function removeVideo(\Carnaval\WebsiteBundle\Entity\Video $video)
    {
        $this->videos->removeElement($video);
    }

    /**
     * Get liens
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }

    public function setVideos($videos)
    {
        return $this->videos = $videos;
    }

}
