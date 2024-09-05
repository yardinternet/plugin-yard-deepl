<?php

// Copyright 2024 DeepL SE (https://www.deepl.com)
// Use of this source code is governed by an MIT
// license that can be found in the LICENSE file.

namespace YardDeepl\Vendor_Prefixed\DeepL;

/**
 * Exception thrown if an error occurs during document minification.
 * @see Translator::translateDocument()
 * @see DocumentMinifier::deminifyDocument
 *
 * @license MIT
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class DocumentDeminificationException extends DocumentTranslationException
{
    public function __construct($message = "", $code = 0, $previous = null, ?DocumentHandle $handle = null)
    {
        parent::__construct($message, $code, $previous, $handle);
    }
}
