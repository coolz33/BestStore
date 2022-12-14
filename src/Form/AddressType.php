<?php

namespace App\Form;

use App\Entity\Adress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'Quel nom souhaitez-vous donner à votre adresse ?',
                'attr' => [
                    'placeholder' => 'nommez votre adresse'
                ]
            ])
            ->add('firstname',TextType::class,[
                'label' => 'Votre prénom',
                'attr' => [
                    'placeholder' => 'Entrez votre prénom'
                ]
            ])
            ->add('lastname',TextType::class,[
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Entrez votre nom'
                ]
            ])
            ->add('company',TextType::class,[
                'label' => 'Votre société',
                'required' => false,
                'attr' => [
                    'placeholder' => '(facultatif) entrez le nom de votre société',

                ]
            ])
            ->add('address',TextType::class,[
                'label' => 'Votre adresse',
                'attr' => [
                    'placeholder' => '8 rue des lylas...'
                ]
            ])
            ->add('postal',TextType::class,[
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Entrez votre code postal'
                ]
            ])
            ->add('city',TextType::class,[
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Entrez votre ville '
                ]
            ])
            ->add('country',CountryType::class,[
                'label' => 'Pays',
                'preferred_choices' =>['FR'],
                'attr' => [
                    'placeholder' => 'Votre pays',
                ]
            ])
            ->add('phone',TelType::class,[
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Entrez votre téléphone'
                ]
            ])
            ->add('submit',SubmitType::class, [
                'label'=> 'Valider',
                'attr' => [
                    'class' => 'col-12 btn-info  '
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
