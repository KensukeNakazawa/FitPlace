<?php

namespace App\Lib\Transaction;

use Illuminate\Support\Facades\DB;

class DBTransaction implements TransactionInterface
{
    public function begin()
    {
        DB::beginTransaction();
    }

    public function commit()
    {
        DB::commit();
    }

    public function rollBack()
    {
        DB::rollBack();
    }
}
