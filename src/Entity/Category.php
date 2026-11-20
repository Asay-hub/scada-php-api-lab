<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'Категорія з таким кодом уже існує.')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, unique: true)]
    #[Assert\NotBlank(message: 'Вкажіть код категорії (наприклад, Temperature).')]
    #[Assert\Length(min: 2, max: 30)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Вкажіть назву категорії.')]
    #[Assert\Length(min: 2, max: 100)]
    private ?string $title = null;

    /**
     * @var Collection<int, Device>
     */
    #[ORM\OneToMany(targetEntity: Device::class, mappedBy: 'category', orphanRemoval: true)]
    private Collection $devices;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->code ? "{$this->code} ({$this->title})" : 'Категорія';
    }

    public function getId(): ?int { return $this->id; }

    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    /** @return Collection<int, Device> */
    public function getDevices(): Collection { return $this->devices; }

    public function addDevice(Device $device): static
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
            $device->setCategory($this);
        }
        return $this;
    }

    public function removeDevice(Device $device): static
    {
        if ($this->devices->removeElement($device)) {
            if ($device->getCategory() === $this) {
                $device->setCategory(null);
            }
        }
        return $this;
    }
}