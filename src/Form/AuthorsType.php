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

        $books  = array();
        $booksActive  = array();

        foreach ($builder->getData()->getBooks() as $id => $books) {
            $books[$books->getLabel()] = $books->getId();
            if ($books->getActive()) {
                $booksActive[] = $books->getId();
            }
        }

        $builder->add('books', ChoiceType::class, array(
            'choices' => $options['book_list'],
            'data' => $booksActive,
            'label' => "Groupes",
            'multiple' => True,
            'expanded' => True
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