<?php

namespace App\Form;

use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('addressOrder', null, ['required' => false])
            ->add('addressComplementOrder', null, ['required' => false])
            ->add('cityOrder', null, ['required' => false])
            ->add('zipCodeOrder', NumberType::class, ['required' => false])
            ->add('countryOrder', ChoiceType::class, [
                'choices' => [
                    'France' => 'france',
                    'Belgique' => 'belgique',
                    'Luxembourg' => 'luxembourg',
                ],
            ], ['required' => false])
            ->add('client', ClientFormType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
        ]);
    }
}
