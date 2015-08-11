<?php

namespace MH4Editor\MH4EditorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use MH4Editor\MH4EditorBundle\Form\UserType;


class UserRegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('user', new UserType())
        ;

    }

    public function getName()
    {
        return 'user_registration';
    }
}