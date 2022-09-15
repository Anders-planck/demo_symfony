<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\Api\CommentCreateController;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    collectionOperations: [
        "get",
        "post" => [
            "controller" => CommentCreateController::class,
            "security" => "is_granted('IS_AUTHENTICATED_FULLY')"
        ]
    ],
    itemOperations: [
        "get" => [
            "normalization_context" =>[
                "groups" => ["read:full:comment","read:comment"]
            ]
        ],
        "put" =>[
            "security" => "is_granted('EDIT_COMMENT',object)",
            "denormalizationContext" => ["groups"=>["update:comment"]]
        ],
        "delete" =>[
            "security" => "is_granted('EDIT_COMMENT',object)"
        ],
    ],
    denormalizationContext: ["groups"=>["create:comment"]],
    normalizationContext: ["groups"=>["read:comment"]],
    order: ["published_at" => "DESC"],
    paginationItemsPerPage: 3
)]
#[ApiFilter(SearchFilter::class,properties: ["post"=>"exact"])]
class Comment
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["read:comment"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "comment.blank")]
    #[Assert\Length(min: 5, max: 1000, minMessage: "comment.too_short", maxMessage: "comment.too_long")]
    #[Groups(["read:comment","create:comment","update:comment"])]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: Post::class, cascade: ["persist"], inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["read:full:comment","create:comment"])]

    private ?Post $post = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["read:comment"])]
    private ?User $author = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["read:comment","create:comment"])]
    private ?\DateTime $published_at = null;

    public function __construct()
    {
        $this->published_at = new \DateTime();
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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->published_at;
    }

    public function setPublishedAt(\DateTime $published_at): self
    {
        $this->published_at = $published_at;

        return $this;
    }
}
