<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Helper
{
    /**
     * Dispatch jobs to process records in batches.
     *
     * @param  mixed  $commandClass  The class instance of the command (to show output).
     * @param  string  $jobClass  The job class to dispatch.
     * @param  string  $modelClass  The model class being processed.
     * @param  int  $totalRecords  The total number of records to process.
     * @param  int  $batchSize  The number of records per job (default: 100).
     * @return void
     */
    public static function dispatchJobHelper($commandClass, $jobClass, string $modelClass, int $totalRecords, int $batchSize = 100): void
    {
        // Calcular el número de Jobs necesarios
        $jobsCount = ceil($totalRecords / $batchSize);

        if ($totalRecords > 0) {
            $commandClass->info("{$jobClass} -- {$modelClass}: Dispatching {$jobsCount} jobs to process {$totalRecords} records.");

            // Despachar un Job por cada lote
            for ($i = 0; $i < $jobsCount; $i++) {
                $offset = $i * $batchSize;

                // Ajustar el tamaño real del lote para el último trabajo
                $actualBatchSize = min($batchSize, $totalRecords - $offset);

                try {
                    // Despachar el Job
                    $jobClass::dispatch($modelClass, $offset, $actualBatchSize);
                    $commandClass->info("{$jobClass} -- {$modelClass}: Dispatched job for records {$offset} to " . ($offset + $actualBatchSize));
                } catch (\Exception $e) {
                    // Manejar errores al despachar el Job
                    $commandClass->error("Failed to dispatch job for records {$offset} to " . ($offset + $actualBatchSize) . ". Error: {$e->getMessage()}");
                    Log::error("Failed to dispatch job for records {$offset} to " . ($offset + $actualBatchSize) . ". Error: {$e->getMessage()}");
                }
            }
        }
    }
}