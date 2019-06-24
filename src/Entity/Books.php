<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Books
 * @ORM\Entity
 * @ORM\Table(name="books")
 */
class Books
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
     * @ORM\ManyToMany(targetEntity="Authors", mappedBy="books")
     */
    protected $authors;


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

    public function addAuthors(Authors $authors)
    {
        $this->authors[] = $authors;

        return $this;
    }

    public function removeAuthors(Authors $authors)
    {
        $this->authors->removeElement($authors);
    }

    public function getAuthors()
    {
        return $this->authors;
    }

}