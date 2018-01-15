<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;
use App\Entity\Picture;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('name', FileType::class, [
                'label' => 'Importer une nouvelle photo : ',
            ])
            ->add('envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'hidden',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'user'   => null,
            'data_class' => Picture::class,
        ));
    }
}
