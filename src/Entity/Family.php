<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FamilyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: FamilyRepository::class)]
#[ORM\Table(name: 'ppi_family')]
class Family implements AuditableInterface
{
    use AuditableTrait;

    final public const RESOURCE_KEY = 'families';
    final public const FORM_KEY = 'family_details';
    final public const LIST_KEY = 'families';
    final public const SECURITY_CONTEXT = 'sulu.family.families';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 31)]
    private string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
