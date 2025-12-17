<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerShopTest\Yves\CartPage\Model;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use ReflectionClass;
use SprykerShop\Yves\CartPage\CartPageConfig;
use SprykerShop\Yves\CartPage\ViewModel\CartPageView;

/**
 * @group SprykerShopTest
 * @group Yves
 * @group CartPage
 * @group Model
 * @group IsQuoteValidTest
 */
class IsQuoteValidTest extends Unit
{
    /**
     * @var \SprykerShop\Yves\CartPage\CartPageConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \SprykerShop\Yves\CartPage\ViewModel\CartPageView|\PHPUnit\Framework\MockObject\MockObject
     */
    protected CartPageView $cartPageView;

    /**
     * {@inheritDoc}
     */
    protected function _before(): void
    {
        parent::_before();

        $this->configMock = $this->getMockBuilder(CartPageConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartPageView = $this->getMockBuilder(CartPageView::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->setConfig($this->configMock);
    }

    public function testIsQuoteValidReturnsTrueWhenQuoteValidationDisabled(): void
    {
        // Arrange
        $this->configMock->expects($this->once())
            ->method('isQuoteValidationEnabled')
            ->willReturn(false);

        $this->configMock->expects($this->never())
            ->method('isQuoteValidationEnabledForAjaxCartItems');

        $this->setQuoteResponseTransfer(null);

        // Act
        $result = $this->callIsQuoteValid();

        // Assert
        $this->assertTrue($result);
    }

    public function testIsQuoteValidReturnsTrueWhenAjaxQuoteValidationDisabled(): void
    {
        // Arrange
        $this->configMock->expects($this->once())
            ->method('isQuoteValidationEnabled')
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('isQuoteValidationEnabledForAjaxCartItems')
            ->willReturn(false);

        $this->setQuoteResponseTransfer($this->quoteResponseTransferMock);

        // Act
        $result = $this->callIsQuoteValid();

        // Assert
        $this->assertTrue($result);
    }

    public function testIsQuoteValidReturnsTrueWhenNoQuoteResponseTransfer(): void
    {
        // Arrange
        $this->configMock->expects($this->once())
            ->method('isQuoteValidationEnabled')
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('isQuoteValidationEnabledForAjaxCartItems')
            ->willReturn(true);

        $this->setQuoteResponseTransfer(null);

        // Act
        $result = $this->callIsQuoteValid();

        // Assert
        $this->assertTrue($result);
    }

    public function testIsQuoteValidReturnsTrueWhenQuoteResponseIsSuccessful(): void
    {
        // Arrange
        $this->configMock->expects($this->once())
            ->method('isQuoteValidationEnabled')
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('isQuoteValidationEnabledForAjaxCartItems')
            ->willReturn(true);

        $this->quoteResponseTransferMock->expects($this->once())
            ->method('getIsSuccessfulOrFail')
            ->willReturn(true);

        $this->setQuoteResponseTransfer($this->quoteResponseTransferMock);

        // Act
        $result = $this->callIsQuoteValid();

        // Assert
        $this->assertTrue($result);
    }

    public function testIsQuoteValidReturnsFalseWhenQuoteResponseIsNotSuccessful(): void
    {
        // Arrange
        $this->configMock->expects($this->once())
            ->method('isQuoteValidationEnabled')
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('isQuoteValidationEnabledForAjaxCartItems')
            ->willReturn(true);

        $this->quoteResponseTransferMock->expects($this->once())
            ->method('getIsSuccessfulOrFail')
            ->willReturn(false);

        $this->setQuoteResponseTransfer($this->quoteResponseTransferMock);

        // Act
        $result = $this->callIsQuoteValid();

        // Assert
        $this->assertFalse($result);
    }

    protected function callIsQuoteValid(): bool
    {
        $reflectionClass = new ReflectionClass($this->cartPageView);
        $method = $reflectionClass->getMethod('isQuoteValid');

        return $method->invoke($this->cartPageView);
    }

    protected function setQuoteResponseTransfer(?QuoteResponseTransfer $quoteResponseTransfer): void
    {
        $reflectionClass = new ReflectionClass($this->cartPageView);
        $property = $reflectionClass->getProperty('quoteResponseTransfer');

        $property->setValue($this->cartPageView, $quoteResponseTransfer);
    }

    protected function setConfig(?CartPageConfig $config): void
    {
        $reflectionClass = new ReflectionClass($this->cartPageView);
        $property = $reflectionClass->getProperty('config');

        $property->setValue($this->cartPageView, $config);
    }
}
