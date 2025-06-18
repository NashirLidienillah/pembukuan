<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi</title>
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Laporan Laba Rugi (<?= date('F', mktime(0, 0, 0, $month, 1)) ?> <?= $year ?>)</h1>
        <div class="table-container">
            <table>
                <tbody>
                    <tr>
                        <td>Penjualan Bersih</td>
                        <td>Rp <?= number_format($report['net_sales'], 2) ?></td>
                    </tr>
                    <tr>
                        <td>Harga Pokok Penjualan (HPP)</td>
                        <td>Rp <?= number_format($report['cogs'], 2) ?></td>
                    </tr>
                    <tr>
                        <td>Laba Kotor</td>
                        <td>Rp <?= number_format($report['gross_profit'], 2) ?></td>
                    </tr>
                    <tr>
                        <td>Biaya Operasional</td>
                        <td>Rp <?= number_format($report['operating_expenses'], 2) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Laba/Rugi Bersih</strong></td>
                        <td><strong>Rp <?= number_format($report['net_income'], 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
            <a href="/" class="back-link">Kembali</a>
        </div>
    </div>
</body>
</html>