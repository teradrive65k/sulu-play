<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type ProductData array{
 *     id: int|null,
 *     name: string,
 *     description: string,
 *     attributes: mixed[],
 * }
 */
class ProductController extends AbstractController implements SecuredControllerInterface
{
    public function __construct(
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(path: '/admin/api/products/{id}', name: 'app.get_product', methods: ['GET'])]
    public function getAction(int $id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->getDataForEntity($product));
    }

    #[Route(path: '/admin/api/products/{id}', name: 'app.put_product', methods: ['PUT'])]
    public function putAction(Request $request, int $id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException();
        }

        /** @var ProductData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $product);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($product));
    }

    #[Route(path: '/admin/api/products', name: 'app.post_product', methods: ['POST'])]
    public function postAction(Request $request): Response
    {
        $product = new Product();

        /** @var ProductData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $product);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($product), 201);
    }

    /**
     * @throws ORMException
     */
    #[Route(path: '/admin/api/products/{id}', name: 'app.delete_product', methods: ['DELETE'])]
    public function deleteAction(int $id): Response
    {
        $product = $this->entityManager->getReference(Product::class, $id);

        if (!$product instanceof Product) {
            throw new NotFoundHttpException();
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    #[Route(path: '/admin/api/products', name: 'app.get_product_list', methods: ['GET'])]
    public function getListAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Product::RESOURCE_KEY,
        );

        return $this->json($listRepresentation->toArray());
    }

    /**
     * @return ProductData $data
     */
    protected function getDataForEntity(Product $entity): array
    {
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'attributes' => $entity->getAttributes(),
        ];
    }

    /**
     * @param ProductData $data
     */
    protected function mapDataToEntity(array $data, Product $entity): void
    {
        $entity->setName($data['name']);
        $entity->setDescription($data['description']);
        $entity->setAttributes($data['attributes']);
    }

    public function getSecurityContext(): string
    {
        return Product::SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): ?string
    {
        return $request->query->get('locale');
    }
}
