<?php

namespace MH4Editor\MH4EditorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use MH4Editor\MH4EditorBundle\Form\UploadedFileTransformer;


class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username', 'text', array("label" => "Username","required" => true))
            ->add('email', 'email', array("label" => "E-mail","required" => true))            
            ->add('password', 'repeated',array(
                    "type"              => "password",
                    "invalid_message"   => "The passwords doesn't match",
                    "required"          => true,
                    "first_options"     => array("label" => "Password", "required" => true),
                    "second_options"    => array("label" => "Confirm Password", "required" => true),
                    
                )
            )  
            ->add(
                'mh4File',
                'file',
                array("label" => "MH4 User File")
                /*$builder->create(
                    'mh4File',
                    'file',
                    array("label" => "MH4 User File")
                )
                ->addModelTransformer(new UploadedFileTransformer)*/
            )
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MH4Editor\MH4EditorBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}