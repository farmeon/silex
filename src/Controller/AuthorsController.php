<?php

namespace Controller;

use Entity\Authors;
use Entity\Books;
use Form\Type\BooksType;
use Form\Type\BooksTypes;
use Silex\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Silex\Api\ControllerProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Form\Type\AuthorsType;

class AuthorsController extends AbstractController implements ControllerProviderInterface
{

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->get("/", [$this, 'index'])->bind('author_index');
        $factory->get("/show/{id}", [$this, 'show'])->bind('author_show');
        $factory->match("/create", [$this, 'create'])->bind('author_insert');
        $factory->match("/update/{id}", [$this, 'update'])->bind('author_update');
        $factory->get("/delete/{id}", [$this, 'delete'])->bind('author_delete');

        return $factory;
    }

    /**
     * List all authors
     * @param Application $app
     * @return mixed
     */
    public function index(Application $app)
    {
        $authors = $app['orm.em']->getRepository(Authors::class)
            ->findAll();

        return $app['twig']->render('/admin/authors/authors.html.twig', [
            'authors' => $authors
        ]);
    }

    /**
     * Show author by id
     * @param Application $app
     * @param int $id
     * @return mixed
     */
    public function show(Application $app, int $id)
    {
        $author = $app['orm.em']->getRepository(Authors::class)
            ->find($id);

        if (!$author) {
            $app->abort(404, 'No author found for id ' . $id);
        }

        return $app['twig']->render('/admin/authors/show.html.twig', [
            'authors' => $author
        ]);
    }

    /**
     * Update author by id
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Application $app, Request $request, int $id)
    {
        $author = $app['orm.em']->getRepository(Authors::class)
            ->find($id);

        if (!$author) {
            $app->abort(404, 'No author found for id ' . $id);
        }

        $form = $app['form.factory']->createBuilder(FormType::class, $author)
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
            ])

            ->add('books', ChoiceType::class, [
                'choices' => $app['orm.em']->getRepository(Books::class)->findAll(),
                'choices_as_values' => true,
                'choice_label' => function($books, $key, $value) {
                    return strtoupper($books->getName());
                },
                'choice_attr' => function($books, $key, $value) {
                    return ['class' => 'books_'.strtolower($books->getId())];
                },
            ])

            /*->add('books', CollectionType::class, array(
                'entry_type' => BooksType::class,
                'allow_add' => true,
                'allow_delete' => true
            ))*/

            ->add('submit', SubmitType::class, [
                'label' => 'Save',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $app['orm.em']->flush();
            $app['session']->getFlashBag()->add('success', 'Author update success!');

            return $app->redirect($app['url_generator']->generate('author_show', ['id' => $author->getId()]));
        }

        return $app['twig']->render('/admin/authors/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete author by id
     * @param Application $app
     * @param int $id
     * @return mixed
     */
    public function delete(Application $app, int $id)
    {
        $author = $app['orm.em']->getRepository(Authors::class)
            ->find($id);

        if (!$author) {
            $app->abort(404, 'No author found for id '.$id);
        }

        $app['orm.em']->remove($author);
        $app['orm.em']->flush();

        return $app->redirect($app['url_generator']->generate('author_index'));
    }

    /**
     * Create author
     * @param Application $app
     * @param Request $request
     * @return mixed
     */
    public function create(Application $app, Request $request)
    {
        $author = new Authors();

        $form = $app['form.factory']->createBuilder(FormType::class, $author)
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
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $app['orm.em']->persist($author);
            $app['orm.em']->flush();

            return $app->redirect($app['url_generator']->generate('author_show', ['id' => $author->getId()]));
        }

        return $app['twig']->render('/admin/authors/create.html.twig', [
            'author' => $author,
            'form' => $form->createView()
        ]);
    }

}