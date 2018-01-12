<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\User;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Homme' => User::GENDER_MALE,
                    'Femme' => User::GENDER_FEMALE,
                ],
            ])
            ->add('birthdate', BirthdayType::class)
            ->add('profilePicture', FileType::class)
            ->add('shortDescription')
            ->add('description')
            ->add('height')
            ->add('weight')
            ->add('job')
            ->add('isTattooed')
            ->add('isPierced');
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_profile';
    }
}
