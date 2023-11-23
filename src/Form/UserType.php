<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'label' => 'Email',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preFillEmail'])
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'New password',
                    'hash_property_path' => 'password'
                ],
                'second_options' => [
                    'label' => 'Insert new password again',
                ],
                'mapped' => false
            ])
        ;
    }

    public function preFillEmail(FormEvent $event): void
    {
        $user = $event->getData();

        if($user instanceof User)
        {
            $event->getForm()->get('email')->setData($user->getEmail());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
