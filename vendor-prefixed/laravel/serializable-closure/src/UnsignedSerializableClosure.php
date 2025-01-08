<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 08-January-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YDPL\Vendor_Prefixed\Laravel\SerializableClosure;

use Closure;
use YDPL\Vendor_Prefixed\Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;

class UnsignedSerializableClosure
{
    /**
     * The closure's serializable.
     *
     * @var \YDPL\Vendor_Prefixed\Laravel\SerializableClosure\Contracts\Serializable
     */
    protected $serializable;

    /**
     * Creates a new serializable closure instance.
     *
     * @param  \Closure  $closure
     * @return void
     */
    public function __construct(Closure $closure)
    {
        if (\PHP_VERSION_ID < 70400) {
            throw new PhpVersionNotSupportedException();
        }

        $this->serializable = new Serializers\Native($closure);
    }

    /**
     * Resolve the closure with the given arguments.
     *
     * @return mixed
     */
    public function __invoke()
    {
        if (\PHP_VERSION_ID < 70400) {
            throw new PhpVersionNotSupportedException();
        }

        return call_user_func_array($this->serializable, func_get_args());
    }

    /**
     * Gets the closure.
     *
     * @return \Closure
     */
    public function getClosure()
    {
        if (\PHP_VERSION_ID < 70400) {
            throw new PhpVersionNotSupportedException();
        }

        return $this->serializable->getClosure();
    }

    /**
     * Get the serializable representation of the closure.
     *
     * @return array
     */
    public function __serialize()
    {
        return [
            'serializable' => $this->serializable,
        ];
    }

    /**
     * Restore the closure after serialization.
     *
     * @param  array  $data
     * @return void
     */
    public function __unserialize($data)
    {
        $this->serializable = $data['serializable'];
    }
}
