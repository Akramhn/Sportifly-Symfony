<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;



class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class,[
                "attr" => [
                    "class" => "form-control",
                    "placeholder" => "Description"
                ],
                "required" => false
            ])
            ->add('prix', NumberType::class,[
                "attr" => [
                    "class" => "form-control",
                    "placeholder" => "prix"
                    ],
                "required" => false

            ])
            ->add('nbplace', NumberType::class,[
                "attr" => [
                    "class" => "form-control",
                    "placeholder" => "nombre de place disponible"
                ],
                "required" => false

            ])
            ->add('affiche', FileType::class, [
                "attr" =>[
                    'placeholder' => 'Ajouter votre affiche(image file): '
                ]
                ,

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '40096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ])
            ->add('id_category')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
            ""
        ]);
    }
}
