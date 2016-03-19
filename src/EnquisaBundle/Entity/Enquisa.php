<?php

namespace EnquisaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enquisa
 *
 * @ORM\Table(name="enquisa")
 * @ORM\Entity(repositoryClass="EnquisaBundle\Repository\EnquisaRepository")
 */
class Enquisa
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
     * @ORM\Column(name="nome", type="string", length=255)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="ficheiro", type="string", length=255)
     */
    private $ficheiro;

    /**
     * @var bool
     *
     * @ORM\Column(name="procesada", type="boolean")
     */
    private $procesada;

    /**
     * @ORM\OneToMany(targetEntity="Resposta", mappedBy="enquisa")
     */
    protected $respostas;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurante", inversedBy="enquisas")
     * @ORM\JoinColumn(name="restaurante_id", referencedColumnName="id")
     */
    protected $restaurante;


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
     * @return Enquisa
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
     * Set ficheiro
     *
     * @param string $ficheiro
     *
     * @return Enquisa
     */
    public function setFicheiro($ficheiro)
    {
        $this->ficheiro = $ficheiro;

        return $this;
    }

    /**
     * Get ficheiro
     *
     * @return string
     */
    public function getFicheiro()
    {
        return $this->ficheiro;
    }

    /**
     * Set procesada
     *
     * @param boolean $procesada
     *
     * @return Enquisa
     */
    public function setProcesada($procesada)
    {
        $this->procesada = $procesada;

        return $this;
    }

    /**
     * Get procesada
     *
     * @return bool
     */
    public function getProcesada()
    {
        return $this->procesada;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->respostas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add resposta
     *
     * @param \EnquisaBundle\Entity\Resposta $resposta
     *
     * @return Enquisa
     */
    public function addResposta(\EnquisaBundle\Entity\Resposta $resposta)
    {
        $this->respostas[] = $resposta;

        return $this;
    }

    /**
     * Remove resposta
     *
     * @param \EnquisaBundle\Entity\Resposta $resposta
     */
    public function removeResposta(\EnquisaBundle\Entity\Resposta $resposta)
    {
        $this->respostas->removeElement($resposta);
    }

    /**
     * Get respostas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRespostas()
    {
        return $this->respostas;
    }

    /**
     * Set restaurante
     *
     * @param \EnquisaBundle\Entity\Restaurante $restaurante
     *
     * @return Enquisa
     */
    public function setRestaurante(\EnquisaBundle\Entity\Restaurante $restaurante = null)
    {
        $this->restaurante = $restaurante;

        return $this;
    }

    /**
     * Get restaurante
     *
     * @return \EnquisaBundle\Entity\Restaurante
     */
    public function getRestaurante()
    {
        return $this->restaurante;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getNome();
    }
}
