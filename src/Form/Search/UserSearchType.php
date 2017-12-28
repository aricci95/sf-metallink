<?php
namespace App\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;

class UserSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', null, ['mapped' => false])
            ->add('age', IntegerType::class, ['mapped' => false])
            ->add('gender', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Homme' => User::GENDER_MALE,
                    'Femme' => User::GENDER_FEMALE,
                ]
            ])
            ->add('save', SubmitType::class);
    }
}
