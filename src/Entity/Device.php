<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Вкажіть назву пристрою (наприклад, Pt100 Sensor).')]
    #[Assert\Length(min: 2, max: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Вкажіть протокол або інтерфейс (наприклад, Modbus TCP, 4-20mA).')]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $protocol = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $unit = null;

    #[ORM\ManyToOne(inversedBy: 'devices')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Оберіть категорію пристрою.')]
    private ?Category $category = null;

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getProtocol(): ?string { return $this->protocol; }
    public function setProtocol(string $protocol): static { $this->protocol = $protocol; return $this; }

    public function getUnit(): ?string { return $this->unit; }
    public function setUnit(?string $unit): static { $this->unit = $unit; return $this; }

    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): static { $this->category = $category; return $this; }
}