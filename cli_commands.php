<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
if (PHP_SAPI === 'cli') {
    \Magento\Framework\Console\CommandLocator::register(\Duck\ComposeGenerate\Console\CommandList::class);
}
