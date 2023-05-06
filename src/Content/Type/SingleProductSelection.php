<?php

declare(strict_types=1);

namespace App\Content\Type;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class SingleProductSelection extends SimpleContentType
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct('single_product_selection', null);
    }

    public function getContentData(PropertyInterface $property): ?Product
    {
        $id = $property->getValue();

        if (empty($id)) {
            return null;
        }

        return $this->entityManager->getRepository(Product::class)->find($id);
    }

    /**
     * @return array<string, int|null>
     */
    public function getViewData(PropertyInterface $property): array
    {
        return [
            'id' => $property->getValue(),
        ];
    }
}
