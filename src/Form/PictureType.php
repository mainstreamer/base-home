<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//            ->add('path', FileType::class)
//        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
//            'data_class' => Picture::class,
        ]);
    }
}
