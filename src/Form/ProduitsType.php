<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Distributeurs;
use App\Entity\Produits;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                'label' => 'Product name'
            ])
            ->add('description', TextareaType::class,[
                'label' => 'product description'
            ])
            ->add('prix', MoneyType::class,[
                'label' => 'Product Price'
            ])
            ->add('reference', ReferencesType::class,[
                'label' => 'Product reference',
            ])
            ->add('distributeur', EntityType::class,[
                'label' => 'choice of distributor',
                'class' => Distributeurs::class,
                'multiple' => true,
                'choice_label' => 'name',
                'expanded' => true
            ])
            ->add('categorie', EntityType::class,[
                'label' => 'Product category',
                'class' => Categories::class,
                'choice_label' => 'name',
            ])
            ->add('photos', CollectionType::class,[
                'entry_type' => PhotosType::class,
                'entry_options' => ['label' => false ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false
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
