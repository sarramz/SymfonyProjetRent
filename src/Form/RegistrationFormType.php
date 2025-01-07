<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'attr' => ['placeholder' => 'Enter your first name'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'First name is required.',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'attr' => ['placeholder' => 'Enter your last name'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Last name is required.',
                    ]),
                ],
            ])
            ->add('email',  EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Votre adresse email'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Email is required.',
                    ]),
                ],
            ])
            ->add("roles", ChoiceType::class, [
                "choices" => [
                    "USER" => "ROLE_USER"
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'I agree to the terms and conditions',
                'constraints' => [
                    new IsTrue([
                        'message' => 'You must agree to the terms and conditions.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Password',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Password is required.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password must be at least {{ limit }} characters.',
                        'max' => 4096, // Symfony's security limit
                    ]),
                ],
            ]);


        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
            function ($rolesAsArray) {
                // Transform the array to a comma-separated string
                return implode(', ', $rolesAsArray);
            },
            function ($rolesAsString) {
                // Transform the string back to an array
                return array_map('trim', explode(',', $rolesAsString));
            }
        ));}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
