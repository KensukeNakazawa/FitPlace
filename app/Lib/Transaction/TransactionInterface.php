<?php

namespace App\Lib\Transaction;

/**
 * Interface TransactionInterface
 * @package App\Lib\Transaction
 */
interface TransactionInterface
{
    /**
     * トランザクションの開始
     * @return mixed
     */
    public function begin();

    /**
     * トランザクションのコミット
     * @return mixed
     */
    public function commit();

    /**
     * トランザクションのロールバック
     * @return mixed
     */
    public function rollBack();
}