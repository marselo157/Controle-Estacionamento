<?php
namespace App\Domain;

class Vehicle
{
    public function __construct(
        public ?int $id,
        public string $plate,
        public string $type,
        public \DateTimeImmutable $entryAt,
        public ?\DateTimeImmutable $exitAt = null,
        public float $paid = 0.0
    ) {}
}
