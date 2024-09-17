<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\ContactRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContactRequestRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(),
        new Get(security: "is_granted('ROLE_ADMIN') or object.getClient() == user"),
        new Put(security: "is_granted('ROLE_ADMIN') or object.getClient() == user"),
        new Patch(security: "is_granted('ROLE_ADMIN') or object.getClient() == user"),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.getClient() == user"),
    ],
    normalizationContext: ['groups' => ['contact_request:read']],
    denormalizationContext: ['groups' => ['contact_request:write']]
)]
class ContactRequest
{
    #[Groups(['contact_request:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['contact_request:read', 'contact_request:write'])]
    #[ORM\ManyToOne(inversedBy: 'contactRequests')]
    private ?User $client = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['contact_request:read', 'contact_request:write'])]
    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['contact_request:read', 'contact_request:write'])]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Groups(['contact_request:read', 'contact_request:write'])]
    #[ORM\Column(length: 20)]
    private ?string $phone_number = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['contact_request:read', 'contact_request:write'])]
    #[ORM\Column(length: 255)]
    private ?string $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): static
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
