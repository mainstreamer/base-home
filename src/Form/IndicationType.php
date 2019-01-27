<?php

namespace App\Form;

use App\Entity\Indication;
use App\Entity\Tariff;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dump($options['data']->getMeter()->getTariffs());exit;
        $builder
//            ->add('name')
            ->add('value')
            ->add('tariff', ChoiceType::class, [
//                'class' => Tariff::class,
                'choices' => $options['data']->getMeter()->getTariffs(),
                'choice_label' => 'name'

//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('')
//                        ->innerJoin('i.t')
//                    ->andWhere('');
//                        ->orderBy('u.username', 'ASC');
//                },
//                'choice_label' => 'username',
            ])
            ->add( $builder->create('date', TextType::class)
                ->addModelTransformer(
                    new CallbackTransformer(
                        function ($dateToString) {
                            return $dateToString->format('d/m/Y');
                        },
                        function ($stringToDate) {

                            return \DateTime::createFromFormat('d/m/Y', $stringToDate);
                        }
                    )
                )
            )
            ->add('meter')
            ->add('file', FileType::class, ['required' => false])
//            ->add('unit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Indication::class,
        ]);
    }
}
