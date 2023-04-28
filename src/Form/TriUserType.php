<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TriUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       $builder->add('Tritype', ChoiceType::class, array('choices'  => array(
           'Par lastname_Asc' => 'Par lastname_Asc',
           'Par lastname_Desc' => 'Par lastname_Desc',
           'Par diplome_Asc' => 'Par diplome_Asc',
           'Par diplome_Desc' => 'Par diplome_Desc',
           'Par experience_Asc' => 'Par experience_Asc',
           'Par experience_Desc' => 'Par experience_Desc',
       )

       ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
