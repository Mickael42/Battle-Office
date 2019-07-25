<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ClientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('phone', TextType::class)
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => 'The Email fields must match.',
                'required' => true,
                'first_options' => ['label' => 'Email'],
                'second_options' => ['label' => 'Confirmation email'],
            ])
            ->add('address')
            ->add('addressComplement')
            ->add('city')
            ->add('zipCode', TextType::class)
            ->add('country', ChoiceType::class, [
                'choices' => [
                    'France' => 'france',
                    'Belgique' => 'belgique',
                    'Luxembourg' => 'luxembourg',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
