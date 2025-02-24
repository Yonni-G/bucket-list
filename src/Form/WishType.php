<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Categories;
use App\Entity\Genre;
use App\Entity\User2;
use App\Entity\Wish;
use App\Repository\GenreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;


class WishType extends AbstractType
{

    private GenreRepository $genreRepository;

    public function __construct(GenreRepository $genreRepository) {
        $this->genreRepository = $genreRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Entrez un titre',
                ],
                'required' => false,
            ])


            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('cgv', CheckboxType::class, [
                'label' => 'Accepter les CGV',
                'mapped' => false, // ❗ Empêche Symfony de chercher ce champ dans l'entité
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez accepter les CGV.',

                    ])
                ],
            ])
            ->add('poster_file', FileType::class, [
                'label' => 'Poster',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                        'maxSizeMessage' => 'Trop lourd ! (Maximum size is 1MB)',

                    ])
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class, // Entité à utiliser pour le sélecteur
                'choice_label' => 'name', // Attribut de l'entité Category à afficher dans la liste déroulante
                'placeholder' => 'Choisir une catégorie', // Optionnel : ajouter un message "Choisir"
                'required' => true, // Optionnel : rendre le champ obligatoire
            ])
            ->add('genres', EntityType::class, [
                'class' => Genre::class, // L'entité Genre
                'choice_label' => 'nom', // Attribut de Genre à afficher
                'multiple' => true, // Permet de sélectionner plusieurs valeurs
                'expanded' => true, // Transforme le champ en cases à cocher
                'required' => true, // Rend le champ obligatoire
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wish::class,
        ]);
    }
}

