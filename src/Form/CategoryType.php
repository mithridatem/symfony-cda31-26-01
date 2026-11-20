<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => '', 'placeholder'=>'nom catégorie de livre ...'],
                'label'=> 'Saisir le nom de la catégorie'
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn mt-2'],
                //Assignation de la valeur de la propriété
                'label'=> $options['label_save_edit']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            //Valeur par défault de la propriété
            'label_save_edit' => 'Ajouter'
        ]);
        //Ajout d'une propriété (nom et type)
        $resolver->setAllowedTypes('label_save_edit', 'string');
    }
}
