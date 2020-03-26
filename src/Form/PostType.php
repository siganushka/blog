<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'resource.post.title',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'resource.post.content',
                'attr' => ['rows' => 20],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
            ->add('submit', SubmitType::class, [
                'label' => 'resource.post.submit',
                'attr' => ['rows' => 20],
            ])
        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        $data = $event->getData();
        $label = (!$data || $data->isNew())
            ? 'resource.post.submit_create'
            : 'resource.post.submit_update';

        $form = $event->getForm();
        $form->add('submit', SubmitType::class, [
            'label' => $label,
            'attr' => ['rows' => 20],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
