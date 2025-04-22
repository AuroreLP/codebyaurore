<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_token', HiddenType::class, [
                'mapped' => false,
            ])

            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Entrez le titre de l\'article']
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug',
                'required' => false,
                'attr' => ['placeholder' => 'Généré automatiquement si vide']
            ])
            ->add('metaDescription', TextType::class, [
                'label' => 'Résumé',
                'required' => false,
                'attr' => ['placeholder' => 'Résumé de 160 caractères max']
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Brouillon' => 'draft',
                    'Prêt à publier' => 'ready',
                    'Publié' => 'published',
                ],
                'label' => 'Statut',
                'attr' => ['class' => 'select'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false, // <--- empêche le blocage avec CKEditor
                'attr' => ['placeholder' => 'Écrivez votre article ici...']
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // Assure-toi que `name` est une propriété de Category
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie'
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,       // permet la sélection multiple
                'expanded' => true,
                'required' => false,
                'attr' => [
                    'class' => 'select-multiple', // optionnel, pour ton CSS
                ]
            ])
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de publication',
                'required' => false,
                'html5' => true,
                'input' => 'datetime_immutable'
            ])
            
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}