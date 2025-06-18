<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Umum</title>
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Jurnal Umum</h1>

        <div class="filter-form">
            <form method="get" action="/">
                <div class="form-group">
                    <label>Bulan</label>
                    <select name="month">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= sprintf('%02d', $m) ?>" <?= $month == sprintf('%02d', $m) ? 'selected' : '' ?>>
                                <?= sprintf('%02d', $m) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="year" value="<?= $year ?>" min="2000" max="2100">
                </div>
                <button type="submit">Filter</button>
            </form>
            <a href="/export-excel?month=<?= $month ?>&year=<?= $year ?>" class="btn btn-success">Ekspor ke Excel</a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form action="/add-transaction" method="post" class="transaction-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Tanggal <span class="tooltip">(Pilih tanggal transaksi)</span></label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label>Jumlah (Rp) <span class="tooltip">(Masukkan jumlah tanpa Rp atau koma)</span></label>
                    <input type="number" name="amount" step="0.01" min="0" placeholder="Contoh: 1000000" required>
                </div>
                <div class="form-group full-width">
                    <label>Deskripsi <span class="tooltip">(Jelaskan transaksi, misal: Pembelian barang dagang)</span></label>
                    <input type="text" name="description" placeholder="Contoh: Pembelian barang dari PT ABC" required minlength="5">
                </div>
                <div class="form-group">
                    <label>Akun Debit <span class="tooltip">(Akun yang didebit, misal: Kas, Pembelian)</span></label>
                    <select name="debit_account_id" required>
                        <option value="">Pilih Akun Debit</option>
                        <?php foreach ($accounts as $account): ?>
                            <option value="<?= esc($account['id']) ?>">
                                <?= esc($account['account_name']) ?> (<?= esc($account['account_type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Akun Kredit <span class="tooltip">(Akun yang dikredit, misal: Utang Dagang, Penjualan)</span></label>
                    <select name="credit_account_id" required>
                        <option value="">Pilih Akun Kredit</option>
                        <?php foreach ($accounts as $account): ?>
                            <option value="<?= esc($account['id']) ?>">
                                <?= esc($account['account_name']) ?> (<?= esc($account['account_type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <button type="submit">Tambah Transaksi</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Akun Debit</th>
                        <th>Akun Kredit</th>
                        <th>Jumlah (Rp)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="6">Tidak ada transaksi untuk bulan dan tahun ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($t['date'])) ?></td>
                                <td><?= esc($t['description']) ?></td>
                                <td>
                                    <?= esc(array_column($accounts, 'account_name', 'id')[$t['debit_account_id']] ?? 'Unknown') ?>
                                </td>
                                <td>
                                    <?= esc(array_column($accounts, 'account_name', 'id')[$t['credit_account_id']] ?? 'Unknown') ?>
                                </td>
                                <td><?= number_format($t['amount'], 2, ',', '.') ?></td>
                                <td>
                                    <a href="/edit-transaction/<?= $t['id'] ?>" class="btn btn-edit">Edit</a>
                                    <a href="/delete-transaction/<?= $t['id'] ?>" class="btn btn-delete" onclick="return confirm('Hapus transaksi ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="report-links">
            <a href="/generate-income-statement?month=<?= $month ?>&year=<?= $year ?>" class="btn btn-report">Laporan Laba Rugi</a>
            <a href="/generate-balance-sheet?month=<?= $month ?>&year=<?= $year ?>" class="btn btn-report">Neraca</a>
        </div>
    </div>
</body>
</html>