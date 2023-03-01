<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Resto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RestoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomresto')
            ->add('nbplace')
            ->add('budget')

            ->add('specialite',EntityType::class,['class'=>Categorie::class,'choice_label'=>'NomCategorie'])
            ->add('imageFile',VichImageType::class)
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resto::class,
        ]);
    }
}
