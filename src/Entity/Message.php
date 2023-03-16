<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\CreatePost;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\MessageVoteController;


#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource(
    operations: [
        //        new GetCollection(normalizationContext: ['groups' => ['thread:read']]),
        new Post(denormalizationContext: ['groups' => ['message:write']], security: "is_granted('ROLE_USER')"),
        //        new Get(normalizationContext: ['groups' => ['thread:read']]),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.owner == user"),
        new Patch(
            denormalizationContext: ['groups' => ['message:write']],
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') and object.owner == user"
        )
    ],
)]
#[ApiResource(
    uriTemplate: '/threads/{thread_id}/messages',
    operations: [new GetCollection()],
    uriVariables: [
        'thread_id' => new Link(toProperty: 'thread', fromClass: Thread::class),
    ],
    denormalizationContext: ['groups' => ['message:read']]
)]

#[ApiResource(
    //    operations: [ new Post(security: "is_granted('ROLE_USER')") ],
    //    denormalizationContext: ['groups' => ['request:write']],
    operations: [
        new Post(
            uriTemplate: '/messages/{id}/vote/{type}',
            controller: MessageVoteController::class,
            description: 'Add or downvote a message',
            name: 'Message_Vote_Controller'
        )
    ],
    denormalizationContext: ['groups' => ['request:empty']]
)]

class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['message:read', 'message:list'])]
    private ?int $upvotes = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['message:read', 'message:list'])]
    private ?int $downvotes = 0;



    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['message:write', 'message:read'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['message:read'])]
    private ?bool $hasBeenEdited = null;

    #[Groups(['message:read'])]
    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[Groups(['message:write', 'message:read'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Thread $thread = null;

    #[ORM\Column]
    #[Groups(['message:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['message:read'])]
    private ?\DateTimeInterface $modifiedAt = null;

    public function __construct()
    {
        $this->hasBeenEdited = false;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function getUpvotes(): ?int
    {
        return $this->upvotes;
    }

    public function setUpvotes(int $upvotes): self
    {
        $this->upvotes = $upvotes;

        return $this;
    }

    public function getDownvotes(): ?int
    {
        return $this->downvotes;
    }

    public function setDownvotes(int $downvotes): self
    {
        $this->downvotes = $downvotes;

        return $this;
    }

    public function addUpvote(): self
    {
        $this->upvotes++;

        return $this;
    }

    public function addDownvote(): self
    {
        $this->downvotes++;

        return $this;
    }

    public function removeUpvote(): self
    {
        $this->upvotes--;

        return $this;
    }

    public function removeDownvote(): self
    {
        $this->downvotes--;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
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

    public function isHasBeenEdited(): ?bool
    {
        return $this->hasBeenEdited;
    }

    public function setHasBeenEdited(bool $hasBeenEdited): self
    {
        $this->hasBeenEdited = $hasBeenEdited;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }
}
