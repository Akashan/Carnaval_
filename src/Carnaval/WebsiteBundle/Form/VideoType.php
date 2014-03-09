<?php

namespace Carnaval\WebsiteBundle\Form;

use Carnaval\WebsiteBundle\Entity\CategorieVideoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categorie', 'entity', array(
                'class'    => 'CarnavalWebsiteBundle:CategorieVideo',
                'query_builder' => function(CategorieVideoRepository $er) {
                    return $er->getAllActiveCategorieQueryBuilder();
                },
                'property' => 'name',
                'multiple' => false,
                'expanded' => false,
                'label' => "Catégorie",
            ))
            ->add('codeExterne','text', array(
                'label' => "Code vidéo (ex : EUSglyLH_yc)",
            ))
            ->add('name','text', array(
                'label' => "Titre de la vidéo",
            ))
            ->add('miniature','text')
            ->add('description','textarea', array(
                'attr' => array('cols' => '75', 'rows' => '15'),
            ))
        ;

        // On récupère la factory (usine)
        $factory = $builder->getFormFactory();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Carnaval\WebsiteBundle\Entity\Video'
        ));
    }

    public function getName()
    {
        return 'carnaval_websitebundle_videotype';
    }
}