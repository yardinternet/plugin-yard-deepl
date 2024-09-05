<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YardDeepl\Vendor_Prefixed\Doctrine\Inflector\Rules;

class Word
{
    /** @var string */
    private $word;

    public function __construct(string $word)
    {
        $this->word = $word;
    }

    public function getWord(): string
    {
        return $this->word;
    }
}
