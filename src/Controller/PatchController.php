<?php


namespace Controller;

use Entity\Authors;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Rs\Json\Patch;
use Rs\Json\Patch\InvalidPatchDocumentJsonException;
use Rs\Json\Patch\InvalidTargetDocumentJsonException;
use Rs\Json\Patch\InvalidOperationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;



class PatchController extends AbstractController  implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->get("/{id}", [$this, 'getOne'])->bind('get_one');
        $factory->patch("/{id}", [$this, 'patch'])->bind('patch');
        $factory->delete("/{id}", [$this, 'remove'])->bind('remove');

        return $factory;
    }

    /**
     * @param int $id
     * @param Application $app
     * @param Request $request
     * @return string
     * @throws InvalidOperationException
     * @throws InvalidPatchDocumentJsonException
     * @throws InvalidTargetDocumentJsonException
     * @throws Patch\FailedTestException
     */
    public function getOne(int $id, Application $app, Request $request)
    {
        $format = 'xml';

        $author = $app['orm.em']->getRepository(Authors::class)->find($id);

        $serialize_author = $app["serializer"]->serialize($author, $format);


        return $serialize_author;
    }

    /**
     * @param int $id
     * @param Application $app
     * @param Request $request
     * @return mixed
     * @throws InvalidOperationException
     * @throws InvalidPatchDocumentJsonException
     * @throws InvalidTargetDocumentJsonException
     * @throws Patch\FailedTestException
     */
    public function patch(int $id, Application $app, Request $request)
    {
        $json_request = $request->getContent();
        $format = $request->getContentType();

        $author = $app['orm.em']->getRepository(Authors::class)->find($id);

        $serialize_author = $app["serializer"]->serialize($author, $format);

        $patch = new Patch($serialize_author, $json_request);
        $patchedDocument = $patch->apply();

        $deserialize_author = $app["serializer"]->deserialize($patchedDocument, Authors::class, $format);

        $app['orm.em']->merge($deserialize_author);
        $app['orm.em']->flush();

        return $patchedDocument;
    }

    /**
     * @param int $id
     * @param Application $app
     * @param Request $request
     * @return mixed
     * @throws InvalidOperationException
     * @throws InvalidPatchDocumentJsonException
     * @throws InvalidTargetDocumentJsonException
     * @throws Patch\FailedTestException
     */
    public function remove(int $id, Application $app, Request $request)
    {
        $json_request = $request->getContent();
        $format = $request->getContentType();

        $author = $app['orm.em']->getRepository(Authors::class)->find($id);

        $serialize_author = $app["serializer"]->serialize($author, $format);

        $patch = new Patch($serialize_author, $json_request);
        $patchedDocument = $patch->apply();

        $deserialize_author = $app["serializer"]->deserialize($patchedDocument, Authors::class, $format);

        $app['orm.em']->merge($deserialize_author);
        $app['orm.em']->flush();

        return $patchedDocument;
    }

}