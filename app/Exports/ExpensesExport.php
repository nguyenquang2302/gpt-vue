<?php

namespace App\Exports;

use App\Models\Expense\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExpensesExport implements FromCollection
{
    public function collection()
    {
        return Expense::all();
    }
}