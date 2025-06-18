<?php

namespace App\Models;

use CodeIgniter\Model;

class SpecialJournalModel extends Model
{
    protected $table = 'special_journals';
    protected $primaryKey = 'id';
    protected $allowedFields = ['type', 'date', 'description', 'account_id', 'amount', 'is_debit'];
}