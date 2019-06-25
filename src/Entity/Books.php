<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Collection;

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

    public function __construct()
    {
        $this->authors = new ArrayCollection();
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

    /**
     * @return Collection|Authors[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthors(Authors $authors): self
    {
        if (!$this->authors->contains($authors)) {
            $this->authors[] = $authors;
            $authors->addTag($this);
        }

        return $this;
    }

    public function setAuthors(Authors $authors): self
    {
        if (!$this->authors->contains($authors)) {
            $this->authors[] = $authors;
            $authors->addTag($this);
        }

        return $this;
    }

    public function removeAuthors(Authors $authors):self
    {
        if ($this->authors->contains($authors)) {
            $this->authors->removeElement($authors);
            $authors->removeTag($this);
        }

        return $this;
    }
}