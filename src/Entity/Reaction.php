<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Enum\ReactionEnum;
use App\Repository\ReactionRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ReactionRepository::class)]
class Reaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private ReactionEnum $type;

    #[ORM\ManyToOne(inversedBy: 'authoredReactions')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'reactions')]
    private ?User $user = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getType(): ReactionEnum
    {
        return $this->type;
    }

    public function setType(ReactionEnum $type): self
    {
        $this->type = $type;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
