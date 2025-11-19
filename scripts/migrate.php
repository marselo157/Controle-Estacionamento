<?php
// scripts/migrate.php
$dbFile = __DIR__ . '/../data/parking.sqlite';
if (!is_dir(dirname($dbFile))) mkdir(dirname($dbFile), 0777, true);
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS vehicles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    plate TEXT NOT NULL,
    type TEXT NOT NULL,
    entry_at TEXT NOT NULL,
    exit_at TEXT,
    paid REAL DEFAULT 0
);");
echo "Banco criado/atualizado em: $dbFile\n";
