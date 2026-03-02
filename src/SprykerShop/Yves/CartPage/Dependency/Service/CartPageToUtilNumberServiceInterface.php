<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CartPage\Dependency\Service;

use Generated\Shared\Transfer\NumberFormatConfigTransfer;
use Generated\Shared\Transfer\NumberFormatIntRequestTransfer;

interface CartPageToUtilNumberServiceInterface
{
    public function formatInt(NumberFormatIntRequestTransfer $numberFormatIntRequestTransfer): string;

    public function getNumberFormatConfig(?string $locale = null): NumberFormatConfigTransfer;
}
