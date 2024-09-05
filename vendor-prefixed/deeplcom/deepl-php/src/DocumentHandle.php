<?php

// Copyright 2022 DeepL SE (https://www.deepl.com)
// Use of this source code is governed by an MIT
// license that can be found in the LICENSE file.

namespace YardDeepl\Vendor_Prefixed\DeepL;

/**
 * Handle to an in-progress document translation.
 *
 * @license MIT
 * Modified by yardinternet on 09-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class DocumentHandle
{

    /**
     * @var string ID of associated document request.
     */
    public $documentId;

    /**
     * @var string Key of associated document request.
     */
    public $documentKey;

    public function __construct(string $documentId, string $documentKey)
    {
        $this->documentId = $documentId;
        $this->documentKey = $documentKey;
    }
}
