<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 16-December-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YDPL\Vendor_Prefixed\Laravel\SerializableClosure\Contracts;

interface Signer
{
    /**
     * Sign the given serializable.
     *
     * @param  string  $serializable
     * @return array
     */
    public function sign($serializable);

    /**
     * Verify the given signature.
     *
     * @param  array  $signature
     * @return bool
     */
    public function verify($signature);
}
