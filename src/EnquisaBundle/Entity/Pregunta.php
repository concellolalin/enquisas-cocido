<?php

namespace EnquisaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pregunta
 *
 * @ORM\Table(name="pregunta")
 * @ORM\Entity(repositoryClass="EnquisaBundle\Repository\PreguntaRepository")
 */
class Pregunta
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
     * @ORM\Column(name="texto", type="string", length=255)
     */
    private $texto;

    /**
     * @var int
     *
     * @ORM\Column(name="orde", type="integer")
     */
    private $orde;

    /**
     * @ORM\OneToMany(targetEntity="Opcion", mappedBy="pregunta")
     */
    protected $opcions;

    /**
     * Pregunta constructor.
     */
    public function __construct()
    {
        $this->opcions = new ArrayCollection();
    }

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
     * Set texto
     *
     * @param string $texto
     *
     * @return Pregunta
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto
     *
     * @return string
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set orde
     *
     * @param integer $orde
     *
     * @return Pregunta
     */
    public function setOrde($orde)
    {
        $this->orde = $orde;

        return $this;
    }

    /**
     * Get orde
     *
     * @return int
     */
    public function getOrde()
    {
        return $this->orde;
    }

    /**
     * Add opcion
     *
     * @param \EnquisaBundle\Entity\Opcion $opcion
     *
     * @return Pregunta
     */
    public function addOpcion(\EnquisaBundle\Entity\Opcion $opcion)
    {
        $this->opcions[] = $opcion;

        return $this;
    }

    /**
     * Remove opcion
     *
     * @param \EnquisaBundle\Entity\Opcion $opcion
     */
    public function removeOpcion(\EnquisaBundle\Entity\Opcion $opcion)
    {
        $this->opcions->removeElement($opcion);
    }

    /**
     * Get opcions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpcions()
    {
        return $this->opcions;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getTexto();
    }
}
