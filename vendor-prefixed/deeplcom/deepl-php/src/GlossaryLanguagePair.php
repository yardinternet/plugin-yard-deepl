<?php

// Copyright 2022 DeepL SE (https://www.deepl.com)
// Use of this source code is governed by an MIT
// license that can be found in the LICENSE file.

namespace YardDeepl\Vendor_Prefixed\DeepL;

/**
 * Information about a glossary language supported by DeepL translator.
 *
 * @license MIT
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class GlossaryLanguagePair
{
    /**
     * @var string Language code of the language that glossary source terms are expressed in.
     * @see LanguageCode
     */
    public $sourceLang;

    /**
     * @var string Language code of the language that glossary target terms are expressed in. Note that unlike the
     * target language argument for translation functions, glossary target languages do not include regional variants.
     * @see LanguageCode
     */
    public $targetLang;

    public function __construct(string $sourceLang, string $targetLang)
    {
        $this->sourceLang = $sourceLang;
        $this->targetLang = $targetLang;
    }

    public function __toString(): string
    {
        return "$this->sourceLang->$this->targetLang";
    }
}
