<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"user_read"}}
 *          },
 *          "post"
 *      },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"user_details_read"}}
 *          },
 *          "put",
 *          "patch",
 *          "delete"
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"email": "partial"})
 * @ApiFilter(DateFilter::class, properties={"createdAt"})
 * @ApiFilter(BooleanFilter::class, properties={"status"})
 * @ApiFilter(NumericFilter::class, properties={"age"})
 * @ApiFilter(RangeFilter::class, properties={"age"})
 * @ApiFilter(ExistsFilter::class, properties={"updatedAt"})
 * @ApiFilter(OrderFilter::class, properties={"id"}, arguments={"orderParameterName"="order"})
 * @UniqueEntity("email", message="Cette email est deja utilisé")
 */
class User implements UserInterface
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user_read", "user_details_read", "article_details_read"})
     * @Assert\NotBlank(message="L'email est obligatoire")
     * @Assert\Email(message="email format invalide")
     */
    private string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Le Mot de Passe est obligatoire")
     */
    private string $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="author", orphanRemoval=true)
     * @Groups({"user_details_read"})
     */
    private Collection $articles;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user_read", "user_details_read", "article_details_read"})
     */
    private bool $status;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user_read", "user_details_read", "article_details_read"})
     */
    private int $age;

    public function __construct()
    {
        $this->articles = new ArrayCollection();

        $this->createdAt = new \DateTime();

        $this->status = true;

        $this->age = 18;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
        }

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }
}
