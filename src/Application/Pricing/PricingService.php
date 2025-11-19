<?php
namespace App\Application\Pricing;

use DateTimeImmutable;

class PricingService
{
    // tarifário por tipo (pode ser estendido/configurado)
    private array $rates = [
        'carro' => 5.0,
        'moto' => 3.0,
        'caminhao' => 10.0
    ];

    public function calculate(string $type, DateTimeImmutable $entry, DateTimeImmutable $exit): float
    {
        $seconds = $exit->getTimestamp() - $entry->getTimestamp();
        $hours = (int)ceil($seconds / 3600);
        $rate = $this->rates[$type] ?? throw new \InvalidArgumentException('Tipo inválido');
        return $hours * $rate;
    }
}
