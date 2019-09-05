<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePostRequest
{
    /**
     * @Assert\NotBlank(message=" should be not blank ")
     * @var string
     */
    public $author;

    /**
     * @Assert\NotBlank(message="should be not blank")
     * @var string
     */
    public $body;
}
