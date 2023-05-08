<?php

declare(strict_types=1);

namespace App\Content\Type;

use App\Entity\Family;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class FamilySelection extends SimpleContentType
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct('family_selection', []);
    }

    /**
     * @return Family[]
     */
    public function getContentData(PropertyInterface $property): array
    {
        $ids = $property->getValue();

        if (empty($ids)) {
            return [];
        }

        $families = $this->entityManager->getRepository(Family::class)->findBy(['id' => $ids]);

        $idPositions = \array_flip($ids);
        \usort($families, fn (Family $a, Family $b) => $idPositions[$a->getId()] - $idPositions[$b->getId()]);

        return $families;
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
