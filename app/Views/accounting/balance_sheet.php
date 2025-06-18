<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neraca</title>
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Neraca (<?= date('F', mktime(0, 0, 0, $month, 1)) ?> <?= $year ?>)</h1>
        <div class="table-container">
            <h2>Aktiva</h2>
            <table>
                <tbody>
                    <?php foreach ($report['assets'] as $asset): ?>
                        <tr>
                            <td><?= $asset['name'] ?></td>
                            <td>Rp <?= number_format($asset['balance'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total Aktiva</strong></td>
                        <td><strong>Rp <?= number_format($report['total_assets'], 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
            <h2>Pasiva</h2>
            <table>
                <tbody>
                    <?php foreach ($report['liabilities'] as $liability): ?>
                        <tr>
                            <td><?= $liability['name'] ?></td>
                            <td>Rp <?= number_format($liability['balance'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php foreach ($report['equity'] as $equity): ?>
                        <tr>
                            <td><?= $equity['name'] ?></td>
                            <td>Rp <?= number_format($equity['balance'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total Pasiva</strong></td>
                        <td><strong>Rp <?= number_format($report['total_liabilities'] + $report['total_equity'], 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
            <a href="/" class="back-link">Kembali</a>
        </div>
    </div>
</body>
</html>