<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'ppi_product')]
class Product implements AuditableInterface
{
    use AuditableTrait;

    final public const RESOURCE_KEY = 'products';
    final public const FORM_KEY = 'product_details';
    final public const LIST_KEY = 'products';
    final public const SECURITY_CONTEXT = 'sulu.product.products';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 31)]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    /**
     * @var mixed[]
     */
    #[ORM\Column(type: Types::JSON)]
    private array $attributes = [];

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param mixed[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }
}
