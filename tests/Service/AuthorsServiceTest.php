<?php

namespace Tests\Services;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Service\AuthorsService;


class AuthorsServiceTest extends TestCase
{
    private $authorsService;

    public function setUp()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), [
            'db.options' => [
                'driver' => 'pdo_pgsql',
                'host' => '192.168.48.3',
                'dbname' => 'db_silex',
                'user' => 'silex',
                'password' => 'silex',
                'port' => '5432'
            ],
        ]);
        $this->authorsService = new AuthorsService($app["db"]);

        $stmt = $app["db"]->prepare("CREATE TABLE authors (id serial, name int, description varchar, phone string)");
        $stmt->execute();
        $stmt = $app["db"]->prepare("INSERT INTO authors (name, description, phone) VALUES ('test', 'description', 123321)");
        $stmt->execute();
    }

    public function testGetOne()
    {
        $data = $this->authorsService->getOne(1);
        $this->assertEquals('test', $data['name']);
    }

    public function testGetAll()
    {
        $data = $this->authorsService->getAll();
        $this->assertNotNull($data);
    }

    function testSave()
    {
        $note = ["name" => "arny"];
        $this->authorsService->save($note);
        $data = $this->authorsService->getAll();
        $this->assertEquals(2, count($data));
    }

    function testUpdate()
    {
        $note = ["name" => "arny1"];
        $this->authorsService->save($note);
        $note = ["name" => "arny2"];
        $this->authorsService->update(1, $note);
        $data = $this->authorsService->getAll();
        $this->assertEquals("arny2", $data[0]["name"]);
    }

    function testDelete()
    {
        $note = ["name" => "arny1"];
        $this->authorsService->save($note);
        $this->authorsService->delete(1);
        $data = $this->authorsService->getAll();
        $this->assertEquals(1, count($data));
    }
}