<?php

namespace App\Models;

use CodeIgniter\Model;

class FinancialReportModel extends Model
{
    protected $table = 'financial_reports';
    protected $primaryKey = 'id';
    protected $allowedFields = ['report_type', 'period_start', 'period_end', 'data'];
}