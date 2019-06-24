<?php

namespace Controller;

use Entity\Books;
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

class BooksController extends AbstractController implements ControllerProviderInterface
{

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->get("/", [$this, 'index'])->bind('book_index');
        $factory->get("/show/{id}", [$this, 'show'])->bind('book_show');
        $factory->match("/create", [$this, 'create'])->bind('book_insert');
        $factory->match("/update/{id}", [$this, 'update'])->bind('book_update');
        $factory->get("/delete/{id}", [$this, 'delete'])->bind('book_delete');

        return $factory;
    }

    /**
     * List all books
     * @param Application $app
     * @return mixed
     */
    public function index(Application $app)
    {
        $books = $app['orm.em']->getRepository(Books::class)
            ->findAll();

        return $app['twig']->render('/admin/books/books.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * Show book by id
     * @param Application $app
     * @param int $id
     * @return mixed
     */
    public function show(Application $app, int $id)
    {
        $book = $app['orm.em']->getRepository(Books::class)
            ->find($id);

        if (!$book) {
            $app->abort(404, 'No book found for id ' . $id);
        }

        return $app['twig']->render('/admin/books/show.html.twig', [
            'books' => $book
        ]);
    }

    /**
     * Update book by id
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Application $app, Request $request, int $id)
    {
        $book = $app['orm.em']->getRepository(Books::class)
            ->find($id);

        if (!$book) {
            $app->abort(404, 'No book found for id ' . $id);
        }

        $form = $app['form.factory']->createBuilder(FormType::class, $book)
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

            $app['orm.em']->flush();
            $app['session']->getFlashBag()->add('success', 'Book update success!');

            return $app->redirect($app['url_generator']->generate('book_show', ['id' => $book->getId()]));
        }

        return $app['twig']->render('/admin/books/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete book by id
     * @param Application $app
     * @param int $id
     * @return mixed
     */
    public function delete(Application $app, int $id)
    {
        $book = $app['orm.em']->getRepository(Books::class)
            ->find($id);

        if (!$book) {
            $app->abort(404, 'No book found for id '.$id);
        }

        $app['orm.em']->remove($book);
        $app['orm.em']->flush();

        return $app->redirect($app['url_generator']->generate('book_index'));
    }

    /**
     * Create book
     * @param Application $app
     * @param Request $request
     * @return mixed
     */
    public function create(Application $app, Request $request)
    {
        $books = new Books();

        $form = $app['form.factory']->createBuilder(FormType::class, $books)
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

            $app['orm.em']->persist($books);
            $app['orm.em']->flush();

            return $app->redirect($app['url_generator']->generate('book_show', ['id' => $books->getId()]));
        }

        return $app['twig']->render('/admin/books/create.html.twig', [
            'book' => $books,
            'form' => $form->createView()
        ]);
    }

}