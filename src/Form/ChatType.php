<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class ChatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content')
            ->add('user', EntityType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'hidden',
                ],
                'class' => User::class,
                'choices' => [
                    $options['user']->getId() => $options['user'],
                ],
            ])
            ->add('target', EntityType::class, [
                'attr' => [
                    'class' => 'hidden',
                ],
                'label' => false,
                'class' => User::class,
                'choices' => [
                    $options['target']->getId() => $options['target'],
                ],
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'user'   => null,
            'target' => null,
        ));
    }
}
