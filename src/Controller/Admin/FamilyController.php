<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Family;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type FamilyData array{
 *     id: int|null,
 *     name: string,
 * }
 */
class FamilyController extends AbstractController implements SecuredControllerInterface
{
    public function __construct(
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(path: '/admin/api/families/{id}', name: 'app.get_family', methods: ['GET'])]
    public function getAction(int $id): Response
    {
        $family = $this->entityManager->getRepository(Family::class)->find($id);
        if (!$family instanceof Family) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->getDataForEntity($family));
    }

    #[Route(path: '/admin/api/families/{id}', name: 'app.put_family', methods: ['PUT'])]
    public function putAction(Request $request, int $id): Response
    {
        $family = $this->entityManager->getRepository(Family::class)->find($id);
        if (!$family instanceof Family) {
            throw new NotFoundHttpException();
        }

        /** @var FamilyData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $family);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($family));
    }

    #[Route(path: '/admin/api/families', name: 'app.post_family', methods: ['POST'])]
    public function postAction(Request $request): Response
    {
        $family = new Family();

        /** @var FamilyData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $family);
        $this->entityManager->persist($family);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($family), 201);
    }

    /**
     * @throws ORMException
     */
    #[Route(path: '/admin/api/family/{id}', name: 'app.delete_family', methods: ['DELETE'])]
    public function deleteAction(int $id): Response
    {
        $family = $this->entityManager->getReference(Family::class, $id);

        if (!$family instanceof Family) {
            throw new NotFoundHttpException();
        }

        $this->entityManager->remove($family);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    #[Route(path: '/admin/api/family', name: 'app.get_family_list', methods: ['GET'])]
    public function getListAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Family::RESOURCE_KEY,
        );

        return $this->json($listRepresentation->toArray());
    }

    /**
     * @return FamilyData $data
     */
    protected function getDataForEntity(Family $entity): array
    {
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
        ];
    }

    /**
     * @param FamilyData $data
     */
    protected function mapDataToEntity(array $data, Family $entity): void
    {
        $entity->setName($data['name']);
    }

    public function getSecurityContext(): string
    {
        return Family::SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): ?string
    {
        return $request->query->get('locale');
    }
}
