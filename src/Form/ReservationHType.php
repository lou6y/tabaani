<?php

namespace App\Form;

use App\Entity\Reservationhotel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class ReservationHType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {$date = new DateTime('now');

        $builder
            ->add('nbNuit')
            ->add('nbChambre')
            ->add('type',ChoiceType::class, [
        'choices'  => [
            'adulte + enfant' => "adulte + enfant",
            'adulte' => "adulte",
            'conference' => "conference",
        ],])
            ->add('nbPersonne')
            ->add('dateReservation', DateType::class,[
                'invalid_message' => 'You entered an invalid value, it should be bigger than today s date',
                'data' => new \DateTime("now")
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservationhotel::class,
        ]);

    }

}
