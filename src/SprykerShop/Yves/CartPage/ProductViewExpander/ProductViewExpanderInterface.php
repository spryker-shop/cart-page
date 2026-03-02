<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CartPage\ProductViewExpander;

use Generated\Shared\Transfer\ProductViewTransfer;

interface ProductViewExpanderInterface
{
    public function expandProductViewWithCartData(ProductViewTransfer $productViewTransfer): ProductViewTransfer;
}
