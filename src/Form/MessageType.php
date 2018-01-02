<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class)
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choices' => [
                    $options['user']->getId() => $options['user'],
                ],
            ])
            ->add('target', EntityType::class, [
                'class' => User::class,
                'choices' => [
                    $options['target']->getId() => $options['target'],
                ],

            ])
            ->add('envoyer', SubmitType::class);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'user'   => null,
            'target' => null,
        ));
    }
}
