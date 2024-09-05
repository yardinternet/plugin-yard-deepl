<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Bus;

use YardDeepl\Vendor_Prefixed\Carbon\CarbonImmutable;
use YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Queue\Factory as QueueFactory;

class BatchFactory
{
    /**
     * The queue factory implementation.
     *
     * @var \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Queue\Factory
     */
    protected $queue;

    /**
     * Create a new batch factory instance.
     *
     * @param  \YardDeepl\Vendor_Prefixed\Illuminate\Contracts\Queue\Factory  $queue
     * @return void
     */
    public function __construct(QueueFactory $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Create a new batch instance.
     *
     * @param  \YardDeepl\Vendor_Prefixed\Illuminate\Bus\BatchRepository  $repository
     * @param  string  $id
     * @param  string  $name
     * @param  int  $totalJobs
     * @param  int  $pendingJobs
     * @param  int  $failedJobs
     * @param  array  $failedJobIds
     * @param  array  $options
     * @param  \YardDeepl\Vendor_Prefixed\Carbon\CarbonImmutable  $createdAt
     * @param  \YardDeepl\Vendor_Prefixed\Carbon\CarbonImmutable|null  $cancelledAt
     * @param  \YardDeepl\Vendor_Prefixed\Carbon\CarbonImmutable|null  $finishedAt
     * @return \YardDeepl\Vendor_Prefixed\Illuminate\Bus\Batch
     */
    public function make(BatchRepository $repository,
                         string $id,
                         string $name,
                         int $totalJobs,
                         int $pendingJobs,
                         int $failedJobs,
                         array $failedJobIds,
                         array $options,
                         CarbonImmutable $createdAt,
                         ?CarbonImmutable $cancelledAt,
                         ?CarbonImmutable $finishedAt)
    {
        return new Batch($this->queue, $repository, $id, $name, $totalJobs, $pendingJobs, $failedJobs, $failedJobIds, $options, $createdAt, $cancelledAt, $finishedAt);
    }
}
