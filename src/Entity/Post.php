<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 *@Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["read:full:comment"])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ["default" => true])]
    private ?bool $posted = true;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $image = null;

    /**
     * @Vich\UploadableField(mapping="posts_images", fileNameProperty="image")
     * @Assert\Image
     */
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $comments;


    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }


    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isPosted(): ?bool
    {
        return $this->posted;
    }

    public function setPosted(bool $posted): self
    {
        $this->posted = $posted;

        return $this;
    }

    public function getSlug(): String
    {
        return (new Slugify())->slugify($this->title);
    }

    /**
     * @return string|null
     */
    public function getImage(): string|null
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     */
    public function setImage(string $image = null): void
    {

        $this->image = $image;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): File | null
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     */
    public function setImageFile(File $imageFile=null): void
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            # update the value of updated_at for persist data in db because image only is not sufficient
            $this->updated_at = new DateTimeImmutable('now');
        }
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $update_at): self
    {
        $this->updated_at = $update_at;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

   


}
