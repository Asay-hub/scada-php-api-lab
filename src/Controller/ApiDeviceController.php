<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\DeviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ApiDeviceController extends AbstractController
{
    private const JSON_OPTS = ['json_encode_options' => JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT];

    #[Route('/categories', name: 'api_categories', methods: ['GET'])]
    public function categories(CategoryRepository $categoryRepo): JsonResponse
    {
        $codes = array_map(
            fn($cat) => ['code' => $cat->getCode(), 'title' => $cat->getTitle()],
            $categoryRepo->findAllOrdered()
        );

        return $this->json([
            'categories' => $codes,
            'hint' => 'додайте /api/devices/Temperature або ?q=Pt100',
        ], 200, [], self::JSON_OPTS);
    }

    #[Route('/devices', name: 'api_devices_all', methods: ['GET'])]
    public function index(Request $request, DeviceRepository $deviceRepo): JsonResponse
    {
        $category = $request->query->get('category');
        $search = $request->query->get('q');

        $devices = $deviceRepo->findByFilter($category, $search);

        $data = array_map(fn($d) => [
            'id' => $d->getId(),
            'category' => $d->getCategory()->getCode(),
            'name' => $d->getName(),
            'protocol' => $d->getProtocol(),
            'unit' => $d->getUnit(),
        ], $devices);

        return $this->json($data, 200, [], self::JSON_OPTS);
    }

    #[Route('/devices/{code}', name: 'api_devices_by_category', methods: ['GET'])]
    public function byCategory(string $code, CategoryRepository $categoryRepo, DeviceRepository $deviceRepo): JsonResponse
    {
        $category = $categoryRepo->findOneBy(['code' => $code]);

        if (!$category) {
            return $this->json(
                ['error' => "Категорію «{$code}» не знайдено"],
                404,
                [],
                self::JSON_OPTS
            );
        }

        $devices = $deviceRepo->findByFilter($code);
        $items = array_map(fn($d) => [
            'id' => $d->getId(),
            'name' => $d->getName(),
            'protocol' => $d->getProtocol(),
            'unit' => $d->getUnit(),
        ], $devices);

        return $this->json([
            'category' => $category->getCode(),
            'title' => $category->getTitle(),
            'devices' => $items,
        ], 200, [], self::JSON_OPTS);
    }
}