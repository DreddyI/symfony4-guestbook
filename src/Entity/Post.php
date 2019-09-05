<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="text", name="body", nullable=false)
     * @var string
     */
    private $body;

    /**
     * @ORM\Column(type="text", name="author", nullable=false)
     * @var string
     */
    private $author;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     * @var
     */
    private $createdAt;

    public function __construct(string $body, string $author)
    {
        $this->author = $author;
        $this->body = $body;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
}
