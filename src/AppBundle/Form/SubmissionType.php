<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmissionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path', 'hidden', [
                'label' => false,
            ])
            ->add('name', 'text', [
                'label' => false,
                'attr' => [
                    'placeholder'=> 'share.form.name',
                ]
            ])
            ->add('location', 'text', [
                'label' => false,
                'attr' => [
                    'placeholder'=> 'share.form.location',
                ]
            ])
            ->add('submit', 'submit', [
                'label' => 'share.form.submit',
                'attr' => ['class' => 'btn upload', 'disabled' => 'disabled']
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver  $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Submission'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'submission';
    }
}