<?php

namespace App\Form;

use App\Entity\Tariff;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TariffType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price')
            ->add('description')
//            ->add('type')
            ->add('name')
            ->add('user')
            ->add('type', EntityType::class, [
                'class' => \App\Entity\TariffType::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.user = :user')
                        ->setParameter('user', $options['data']->getUser())
                    ;
                },
//                'expanded' => false,
//                'multiple' => true,
//                'choice_label' => 'type',
//                'choice_translation_domain' => 'messages',
//                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tariff::class,
        ]);
    }
}
