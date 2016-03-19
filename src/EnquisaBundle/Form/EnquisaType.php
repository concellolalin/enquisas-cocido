<?php

namespace EnquisaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnquisaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('restaurante', 'entity', array(
            'class' => 'EnquisaBundle\Entity\Restaurante',
        ));

        $builder
            ->add('nome')
            ->add('ficheiro')
            ->add('procesada')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EnquisaBundle\Entity\Enquisa'
        ));
    }
}
