<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class Authors
 * @ORM\Entity
 * @ORM\Table(name="authors")
 */
class Authors
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="Books", inversedBy="authors")
     * @ORM\JoinColumn(name="author_book")
     */
    protected $books;


    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function setBooks(Books $books = null)
    {
        $this->books = $books;

        return $this;
    }

    public function getBooks()
    {
        return $this->books;
    }

}