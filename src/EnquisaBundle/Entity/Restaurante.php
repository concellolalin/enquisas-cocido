<?php

namespace EnquisaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Restaurante
 *
 * @ORM\Table(name="restaurante")
 * @ORM\Entity(repositoryClass="EnquisaBundle\Repository\RestauranteRepository")
 */
class Restaurante
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=255, unique=true)
     */
    private $nome;

    /**
     * @ORM\OneToMany(targetEntity="Enquisa", mappedBy="restaurante")
     */
    protected $enquisas;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nome
     *
     * @param string $nome
     *
     * @return Restaurante
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enquisas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add enquisa
     *
     * @param \EnquisaBundle\Entity\Enquisa $enquisa
     *
     * @return Restaurante
     */
    public function addEnquisa(\EnquisaBundle\Entity\Enquisa $enquisa)
    {
        $this->enquisas[] = $enquisa;

        return $this;
    }

    /**
     * Remove enquisa
     *
     * @param \EnquisaBundle\Entity\Enquisa $enquisa
     */
    public function removeEnquisa(\EnquisaBundle\Entity\Enquisa $enquisa)
    {
        $this->enquisas->removeElement($enquisa);
    }

    /**
     * Get enquisas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnquisas()
    {
        return $this->enquisas;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getNome();
    }
}
