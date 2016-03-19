<?php

namespace EnquisaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Opcion
 *
 * @ORM\Table(name="opcion")
 * @ORM\Entity(repositoryClass="EnquisaBundle\Repository\OpcionRepository")
 */
class Opcion
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
     * @ORM\Column(name="valor", type="string", length=255)
     */
    private $valor;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer")
     */
    private $height;

    /**
     * @var int
     *
     * @ORM\Column(name="x", type="integer")
     */
    private $x;

    /**
     * @var int
     *
     * @ORM\Column(name="y", type="integer")
     */
    private $y;

    /**
     * @ORM\ManyToOne(targetEntity="Pregunta", inversedBy="opcions")
     * @ORM\JoinColumn(name="pregunta_id", referencedColumnName="id")
     */
    protected $pregunta;

    /**
     * @ORM\OneToMany(targetEntity="Resposta", mappedBy="opcion")
     */
    protected $respostas;


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
     * Set valor
     *
     * @param string $valor
     *
     * @return Opcion
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return Opcion
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return Opcion
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set x
     *
     * @param integer $x
     *
     * @return Opcion
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * Get x
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Set y
     *
     * @param integer $y
     *
     * @return Opcion
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * Get y
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set pregunta
     *
     * @param \EnquisaBundle\Entity\Pregunta $pregunta
     *
     * @return Opcion
     */
    public function setPregunta(\EnquisaBundle\Entity\Pregunta $pregunta = null)
    {
        $this->pregunta = $pregunta;

        return $this;
    }

    /**
     * Get pregunta
     *
     * @return \EnquisaBundle\Entity\Pregunta
     */
    public function getPregunta()
    {
        return $this->pregunta;
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
     * @return Opcion
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
     * @return string
     */
    public function __toString() {
        return $this->pregunta->getTexto() . ' - ' . $this->getValor();
    }
}
