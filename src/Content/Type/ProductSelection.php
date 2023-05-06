<?php

declare(strict_types=1);

namespace App\Content\Type;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class ProductSelection extends SimpleContentType
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct('product_selection', []);
    }

    /**
     * @return Product[]
     */
    public function getContentData(PropertyInterface $property): array
    {
        $ids = $property->getValue();

        if (empty($ids)) {
            return [];
        }

        $products = $this->entityManager->getRepository(Product::class)->findBy(['id' => $ids]);

        $idPositions = \array_flip($ids);
        \usort($products, fn (Product $a, Product $b) => $idPositions[$a->getId()] - $idPositions[$b->getId()]);

        return $products;
    }

    /**
     * @return array<string, array<int>|null>
     */
    public function getViewData(PropertyInterface $property): array
    {
        return [
            'ids' => $property->getValue(),
        ];
    }
}
