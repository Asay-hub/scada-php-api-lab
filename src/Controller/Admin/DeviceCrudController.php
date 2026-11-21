<?php

namespace App\Controller\Admin;

use App\Entity\Device;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DeviceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Device::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('category', 'Категорія');
        yield TextField::new('name', 'Назва пристрою');
        yield TextField::new('protocol', 'Протокол / інтерфейс');
        yield TextField::new('unit', 'Одиниці вимірювання')->setRequired(false);
    }
}