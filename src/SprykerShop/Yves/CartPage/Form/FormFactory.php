<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CartPage\Form;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Kernel\AbstractFactory;
use Symfony\Component\Form\FormFactory as SymfonyFormFactory;
use Symfony\Component\Form\FormInterface;

class FormFactory extends AbstractFactory
{
    public function getFormFactory(): SymfonyFormFactory
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY);
    }

    public function getRemoveForm(): FormInterface
    {
        return $this->getFormFactory()->create(RemoveForm::class);
    }

    public function getAddToCartForm(): FormInterface
    {
        return $this->getFormFactory()->create(AddToCartForm::class);
    }

    public function getAddItemsForm(): FormInterface
    {
        return $this->getFormFactory()->create(AddItemsForm::class);
    }

    public function getCartChangeQuantityForm(): FormInterface
    {
        return $this->getFormFactory()->create(CartChangeQuantityForm::class);
    }
}
