<?php


namespace Form\Type;

use Entity\Books;
use Silex\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class BooksTyp extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('books', EntityType::class, [ // add this
                'label'     => 'Who is fighting in this round?',
                'expanded'  => true,
                'multiple'  => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Books::class,
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Entity\Books'
        );
    }

    public function getName()
    {
        return 'books';
    }
}