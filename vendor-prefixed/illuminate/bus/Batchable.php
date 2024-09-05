<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Illuminate\Bus;

use YardDeepl\Vendor_Prefixed\Illuminate\Container\Container;

trait Batchable
{
    /**
     * The batch ID (if applicable).
     *
     * @var string
     */
    public $batchId;

    /**
     * Get the batch instance for the job, if applicable.
     *
     * @return \YardDeepl\Vendor_Prefixed\Illuminate\Bus\Batch|null
     */
    public function batch()
    {
        if ($this->batchId) {
            return Container::getInstance()->make(BatchRepository::class)->find($this->batchId);
        }
    }

    /**
     * Determine if the batch is still active and processing.
     *
     * @return bool
     */
    public function batching()
    {
        $batch = $this->batch();

        return $batch && ! $batch->cancelled();
    }

    /**
     * Set the batch ID on the job.
     *
     * @param  string  $batchId
     * @return $this
     */
    public function withBatchId(string $batchId)
    {
        $this->batchId = $batchId;

        return $this;
    }
}
