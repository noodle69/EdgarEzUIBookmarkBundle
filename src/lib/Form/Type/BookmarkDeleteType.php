<?php

namespace Edgar\EzUIBookmark\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookmarkDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locationId', HiddenType::class, [
                'required' => true,
                // 'constraints' => [new LocationConstraint()],
            ])
            ->add('bookmark', SubmitType::class, [
                'label' => /** @Desc("Remove") */ 'bookmar_form.delete'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([]);
    }
}
