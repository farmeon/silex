<?php

namespace Form;

use Entity\Authors;
use Entity\Books;
use Service\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;



class AuthorsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 5])
                ]
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 10])
                ]
            ]);

        $booksList  = array();
        $booksActive  = array();

        foreach ($options["book_list"] as $id => $books) {
            $booksList[] = $books;
            if ($builder->getData()->getBooks($books)) {
                $choicesActive[] = $books;

            }
        }

        $builder->add('books', ChoiceType::class, array(
            'choices' => $booksList,
            'data' => $booksActive,
            'choice_label' => function($books, $key, $index) {
                return $books->getName();
            },
            'multiple' => True,
        ))

            ->add('submit', SubmitType::class, [
                'label' => 'Save',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Authors::class,
        ));

        $resolver->setRequired(array(
            'book_list',
        ));

    }

    public function getName()
    {
        return 'name';
    }
}