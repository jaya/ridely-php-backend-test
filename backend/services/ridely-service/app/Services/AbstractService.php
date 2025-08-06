<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class AbstractService
{
    /**
     * @throws ServiceException
     */
    public function checkDatabase(): void
    {
        try {
            Log::debug('Checking database connection');
            DB::connection()->getPdo();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw ServiceException::databaseTemporarilyUnavailable($e->getMessage(), $e);
        }
    }
}