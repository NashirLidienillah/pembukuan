<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi Jurnal Umum</title>
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Edit Transaksi Jurnal Umum</h1>

        <div class="form-container">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error') || isset($error)): ?>
                <div class="alert-error">
                    <?= session()->getFlashdata('error') ?: $error ?>
                </div>
            <?php endif; ?>
            <form id="edit-transaction-form" action="/edit-transaction/<?= esc($transaction['id']) ?>" method="post" class="transaction-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Tanggal <span class="tooltip">(Pilih tanggal transaksi)</span></label>
                    <input type="date" name="date" value="<?= old('date', esc($transaction['date'])) ?>" required>
                    <?php if (isset($validation) && $validation->hasError('date')): ?>
                        <span class="alert-error" style="font-size: 0.9em; margin-top: 5px;"><?= $validation->getError('date') ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Jumlah (Rp) <span class="tooltip">(Masukkan jumlah tanpa Rp atau koma)</span></label>
                    <input type="number" name="amount" step="0.01" min="0" value="<?= old('amount', esc($transaction['amount'])) ?>" placeholder="Contoh: 1000000" required>
                    <?php if (isset($validation) && $validation->hasError('amount')): ?>
                        <span class="alert-error" style="font-size: 0.9em; margin-top: 5px;"><?= $validation->getError('amount') ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group full-width">
                    <label>Deskripsi <span class="tooltip">(Jelaskan transaksi, misal: Pembelian barang dagang)</span></label>
                    <input type="text" name="description" value="<?= old('description', esc($transaction['description'])) ?>" placeholder="Contoh: Pembelian barang dari PT ABC" required minlength="5">
                    <?php if (isset($validation) && $validation->hasError('description')): ?>
                        <span class="alert-error" style="font-size: 0.9em; margin-top: 5px;"><?= $validation->getError('description') ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Akun Debit <span class="tooltip">(Akun yang didebit, misal: Kas, Pembelian)</span></label>
                    <select name="debit_account_id" required>
                        <option value="">Pilih Akun Debit</option>
                        <?php foreach ($accounts as $account): ?>
                            <option value="<?= esc($account['id']) ?>" <?= old('debit_account_id', $transaction['debit_account_id']) == $account['id'] ? 'selected' : '' ?>>
                                <?= esc($account['account_name']) ?> (<?= esc($account['account_type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($validation) && $validation->hasError('debit_account_id')): ?>
                        <span class="alert-error" style="font-size: 0.9em; margin-top: 5px;"><?= $validation->getError('debit_account_id') ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Akun Kredit <span class="tooltip">(Akun yang dikredit, misal: Utang Dagang, Penjualan)</span></label>
                    <select name="credit_account_id" required>
                        <option value="">Pilih Akun Kredit</option>
                        <?php foreach ($accounts as $account): ?>
                            <option value="<?= esc($account['id']) ?>" <?= old('credit_account_id', $transaction['credit_account_id']) == $account['id'] ? 'selected' : '' ?>>
                                <?= esc($account['account_name']) ?> (<?= esc($account['account_type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($validation) && $validation->hasError('credit_account_id')): ?>
                        <span class="alert-error" style="font-size: 0.9em; margin-top: 5px;"><?= $validation->getError('credit_account_id') ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group full-width">
                    <button type="submit">Simpan Perubahan</button>
                </div>
            </form>
            <a href="/" class="back-link">Kembali</a>
        </div>
    </div>
    <script>
        // Debugging form submission
        document.getElementById('edit-transaction-form').addEventListener('submit', function(event) {
            const formData = new FormData(this);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
        });
    </script>
</body>
</html>