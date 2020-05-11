<?php

namespace App\Form;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('period')
            ->add('type', ChoiceType::class, ['choices' => Subscription::TYPES])
            ->add('autoRenew')
            ->add('card')
            ->add('currency', ChoiceType::class, [
                'choices' => array_map(function ($v) { return [$v => $v];}, Currencies::getCurrencyCodes())
                ]
            )
            ->add($builder->create('startDate', TextType::class)
                ->addModelTransformer(
                    new CallbackTransformer(
                        function ($dateToString) {
                            if (null !== $dateToString) {
                                return $dateToString->format('d/m/Y');
                            }
                        },
                        function ($stringToDate) {
                            return \DateTime::createFromFormat('d/m/Y', $stringToDate);
                        }
                    )
                )
            )
            ->add($builder->create('endDate', TextType::class, ['required' => false])
                ->addModelTransformer(
                    new CallbackTransformer(
                        function ($dateToString) {
                            if (null !== $dateToString) {
                                if (false === $dateToString) {
                                    return null;
                                }
                                return $dateToString->format('d/m/Y');
                            } else {
                                return null;
                            }
                        },
                        function ($stringToDate) {
                            if (null === $stringToDate) {
                                return null;
                            }
                            return \DateTime::createFromFormat('d/m/Y', $stringToDate);
                        }
                    )
                )
            )
            ->add($builder->create('nextBillingDate', TextType::class, ['required' => false])
                ->addModelTransformer(
                    new CallbackTransformer(
                        function ($dateToString) {
                            if (null !== $dateToString) {
                                if (false === $dateToString) {
                                    return null;
                                }
                                return $dateToString->format('d/m/Y');
                            } else {
                                return null;
                            }
                        },
                        function ($stringToDate) {
                            if (null === $stringToDate) {
                                return null;
                            }
                            return \DateTime::createFromFormat('d/m/Y', $stringToDate);
                        }
                    )
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
