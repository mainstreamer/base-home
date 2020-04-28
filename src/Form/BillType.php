<?php

namespace App\Form;

use App\Entity\Bill;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', MoneyType::class, ['divisor' => 100, 'currency' => false])
            ->add('actuallyPaid', MoneyType::class, ['divisor' => 100, 'currency' => false, 'required' => false])
            ->add('file', FileType::class, ['required' => false, 'multiple' => true, 'mapped' => false])
            ->add('isPaid')
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Service type',
                'choices' => Bill::TYPES,
                'choice_translation_domain' => 'messages',
                'choice_label' => function ($choiceValue, $key, $value) {
                    return $value;
                },
            ])
            ->add('place', EntityType::class, ['class' => Place::class, 'placeholder' => 'Оберіть місце'])
            ->add(
                $builder->create('date', TextType::class)
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
            ->add(
                $builder->create('payDate', TextType::class, ['required' => false])
                    ->addModelTransformer(
                        new CallbackTransformer(
                            function ($dateToString) {
                                if (null !== $dateToString) {
                                    return $dateToString->format('d/m/Y');
                                }
                                return null;
                            },
                            function ($stringToDate) {
                                if ($stringToDate) {
                                    return \DateTime::createFromFormat('d/m/Y', $stringToDate);
                                }
                                return null;
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
                                return $dateToString ? $dateToString->format('m/Y') : null;
                            },
                            function ($stringToDate) {
                                // transform the array to a string
                                $stringToDate = str_replace(
                                    [   'Січень ',
                                        'Лютий ',
                                        'Березень ',
                                        'Квітень ',
                                        'Травень ',
                                        'Червень',
                                        'Липень ',
                                        'Серпень ',
                                        'Вересень ',
                                        'Жовтень ',
                                        'Листопад ',
                                        'Грудень '],
                                    ['01/', '02/', '03/', '04/', '05/', '06/', '07/', '08/', '09/', '10/', '11/', '12/'], $stringToDate);

                                return $stringToDate ? \DateTime::createFromFormat('m/Y', $stringToDate) : null;
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
