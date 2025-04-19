<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Votre pseudo',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'required' => true,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Votre commentaire',
                'required' => true,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en attente',
                    'Validé' => 'validé',
                    'Refusé' => 'refusé',
                ],
                'data' => $options['data']->getStatus(), // Valeur par défaut
                'label' => 'Statut',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}