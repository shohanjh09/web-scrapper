<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CompanyFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Search by company name or registration code'],
            ])
            ->add('filter', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary input-group-append'],
            ]);
    }
}
