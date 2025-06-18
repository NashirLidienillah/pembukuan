<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\TransactionModel;
use App\Models\SpecialJournalModel;
use App\Models\FinancialReportModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Accounting extends BaseController
{
    protected $accountModel;
    protected $transactionModel;
    protected $specialJournalModel;
    protected $financialReportModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
        $this->transactionModel = new TransactionModel();
        $this->specialJournalModel = new SpecialJournalModel();
        $this->financialReportModel = new FinancialReportModel();
    }

    public function index()
    {
        $month = $this->request->getGet('month') ?? date('m');
        $year = $this->request->getGet('year') ?? date('Y');
        $data = [
            'accounts' => $this->accountModel->findAll(),
            'transactions' => $this->transactionModel
                ->where('MONTH(date)', $month)
                ->where('YEAR(date)', $year)
                ->findAll(),
            'month' => $month,
            'year' => $year
        ];
        log_message('debug', 'Rendering index view');
        return view('accounting/index', $data);
    }

    public function addTransaction()
    {
        $data = $this->request->getPost();
        $validation = \Config\Services::validation();
        $validation->setRules([
            'date' => 'required|valid_date',
            'description' => 'required|min_length[5]',
            'debit_account_id' => [
                'label' => 'Akun Debit',
                'rules' => 'required|numeric|is_not_unique[accounts.id]',
                'errors' => [
                    'is_not_unique' => 'Akun Debit tidak valid.'
                ]
            ],
            'credit_account_id' => [
                'label' => 'Akun Kredit',
                'rules' => 'required|numeric|is_not_unique[accounts.id]',
                'errors' => [
                    'is_not_unique' => 'Akun Kredit tidak valid.'
                ]
            ],
            'amount' => 'required|decimal|greater_than[0]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            log_message('error', 'Validation failed: ' . implode(', ', $validation->getErrors()));
            return redirect()->back()->with('error', implode('<br>', $validation->getErrors()));
        }

        $transaction = [
            'date' => $data['date'],
            'description' => $data['description'],
            'debit_account_id' => (int)$data['debit_account_id'],
            'credit_account_id' => (int)$data['credit_account_id'],
            'amount' => (float)$data['amount']
        ];

        try {
            if ($this->transactionModel->insert($transaction)) {
                log_message('info', 'Transaksi berhasil ditambahkan: ' . json_encode($transaction));
                return redirect()->to('/')->with('success', 'Transaksi berhasil ditambahkan');
            }
            log_message('error', 'Gagal menambahkan transaksi: Tidak ada baris yang disisipkan');
            return redirect()->back()->with('error', 'Gagal menambahkan transaksi');
        } catch (\Exception $e) {
            log_message('error', 'Exception saat insert transaksi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan transaksi: ' . $e->getMessage());
        }
    }

    public function editTransaction($id)
    {
        log_message('debug', 'Edit transaction called with ID: ' . $id);

        if ($this->request->getMethod(true) === 'POST') {
            $data = $this->request->getPost();
            log_message('debug', 'POST data received: ' . json_encode($data));

            $validation = \Config\Services::validation();
            $validation->setRules([
                'date' => 'required|valid_date',
                'description' => 'required|min_length[5]',
                'debit_account_id' => 'required|numeric',
                'credit_account_id' => 'required|numeric',
                'amount' => 'required|decimal|greater_than[0]'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                log_message('error', 'Validation failed: ' . implode(', ', $validation->getErrors()));
                return view('accounting/edit_transaction', [
                    'accounts' => $this->accountModel->findAll(),
                    'transaction' => array_merge($data, ['id' => $id]),
                    'month' => date('m'),
                    'year' => date('Y'),
                    'validation' => $validation
                ])->with('error', implode('<br>', $validation->getErrors()));
            }

            $transaction = [
                'date' => $data['date'],
                'description' => $data['description'],
                'debit_account_id' => (int)$data['debit_account_id'],
                'credit_account_id' => (int)$data['credit_account_id'],
                'amount' => (float)$data['amount']
            ];

            log_message('debug', 'Attempting to update transaction ID ' . $id . ' with data: ' . json_encode($transaction));

            $current = $this->transactionModel->find($id);
            if (!$current) {
                log_message('error', 'Transaction ID ' . $id . ' not found during update');
                return redirect()->to('/')->with('error', 'Transaksi tidak ditemukan');
            }

            try {
                $updated = $this->transactionModel->update($id, $transaction);
                if ($updated) {
                    log_message('info', 'Transaction ID ' . $id . ' updated successfully');
                    $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
                    return redirect()->to('/')->with('success', 'Transaksi berhasil diperbarui');
                } else {
                    log_message('error', 'Update returned false for transaction ID ' . $id);
                    return view('accounting/edit_transaction', [
                        'accounts' => $this->accountModel->findAll(),
                        'transaction' => array_merge($data, ['id' => $id]),
                        'month' => date('m'),
                        'year' => date('Y'),
                        'error' => 'Gagal memperbarui transaksi: Tidak ada perubahan atau kesalahan database'
                    ]);
                }
            } catch (\Exception $e) {
                log_message('error', 'Exception during update for transaction ID ' . $id . ': ' . $e->getMessage());
                return view('accounting/edit_transaction', [
                    'accounts' => $this->accountModel->findAll(),
                    'transaction' => array_merge($data, ['id' => $id]),
                    'month' => date('m'),
                    'year' => date('Y'),
                    'error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()
                ]);
            }
        }

        $transaction = $this->transactionModel->find($id);
        if (!$transaction) {
            log_message('error', 'Transaction ID ' . $id . ' not found');
            return redirect()->to('/')->with('error', 'Transaksi tidak ditemukan');
        }

        $data = [
            'accounts' => $this->accountModel->findAll(),
            'transaction' => $transaction,
            'month' => date('m'),
            'year' => date('Y')
        ];
        log_message('debug', 'Rendering edit_transaction view for ID: ' . $id);
        return view('accounting/edit_transaction', $data);
    }

    public function deleteTransaction($id)
    {
        log_message('debug', 'Delete transaction called with ID: ' . $id);
        if ($this->transactionModel->delete($id)) {
            log_message('info', 'Transaction ID ' . $id . ' deleted successfully');
            return redirect()->to('/')->with('success', 'Transaksi berhasil dihapus');
        }
        log_message('error', 'Failed to delete transaction ID ' . $id);
        return redirect()->to('/')->with('error', 'Gagal menghapus transaksi');
    }

    public function generateIncomeStatement()
    {
        $month = $this->request->getGet('month') ?? date('m');
        $year = $this->request->getGet('year') ?? date('Y');
        $transactions = $this->transactionModel
            ->where('MONTH(date)', $month)
            ->where('YEAR(date)', $year)
            ->findAll();

        $income = ['Penjualan' => 0, 'Retur Penjualan' => 0, 'Potongan Penjualan' => 0];
        $expenses = ['Pembelian' => 0, 'Beban Angkut Pembelian' => 0, 'Retur Pembelian' => 0, 'Potongan Pembelian' => 0, 'Beban Gaji' => 0, 'Beban Sewa' => 0];
        $accountMap = array_column($this->accountModel->findAll(), 'account_name', 'id');

        foreach ($transactions as $t) {
            $debitAccount = $accountMap[$t['debit_account_id']] ?? 'Unknown';
            $creditAccount = $accountMap[$t['credit_account_id']] ?? 'Unknown';
            if (isset($income[$debitAccount])) {
                $income[$debitAccount] += $t['amount'];
            } elseif (isset($expenses[$debitAccount])) {
                $expenses[$debitAccount] += $t['amount'];
            }
            if (isset($income[$creditAccount])) {
                $income[$creditAccount] += $t['amount'];
            } elseif (isset($expenses[$creditAccount])) {
                $expenses[$creditAccount] += $t['amount'];
            }
        }

        $netSales = $income['Penjualan'] - $income['Retur Penjualan'] - $income['Potongan Penjualan'];
        $cogs = $expenses['Pembelian'] + $expenses['Beban Angkut Pembelian'] - $expenses['Retur Pembelian'] - $expenses['Potongan Pembelian'];
        $grossProfit = $netSales - $cogs;
        $operatingExpenses = $expenses['Beban Gaji'] + $expenses['Beban Sewa'];
        $netIncome = $grossProfit - $operatingExpenses;

        $report = [
            'net_sales' => $netSales,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $operatingExpenses,
            'net_income' => $netIncome
        ];

        $this->financialReportModel->insert([
            'report_type' => 'IncomeStatement',
            'period_start' => "$year-$month-01",
            'period_end' => date('Y-m-t', strtotime("$year-$month-01")),
            'data' => json_encode($report)
        ]);

        return view('accounting/income_statement', ['report' => $report, 'month' => $month, 'year' => $year]);
    }

    public function generateBalanceSheet()
    {
        $month = $this->request->getGet('month') ?? date('m');
        $year = $this->request->getGet('year') ?? date('Y');
        $transactions = $this->transactionModel
            ->where('MONTH(date)', $month)
            ->where('YEAR(date)', $year)
            ->findAll();

        $accounts = $this->accountModel->findAll();
        $accountMap = array_column($accounts, 'account_name', 'id');
        $balances = [];

        foreach ($accounts as $account) {
            $balances[$account['account_name']] = ['debit' => 0, 'credit' => 0];
        }

        foreach ($transactions as $t) {
            $debitAccount = $accountMap[$t['debit_account_id']] ?? 'Unknown';
            $creditAccount = $accountMap[$t['credit_account_id']] ?? 'Unknown';
            $balances[$debitAccount]['debit'] += $t['amount'];
            $balances[$creditAccount]['credit'] += $t['amount'];
        }

        $assets = ['Kas', 'Piutang Dagang', 'Persediaan Barang Dagang', 'Asuransi Dibayar Dimuka', 'Tanah', 'Gedung'];
        $liabilities = ['Utang Dagang', 'Hutang Gaji', 'Hutang Sewa'];
        $equity = ['Modal', 'Prive'];

        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        foreach ($assets as $asset) {
            $totalAssets += ($balances[$asset]['debit'] - $balances[$asset]['credit']);
        }
        foreach ($liabilities as $liability) {
            $totalLiabilities += ($balances[$liability]['credit'] - $balances[$liability]['debit']);
        }
        foreach ($equity as $eq) {
            $totalEquity += ($balances[$eq]['credit'] - $balances[$eq]['debit']);
        }

        $report = [
            'assets' => array_map(fn($a) => ['name' => $a, 'balance' => $balances[$a]['debit'] - $balances[$a]['credit']], $assets),
            'liabilities' => array_map(fn($l) => ['name' => $l, 'balance' => $balances[$l]['credit'] - $balances[$l]['debit']], $liabilities),
            'equity' => array_map(fn($e) => ['name' => $e, 'balance' => $balances[$e]['credit'] - $balances[$e]['debit']], $equity),
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity
        ];

        $this->financialReportModel->insert([
            'report_type' => 'BalanceSheet',
            'period_start' => "$year-$month-01",
            'period_end' => date('Y-m-t', strtotime("$year-$month-01")),
            'data' => json_encode($report)
        ]);

        return view('accounting/balance_sheet', ['report' => $report, 'month' => $month, 'year' => $year]);
    }

    public function exportExcel()
    {
        $month = $this->request->getGet('month') ?? date('m');
        $year = $this->request->getGet('year') ?? date('Y');

        // Ambil data transaksi berdasarkan bulan dan tahun
        $transactions = $this->transactionModel
            ->select('transactions.date, transactions.description, transactions.amount, debit.account_name as debit_account, credit.account_name as credit_account')
            ->join('accounts as debit', 'debit.id = transactions.debit_account_id')
            ->join('accounts as credit', 'credit.id = transactions.credit_account_id')
            ->where('MONTH(transactions.date)', $month)
            ->where('YEAR(transactions.date)', $year)
            ->findAll();

        log_message('debug', 'Transactions for export: ' . json_encode($transactions));

        // Buat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jurnal Umum');

        // Set header tabel
        $sheet->setCellValue('A1', 'Jurnal Umum - ' . sprintf('%02d', $month) . '/' . $year);
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $headers = ['Tanggal', 'Deskripsi', 'Akun Debit', 'Akun Kredit', 'Jumlah (Rp)'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        $sheet->getStyle('A3:E3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:E3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Isi data
        $row = 4;
        foreach ($transactions as $t) {
            $sheet->setCellValue('A' . $row, date('d/m/Y', strtotime($t['date'])));
            $sheet->setCellValue('B' . $row, $t['description']);
            $sheet->setCellValue('C' . $row, $t['debit_account']);
            $sheet->setCellValue('D' . $row, $t['credit_account']);
            $sheet->setCellValue('E' . $row, number_format($t['amount'], 2, ',', '.'));
            $sheet->getStyle('A' . $row . ':E' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $row++;
        }

        // Atur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);

        // Format kolom Jumlah sebagai angka dengan pemisah ribuan
        $sheet->getStyle('E4:E' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.00');

        // Tulis file Excel ke output
        $writer = new Xlsx($spreadsheet);
        $filename = 'Jurnal_Umum_' . sprintf('%02d', $month) . '_' . $year . '.xlsx';

        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output file
        $writer->save('php://output');
        exit;
    }
}