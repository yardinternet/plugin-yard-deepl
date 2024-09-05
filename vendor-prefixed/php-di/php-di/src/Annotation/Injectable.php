<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YardDeepl\Vendor_Prefixed\DI\Annotation;

/**
 * "Injectable" annotation.
 *
 * Marks a class as injectable
 *
 * @api
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Domenic Muskulus <domenic@muskulus.eu>
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
final class Injectable
{
    /**
     * Should the object be lazy-loaded.
     * @var bool|null
     */
    private $lazy;

    public function __construct(array $values)
    {
        if (isset($values['lazy'])) {
            $this->lazy = (bool) $values['lazy'];
        }
    }

    /**
     * @return bool|null
     */
    public function isLazy()
    {
        return $this->lazy;
    }
}
