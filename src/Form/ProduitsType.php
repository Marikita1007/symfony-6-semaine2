<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Distributeurs;
use App\Entity\Produits;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Nom du produits'
            ])
            ->add('description', TextareaType::class,[
                'label' => 'Description du produits'
            ])
            ->add('prix', MoneyType::class,[
                'label' => 'Prix du produits'
            ])
            ->add('reference', ReferencesType::class,[
                'label' => 'Reference du produits'
            ])
            ->add('categorie', EntityType::class,[
                'label' => 'Categorie du produits',
                'class' => Categories::class,
                'choice_label' => 'name'
            ])
            ->add('distributeur', EntityType::class,[
                'label' => 'Choix du distributeur',
                'class' => Distributeurs::class,
                'choice_label' => 'name',
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
