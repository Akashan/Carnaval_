<?php

namespace Carnaval\WebsiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array(
                'label' => "Nom",
            ))
            ->add('eventDate','date', array(
                'label' => 'Date',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'class' => 'datepicker'
                )
            ))
            ->add('lieu','text', array(
                'label' => "Lieu",
            ))
            ->add('description','textarea', array(
                'label' => "Description",
                'attr' => array(
                    'class' => 'tinymce',
                    'data-theme' => 'medium' // Skip it if you want to use default theme
                )
            ))
        ;

        // On récupère la factory (usine)
        $factory = $builder->getFormFactory();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Carnaval\WebsiteBundle\Entity\Evenement'
        ));
    }

    public function getName()
    {
        return 'carnaval_websitebundle_evenementtype';
    }
}