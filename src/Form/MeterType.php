<?php

namespace App\Form;

use App\Entity\Meter;
use App\Entity\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', EntityType::class, ['class' => \App\Entity\MeterType::class, 'placeholder' => 'Тип лічильника', 'choice_translation_domain' => 'messages'] )
            ->add('place')
            ->add('unit', EntityType::class, ['class' => Unit::class, 'placeholder' => 'Одиниця виміру', 'choice_translation_domain' => 'messages'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Meter::class,
        ]);
    }
}
