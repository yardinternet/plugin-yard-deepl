<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace YardDeepl\Vendor_Prefixed\Carbon\Exceptions;

use InvalidArgumentException as BaseInvalidArgumentException;
use Throwable;

class UnknownSetterException extends BaseInvalidArgumentException implements BadMethodCallException
{
    /**
     * The setter.
     *
     * @var string
     */
    protected $setter;

    /**
     * Constructor.
     *
     * @param string         $setter   setter name
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($setter, $code = 0, Throwable $previous = null)
    {
        $this->setter = $setter;

        parent::__construct("Unknown setter '$setter'", $code, $previous);
    }

    /**
     * Get the setter.
     *
     * @return string
     */
    public function getSetter(): string
    {
        return $this->setter;
    }
}
