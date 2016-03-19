<?php

namespace EnquisaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resposta
 *
 * @ORM\Table(name="resposta")
 * @ORM\Entity(repositoryClass="EnquisaBundle\Repository\RespostaRepository")
 */
class Resposta
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
     * @ORM\ManyToOne(targetEntity="Enquisa", inversedBy="respostas")
     * @ORM\JoinColumn(name="enquisa_id", referencedColumnName="id")
     */
    protected $enquisa;

    /**
     * @ORM\ManyToOne(targetEntity="Opcion", inversedBy="respostas")
     * @ORM\JoinColumn(name="opcion_id", referencedColumnName="id")
     */
    protected $opcion;


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
     * Set enquisa
     *
     * @param \EnquisaBundle\Entity\Enquisa $enquisa
     *
     * @return Resposta
     */
    public function setEnquisa(\EnquisaBundle\Entity\Enquisa $enquisa = null)
    {
        $this->enquisa = $enquisa;

        return $this;
    }

    /**
     * Get enquisa
     *
     * @return \EnquisaBundle\Entity\Enquisa
     */
    public function getEnquisa()
    {
        return $this->enquisa;
    }

    /**
     * Set opcion
     *
     * @param \EnquisaBundle\Entity\Opcion $opcion
     *
     * @return Resposta
     */
    public function setOpcion(\EnquisaBundle\Entity\Opcion $opcion = null)
    {
        $this->opcion = $opcion;

        return $this;
    }

    /**
     * Get opcion
     *
     * @return \EnquisaBundle\Entity\Opcion
     */
    public function getOpcion()
    {
        return $this->opcion;
    }
}
