<?php

namespace Carnaval\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Video
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Carnaval\WebsiteBundle\Entity\VideoRepository")
 */
class Video
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
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="codeExterne", type="string", length=255)
     */
    private $codeExterne;

    /**
     * @var string
     *
     * @ORM\Column(name="miniature", type="string", length=255)
     */
    private $miniature;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity="Carnaval\WebsiteBundle\Entity\CategorieVideo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

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
     * @return Video
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
     * Set description
     *
     * @param string $description
     * @return Video
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set codeExterne
     *
     * @param string $codeExterne
     * @return Video
     */
    public function setCodeExterne($codeExterne)
    {
        $this->codeExterne = $codeExterne;
    
        return $this;
    }

    /**
     * Get codeExterne
     *
     * @return string 
     */
    public function getCodeExterne()
    {
        return $this->codeExterne;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Video
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
     * Set categorie
     *
     * @param \Carnaval\WebsiteBundle\Entity\CategorieVideo $categorie
     * @return Liens
     */
    public function setCategorie(\Carnaval\WebsiteBundle\Entity\CategorieVideo $categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \Carnaval\WebsiteBundle\Entity\CategorieVideo
     */
    public function getCategorie()
    {
        return $this->categorie;
    }


    /**
     * Set miniature
     *
     * @param string $miniature
     * @return Video
     */
    public function setMiniature($miniature)
    {
        $this->miniature = $miniature;
    
        return $this;
    }

    /**
     * Get miniature
     *
     * @return string 
     */
    public function getMiniature()
    {
        return $this->miniature;
    }
}