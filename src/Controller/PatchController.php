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
        $factory->post("/{id}", [$this, 'index'])->bind('patch_index');

        return $factory;
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
    public function index(int $id, Application $app, Request $request)
    {
        $json_request = $request->getContent();
        $format = $request->getContentType();

        $author = $app['orm.em']->getRepository(Authors::class)->find($id);

        $serialize_author = $app["serializer"]->serialize($author, $format);

        //$tested_str = '[{"op": "replace", "path": "/name", "value": "Adam"}]';
        //{"id":1,"name":"Testing","description":"testing","phone":"12332123","books":[]}
        //$patch = new Patch($serialize_author, $tested_str);

        $patch = new Patch($serialize_author, $json_request);
        $patchedDocument = $patch->apply();

        $deserialize_author = $app["serializer"]->deserialize($patchedDocument, Authors::class, $format);

        $app['orm.em']->merge($deserialize_author);
        $app['orm.em']->flush();

        return $patchedDocument;
    }
}