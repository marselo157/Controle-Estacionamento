<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Infra\Repository\VehicleRepository;
use App\Application\Pricing\PricingService;

$dbFile = __DIR__ . '/../data/parking.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$repo = new VehicleRepository($pdo);
$pricing = new PricingService();

$action = $_POST['action'] ?? $_GET['action'] ?? null;

if ($action === 'enter') {
    $plate = strtoupper(trim($_POST['plate'] ?? ''));
    $type = strtolower(trim($_POST['type'] ?? 'carro'));
    $entry = new DateTimeImmutable();
    $id = $repo->add(new \App\Domain\Vehicle(null, $plate, $type, $entry));
    header('Location: /?msg=entered&id=' . $id);
    exit;
}

if ($action === 'exit') {
    $id = (int)($_POST['id'] ?? 0);
    $row = $repo->findById($id);
    if (!$row) {
        header('Location: /?err=notfound');
        exit;
    }
    $entry = new DateTimeImmutable($row['entry_at']);
    $exit = new DateTimeImmutable();
    $paid = $pricing->calculate($row['type'], $entry, $exit);
    $repo->setExit($id, $exit, $paid);
    header('Location: /?msg=exited&id=' . $id);
    exit;
}

$open = $repo->findOpen();
$all = $repo->findAll();
$report = $repo->reportByType();

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Controle de Estacionamento</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:Arial,Helvetica,sans-serif;padding:20px;max-width:900px;margin:auto}
    form{margin-bottom:1rem}
    table{width:100%;border-collapse:collapse}
    th,td{border:1px solid #ddd;padding:8px;text-align:left}
    th{background:#f4f4f4}
    .small{font-size:0.9rem;color:#555}
  </style>
</head>
<body>
  <h1>Controle de Estacionamento</h1>

  <h2>Registrar entrada</h2>
  <form method="post">
    <input type="hidden" name="action" value="enter">
    Placa: <input name="plate" required>
    Tipo:
    <select name="type">
      <option value="carro">Carro</option>
      <option value="moto">Moto</option>
      <option value="caminhao">Caminhão</option>
    </select>
    <button type="submit">Registrar</button>
  </form>

  <h2>Veículos em aberto</h2>
  <?php if(count($open)===0): ?>
    <p class="small">Nenhum veículo em aberto.</p>
  <?php else: ?>
    <table><tr><th>ID</th><th>Placa</th><th>Tipo</th><th>Entrada</th><th>Ação</th></tr>
    <?php foreach($open as $r): ?>
      <tr>
        <td><?=h($r['id'])?></td>
        <td><?=h($r['plate'])?></td>
        <td><?=h($r['type'])?></td>
        <td><?=h($r['entry_at'])?></td>
        <td>
          <form method="post" style="display:inline">
            <input type="hidden" name="action" value="exit">
            <input type="hidden" name="id" value="<?=h($r['id'])?>">
            <button type="submit">Registrar saída</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <h2>Relatório (tudo)</h2>
  <table>
    <tr><th>ID</th><th>Placa</th><th>Tipo</th><th>Entrada</th><th>Saída</th><th>Pago (R$)</th></tr>
    <?php foreach($all as $r): ?>
      <tr>
        <td><?=h($r['id'])?></td>
        <td><?=h($r['plate'])?></td>
        <td><?=h($r['type'])?></td>
        <td><?=h($r['entry_at'])?></td>
        <td><?=h($r['exit_at'] ?? '-')?></td>
        <td><?=number_format($r['paid'] ?? 0,2,',','.')?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h2>Resumo por tipo</h2>
  <table>
    <tr><th>Tipo</th><th>Total</th><th>Faturamento (R$)</th></tr>
    <?php foreach($report as $r): ?>
      <tr><td><?=h($r['type'])?></td><td><?=h($r['total'])?></td><td><?=number_format($r['revenue'] ?? 0,2,',','.')?></td></tr>
    <?php endforeach; ?>
  </table>

  <p class="small">Obs: tarifas fixas em `src/Application/Pricing/PricingService.php`. Tempo arredondado para cima (horas).</p>
</body>
</html>
