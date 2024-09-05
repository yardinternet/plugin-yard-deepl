<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Support\Testing\Fakes;

use YardDeepl\Vendor_Prefixed\Illuminate\Bus\PendingBatch;
use YardDeepl\Vendor_Prefixed\Illuminate\Support\Collection;

class PendingBatchFake extends PendingBatch
{
    /**
     * The fake bus instance.
     *
     * @var \YardDeepl\Vendor_Prefixed\Illuminate\Support\Testing\Fakes\BusFake
     */
    protected $bus;

    /**
     * Create a new pending batch instance.
     *
     * @param  \YardDeepl\Vendor_Prefixed\Illuminate\Support\Testing\Fakes\BusFake  $bus
     * @param  \YardDeepl\Vendor_Prefixed\Illuminate\Support\Collection  $jobs
     * @return void
     */
    public function __construct(BusFake $bus, Collection $jobs)
    {
        $this->bus = $bus;
        $this->jobs = $jobs;
    }

    /**
     * Dispatch the batch.
     *
     * @return \YardDeepl\Vendor_Prefixed\Illuminate\Bus\Batch
     */
    public function dispatch()
    {
        return $this->bus->recordPendingBatch($this);
    }
}
