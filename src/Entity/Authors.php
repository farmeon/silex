<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Validator\ConstraintPhone;
use JMS\Serializer\Annotation\Type;

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
     * @Type("integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Type("string")
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     * @Type("string")
     */
    protected $description;

    /**
     * @ORM\Column(type="text")
     * @Type("string")
     */
    protected $phone;

    /**
     * @ORM\ManyToMany(targetEntity="Books", inversedBy="authors")
     * @ORM\JoinTable(name="authors_books")
     * @Type("array")
     */
    protected $books;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('phone', new ConstraintPhone());
    }

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

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

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return Collection|Books[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBooks(Books $books = null): self
    {
        if (!$this->books->contains($books)) {
            $this->books[] = $books;
        }

        return $this;
    }

    public function setBooks(Books $books = null): self
    {
        if (!$this->books->contains($books)) {
            $this->books[] = $books;
        }

        return $this;
    }
}