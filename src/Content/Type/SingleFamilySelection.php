<?php

declare(strict_types=1);

namespace App\Content\Type;

use App\Entity\Family;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class SingleFamilySelection extends SimpleContentType
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct('single_family_selection', null);
    }

    public function getContentData(PropertyInterface $property): ?Family
    {
        $id = $property->getValue();
        if (empty($id)) {
            return null;
        }

        return $this->entityManager->getRepository(Family::class)->find($id);
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
