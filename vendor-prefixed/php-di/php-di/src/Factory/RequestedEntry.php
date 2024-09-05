<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YardDeepl\Vendor_Prefixed\DI\Factory;

/**
 * Represents the container entry that was requested.
 *
 * Implementations of this interface can be injected in factory parameters in order
 * to know what was the name of the requested entry.
 *
 * @api
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface RequestedEntry
{
    /**
     * Returns the name of the entry that was requested by the container.
     */
    public function getName() : string;
}
