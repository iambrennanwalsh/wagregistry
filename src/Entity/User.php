<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(
  fields: 'email',
  message: "That email address is already in use."
)]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{

  #[ORM\Column(length: 180, unique: true)]
  #[Assert\Email, Assert\NotBlank, Assert\Length(max: 255)]
  private string $email;

  #[ORM\Column(type: 'string')]
  #[Assert\NotBlank, Assert\Length(max: 255)]
  private string $name;

  #[ORM\Column(type: 'string', nullable: true)]
  private ?string $gravatar;

  #[ORM\Column(type: 'json')]
  private array $roles = [];

  #[ORM\Column(type: 'string')]
  #[Assert\NotBlank, Assert\Length(max: 255)]
  private string $password;

  #[ORM\Column(type: 'boolean')]
  private bool $emailConfirmation = false;

  #[ORM\Column(type: 'string')]
  #[Assert\NotBlank]
  private string $resetPasswordToken;

  #[ORM\Column(type: 'string')]
  #[Assert\NotBlank]
  private string $emailConfirmationToken;

  #[ORM\OneToMany(targetEntity: SupportTicket::class, mappedBy: "user", cascade: ["persist"])]
  private Collection $supportTickets;

  #[ORM\Column(type: 'string', nullable: true)]
  private ?string $facebookId;

  #[ORM\Column(type: 'string', nullable: true)]
  private ?string $googleId;

  #[ORM\Column(type: 'string', nullable: true)]
  private ?string $appleId;

  public function __construct(array $args = [])
  {
    $this->setEmailConfirmationToken();
    $this->setResetPasswordToken();
    $this->supportTickets = new ArrayCollection();
    parent::__construct($args);
  }

  public function getName(): string
  {
    return $this->name;
  }
  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function getEmail(): string
  {
    return $this->email;
  }
  public function setEmail(string $email): void
  {
    $this->email = $email;
    $this->setEmailConfirmation(false);
    $this->setEmailConfirmationToken();
    $this->setGravatar($email);
  }

  public function getGravatar(): ?string
  {
    return $this->gravatar;
  }
  public function setGravatar(string $email): void
  {
    $this->gravatar =
      'https://www.gravatar.com/avatar/' . md5($email) . '?s=500';
  }

  public function getRoles(): array
  {
    $roles = $this->roles;
    $roles[] = 'ROLE_USER';
    return array_unique($roles);
  }
  public function setRoles(array $roles): void
  {
    $this->roles = $roles;
  }

  public function getPassword(): string
  {
    return $this->password;
  }
  public function setPassword(string $password): void
  {
    $this->password = $password;
  }

  public function getResetPasswordToken(): ?string
  {
    return $this->resetPasswordToken;
  }
  public function setResetPasswordToken(): void
  {
    $this->resetPasswordToken = bin2hex(random_bytes(10));
  }

  public function getEmailConfirmation(): bool
  {
    return $this->emailConfirmation;
  }
  public function setEmailConfirmation(bool $emailConfirmation): void
  {
    $this->emailConfirmation = $emailConfirmation;
  }

  public function getEmailConfirmationToken(): ?string
  {
    return $this->emailConfirmationToken;
  }
  public function setEmailConfirmationToken(): void
  {
    $this->emailConfirmationToken = bin2hex(random_bytes(10));
  }

  public function getSupportTickets(): Collection
  {
    return $this->supportTickets;
  }

  public function addSupportTicket(SupportTicket $ticket): void
  {
    if (!$this->supportTickets->contains($ticket)) {
      $this->supportTickets->add($ticket);
    }
  }

  public function removeSupportTicket(SupportTicket $ticket): void
  {
    $this->supportTickets->removeElement($ticket);
  }

  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  public function eraseCredentials(): void
  {
  }

  public function getGoogleId(): string
  {
    return $this->googleId;
  }

  public function setGoogleId(string $googleId): self
  {
    $this->googleId = $googleId;
    return $this;
  }

  public function getFacebookId(): string
  {
    return $this->facebookId;
  }

  public function setFacebookId(string $facebookId): self
  {
    $this->facebookId = $facebookId;
    return $this;
  }

  public function getAppleId(): string
  {
    return $this->appleId;
  }

  public function setAppleId(string $appleId): self
  {
    $this->appleId = $appleId;
    return $this;
  }
}
