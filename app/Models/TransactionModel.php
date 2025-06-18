<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['date', 'description', 'debit_account_id', 'credit_account_id', 'amount'];
    protected $useTimestamps = false;
}