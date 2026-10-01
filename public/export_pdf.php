<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use App\Database;

$pdo = Database::getConnection();
$stmt = $pdo->query("SELECT d.name, d.protocol, c.code as category FROM devices d JOIN categories c ON c.id = d.category_id");
$devices = $stmt->fetchAll();

$html = '
    <html lang="uk">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: DejaVu Sans, sans-serif; color: #333; margin: 30px; }
            h2 { color: #2c3e50; border-bottom: 2px solid #0d6efd; padding-bottom: 8px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; font-size: 14px; }
            th { background-color: #f8f9fa; color: #495057; }
            .footer { margin-top: 30px; font-size: 12px; color: #6c757d; text-align: right; }
        </style>
    </head>
    <body>
        <h2>🎛️ Звіт: Реєстр промислового обладнання SCADA</h2>
        <p>Дата формування звіту: ' . date('Y-m-d H:i:s') . '</p>
        <table>
            <tr>
                <th>Назва пристрою</th>
                <th>Категорія</th>
                <th>Промисловий інтерфейс</th>
            </tr>';

foreach ($devices as $d) {
    $html .= "<tr>
                <td><strong>{$d['name']}</strong></td>
                <td>{$d['category']}</td>
                <td>{$d['protocol']}</td>
              </tr>";
}

$html .= '</table>
        <div class="footer">Система диспетчеризації та автоматизації</div>
    </body>
    </html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("scada_devices_report.pdf", ["Attachment" => true]);