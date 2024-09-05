<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Support\Testing\Fakes;

use YardDeepl\Vendor_Prefixed\Carbon\CarbonImmutable;
use Closure;
use YardDeepl\Vendor_Prefixed\Illuminate\Bus\Batch;
use YardDeepl\Vendor_Prefixed\Illuminate\Bus\BatchRepository;
use YardDeepl\Vendor_Prefixed\Illuminate\Bus\PendingBatch;
use YardDeepl\Vendor_Prefixed\Illuminate\Bus\UpdatedBatchJobCounts;
use YardDeepl\Vendor_Prefixed\Illuminate\Support\Facades\Facade;
use YardDeepl\Vendor_Prefixed\Illuminate\Support\Str;

class BatchRepositoryFake implements BatchRepository
{
    /**
     * Retrieve a list of batches.
     *
     * @param  int  $limit
     * @param  mixed  $before
     * @return \YardDeepl\Vendor_Prefixed\Illuminate\Bus\Batch[]
     */
    public function get($limit, $before)
    {
        return [];
    }

    /**
     * Retrieve information about an existing batch.
     *
     * @param  string  $batchId
     * @return \YardDeepl\Vendor_Prefixed\Illuminate\Bus\Batch|null
     */
    public function find(string $batchId)
    {
        //
    }

    /**
     * Store a new pending batch.
     *
     * @param  \YardDeepl\Vendor_Prefixed\Illuminate\Bus\PendingBatch  $batch
     * @return \YardDeepl\Vendor_Prefixed\Illuminate\Bus\Batch
     */
    public function store(PendingBatch $batch)
    {
        return new Batch(
            new QueueFake(Facade::getFacadeApplication()),
            $this,
            (string) Str::orderedUuid(),
            $batch->name,
            count($batch->jobs),
            count($batch->jobs),
            0,
            [],
            $batch->options,
            CarbonImmutable::now(),
            null,
            null
        );
    }

    /**
     * Increment the total number of jobs within the batch.
     *
     * @param  string  $batchId
     * @param  int  $amount
     * @return void
     */
    public function incrementTotalJobs(string $batchId, int $amount)
    {
        //
    }

    /**
     * Decrement the total number of pending jobs for the batch.
     *
     * @param  string  $batchId
     * @param  string  $jobId
     * @return \YardDeepl\Vendor_Prefixed\Illuminate\Bus\UpdatedBatchJobCounts
     */
    public function decrementPendingJobs(string $batchId, string $jobId)
    {
        return new UpdatedBatchJobCounts;
    }

    /**
     * Increment the total number of failed jobs for the batch.
     *
     * @param  string  $batchId
     * @param  string  $jobId
     * @return \YardDeepl\Vendor_Prefixed\Illuminate\Bus\UpdatedBatchJobCounts
     */
    public function incrementFailedJobs(string $batchId, string $jobId)
    {
        return new UpdatedBatchJobCounts;
    }

    /**
     * Mark the batch that has the given ID as finished.
     *
     * @param  string  $batchId
     * @return void
     */
    public function markAsFinished(string $batchId)
    {
        //
    }

    /**
     * Cancel the batch that has the given ID.
     *
     * @param  string  $batchId
     * @return void
     */
    public function cancel(string $batchId)
    {
        //
    }

    /**
     * Delete the batch that has the given ID.
     *
     * @param  string  $batchId
     * @return void
     */
    public function delete(string $batchId)
    {
        //
    }

    /**
     * Execute the given Closure within a storage specific transaction.
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    public function transaction(Closure $callback)
    {
        return $callback();
    }
}
