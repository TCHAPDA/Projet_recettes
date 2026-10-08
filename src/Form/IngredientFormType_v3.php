<?php

namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientFormType_v3 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'ingrédient',
                'help' => 'Le nom de l\'ingrédient est obligatoire',
                'constraints' => [
                    new Assert\Length(['min' => 3, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'help' => 'Le prix de l\'ingrédient est obligatoire'
            ])
            // ->add('creatdAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                // 'label' => 'Creer mon ingrédient'
                'label' => $options['submit_label'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
            'submit_label' => null, //nouvelle option
        ]);
    }
}
