<?php
namespace App\Infra\Repository;

use App\Domain\Vehicle;
use PDO;
use DateTimeImmutable;

class VehicleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function add(Vehicle $v): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO vehicles (plate, type, entry_at) VALUES (:plate, :type, :entry_at)');
        $stmt->execute([
            ':plate' => $v->plate,
            ':type' => $v->type,
            ':entry_at' => $v->entryAt->format('c')
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function setExit(int $id, DateTimeImmutable $exit, float $paid): bool
    {
        $stmt = $this->pdo->prepare('UPDATE vehicles SET exit_at = :exit_at, paid = :paid WHERE id = :id');
        return $stmt->execute([
            ':exit_at' => $exit->format('c'),
            ':paid' => $paid,
            ':id' => $id
        ]);
    }

    public function findOpen(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM vehicles WHERE exit_at IS NULL ORDER BY entry_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM vehicles ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM vehicles WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function reportByType(): array
    {
        $stmt = $this->pdo->query('SELECT type, COUNT(*) as total, SUM(paid) as revenue FROM vehicles GROUP BY type');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
