<?php


namespace Controller;


use Silex\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiController extends Controller
{
    protected $apiService;

    public function __construct($service)
    {
        $this->apiService = $service;
    }

    public function getOne($id)
    {
        return new JsonResponse($this->apiService->getOne($id));
    }

    public function getAll(Application $app, Request $request)
    {
        return new JsonResponse($this->apiService->getAll());
    }

    public function save(Request $request)
    {
        $authors = $this->getDataFromRequest($request);

        return new JsonResponse(["id" => $this->apiService->save($authors)]);
    }

    public function update($id, Request $request)
    {
        $authors = $this->getDataFromRequest($request);

        $this->apiService->update($id, $authors);

        return new JsonResponse($authors);
    }

    public function updatePatch($id, Request $request)
    {
        $authors = $this->getDataFromRequest($request);

        $this->apiService->updatePatch($id, $authors);

        return new JsonResponse($authors);
    }

    public function delete($id)
    {
        return new JsonResponse($this->apiService->delete($id));
    }

    public function getDataFromRequest(Request $request)
    {
        return $authors = array(
            "name" => $request->get("name"),
            "description" => $request->get("description"),
            "phone" => $request->get("phone")
        );
    }

}