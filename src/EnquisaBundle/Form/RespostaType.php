<?php

namespace EnquisaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RespostaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('enquisa', 'entity', array(
            'class' => 'EnquisaBundle\Entity\Enquisa',
        ) );

        $builder->add('opcion', 'entity', array(
            'class' => 'EnquisaBundle\Entity\Opcion',
        ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EnquisaBundle\Entity\Resposta'
        ));
    }
}
