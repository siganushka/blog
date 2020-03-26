<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\Model\TimestampableInterface;
use Siganushka\GenericBundle\Model\TimestampableTrait;
use Siganushka\GenericBundle\Model\UuidResourceInterface;
use Siganushka\GenericBundle\Model\UuidResourceTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment implements UuidResourceInterface, TimestampableInterface
{
    const STATE_PENDING = 'pending';
    const STATE_APPROVED = 'approved';
    const STATE_UNAPPROVED = 'unapproved';

    use UuidResourceTrait;
    use TimestampableTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="comments")
     */
    private $post;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=16)
     */
    private $content;

    /**
     * @ORM\Column(type="string")
     */
    private $state;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
