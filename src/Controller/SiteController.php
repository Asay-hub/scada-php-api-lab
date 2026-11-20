<?php

namespace App\Controller;

use App\Entity\Device;
use App\Repository\CategoryRepository;
use App\Repository\DeviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SiteController extends AbstractController
{
    #[Route('/', name: 'site_index', methods: ['GET'])]
    public function index(
        Request $request,
        CategoryRepository $categoryRepo,
        DeviceRepository $deviceRepo
    ): Response {
        $selectedCategory = $request->query->get('category');
        $search = $request->query->get('q');

        return $this->render('site/index.html.twig', [
            'categories' => $categoryRepo->findAllOrdered(),
            'devices' => $deviceRepo->findByFilter($selectedCategory, $search),
            'selectedCategory' => $selectedCategory,
            'search' => $search,
        ]);
    }

    #[Route('/category/{code}', name: 'site_category', methods: ['GET'])]
    public function category(
        string $code,
        CategoryRepository $categoryRepo,
        DeviceRepository $deviceRepo
    ): Response {
        $category = $categoryRepo->findOneBy(['code' => $code]);
        if (!$category) {
            throw $this->createNotFoundException("Категорію «{$code}» не знайдено.");
        }

        return $this->render('site/category.html.twig', [
            'category' => $category,
            'devices' => $deviceRepo->findByFilter($code),
        ]);
    }

    #[Route('/device/{id}', name: 'site_device', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function device(Device $device): Response
    {
        return $this->render('site/device.html.twig', [
            'device' => $device,
        ]);
    }
}