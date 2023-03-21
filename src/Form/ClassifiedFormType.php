<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Market\Classified;
use App\Entity\Market\Item;
use App\Service\ImageUploadService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Uuid;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class ClassifiedFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ]
            ])
            ->add('items', LiveCollectionType::class,
                [
                    'label' => false,
                    'entry_type' => MarketItemFormType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'button_delete_options' => [
                        'label' => 'remove item',
                        'attr' => [
                            'class' => 'w-100 btn btn-outline-danger',
                        ],
                    ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classified::class,
        ]);
    }
}
