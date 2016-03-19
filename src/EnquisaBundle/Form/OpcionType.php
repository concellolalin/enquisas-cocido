<?php

namespace EnquisaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpcionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pregunta', 'entity', array(
            'class' => 'EnquisaBundle\Entity\Pregunta',
        ) );

        $builder
            ->add('valor')
            ->add('width')
            ->add('height')
            ->add('x')
            ->add('y')
        ;

    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EnquisaBundle\Entity\Opcion'
        ));
    }
}
