<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_category_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SCADA Адмін-панель');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Головна', 'fa fa-home');
        yield MenuItem::section('Обладнання SCADA');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Категорії', 'fa fa-folder');
        yield MenuItem::linkTo(DeviceCrudController::class, 'Пристрої', 'fa fa-microchip');
        yield MenuItem::section('Навігація');
        yield MenuItem::linkToUrl('На сайт', 'fa fa-globe', '/');
    }
}