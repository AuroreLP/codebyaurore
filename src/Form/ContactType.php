<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Votre prénom'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer votre prénom']),
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
            ])

            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer votre nom']),
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
            ])
            
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Votre adresse email'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer un email']),
                    new Assert\Email(['message' => 'Veuillez entrer un email valide']),
                ],
            ])

            ->add('phone', TelType::class, [
                'label' => 'N° Téléphone',
                'attr' => ['placeholder' => 'Votre numéro de téléphone'],
                'required' => false, 
                'constraints' => [
                    new Assert\Length(['min' => 10, 'max' => 20]),
                ],
            ])

            ->add('subject', TextType::class, [
                'label' => 'Objet',
                'attr' => ['placeholder' => 'Objet de votre message'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer l\'objet de votre message']),
                ],
            ])

            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['placeholder' => 'Votre message', 'rows' => 5],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer votre message']),
                    new Assert\Length(['min' => 10, 'max' => 2000]),
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-dark'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
