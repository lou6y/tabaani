<?php

namespace App\Form;

use App\Entity\Hotel;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categ',ChoiceType::class, [
                'choices'  => [
                    'sans Alcool' => "sans Alcool",
                    'Alcool' => "Alcool",

                ],])
            ->add('type',ChoiceType::class, [
                'choices'  => [
                    'sport' => "sport",
                    'reeducation' => "reeducation",
                    'pro' => "pro",
                ],])
            ->add('idHotel', EntityType::class, [
                'class' => Hotel::class,

                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
