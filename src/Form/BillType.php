<?php

namespace App\Form;

use App\Entity\Bill;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('name')
            ->add('amount')
            ->add('type', ChoiceType::class, [
                'choices' => Bill::TYPES,
                'choice_translation_domain' => 'messages',
                'choice_label' => function ($choiceValue, $key, $value) {
                    return $value;
                },
            ])
            ->add('place', EntityType::class, ['class' => Place::class, 'placeholder' => 'Оберіть місце'])
//            ->add('date')
            ->add(
                $builder->create('date', TextType::class)
                ->addModelTransformer(
                    new CallbackTransformer(
                        function ($dateToString) {
                            // transform the string back to an array
//                            dump($dateToString);exit;
                            return $dateToString->format('d/m/Y');
                        },
                        function ($stringToDate) {
                            // transform the array to a string
//                            dump($stringToDate);exit;
//
//                            dump($stringToDate);
//                            dump(\DateTime::createFromFormat('d-m-Y', $stringToDate));
//                            exit;

                            return \DateTime::createFromFormat('d/m/Y', $stringToDate);
//                            return true;
                        }
                    )
                )
            )
            ->add(
                $builder->create('period', TextType::class)
                    ->addModelTransformer(
                        new CallbackTransformer(
                            function ($dateToString) {
                                // transform the string back to an array

//                            dump($dateToString);exit;
                                return $dateToString ? $dateToString->format('m/Y') : null;
                            },
                            function ($stringToDate) {
                                // transform the array to a string
//                            dump($stringToDate);

                                $stringToDate = str_replace('Червень', 6, $stringToDate);

//                                        dump($stringToDate);

//                                exit;

//                                dump(\DateTime::createFromFormat('m Y', $stringToDate));exit;
//                            dump($stringToDate);
//                            dump(\DateTime::createFromFormat('d-m-Y', $stringToDate));
//                            exit;

                                return $stringToDate ? \DateTime::createFromFormat('m Y', $stringToDate) : null;
//                            return true;
                            }
                        )
                    )
            )
            ->add('note')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bill::class,
        ]);
    }
}
