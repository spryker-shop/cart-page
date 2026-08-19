<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShopTest\Yves\CartPage\Business;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\ItemBuilder;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\StorageAvailabilityTransfer;
use SprykerShop\Yves\CartPage\Dependency\Client\CartPageToProductStorageClientInterface;
use SprykerShop\Yves\CartPage\Mapper\CartItemsAttributeMapper;
use SprykerShop\Yves\CartPage\Mapper\CartItemsMapperInterface;

/**
 * @group SprykerShop
 * @group Yves
 * @group CartPage
 * @group Business
 * @group CartItemsAttributeMapperTest
 */
class CartItemsAttributeMapperTest extends Unit
{
    /**
     * @var string
     */
    protected const KEY_ATTRIBUTE_MAP = 'attribute_map';

    /**
     * @var string
     */
    protected const KEY_ATTRIBUTE_VARIANT_MAP = 'attribute_variant_map';

    /**
     * @var string
     */
    protected const KEY_ATTRIBUTE_VARIANTS = 'attribute_variants';

    /**
     * @var string
     */
    protected const PRODUCT_CONCRETE_IDS = 'product_concrete_ids';

    /**
     * @var string
     */
    protected const FAKE_SKU_1 = 'fake-sku-1';

    /**
     * @var string
     */
    protected const FAKE_SKU_2 = 'fake-sku-2';

    /**
     * @var int
     */
    protected const FAKE_ID_PRODUCT_CONCRETE_1 = 1;

    /**
     * @var int
     */
    protected const FAKE_ID_PRODUCT_CONCRETE_2 = 2;

    /**
     * @var string
     */
    protected const FAKE_SKU_3 = 'fake-sku-3';

    /**
     * @var string
     */
    protected const FAKE_SKU_4 = 'fake-sku-4';

    /**
     * @var int
     */
    protected const FAKE_ID_PRODUCT_CONCRETE_3 = 3;

    /**
     * @var int
     */
    protected const FAKE_ID_PRODUCT_CONCRETE_4 = 4;

    /**
     * @var array
     */
    protected const FAKE_ATTRIBUTE_VARIANT_MAP_1 = [
        'color' => 'white',
        'flash_memory' => '4 GB',
    ];

    /**
     * @var array
     */
    protected const FAKE_ATTRIBUTE_VARIANT_MAP_2 = [
        'color' => 'black',
        'flash_memory' => '8 GB',
    ];

    /**
     * @var array
     */
    protected const FAKE_ATTRIBUTE_VARIANT_MAP_3 = [
        'color' => 'white',
        'flash_memory' => '8 GB',
    ];

    /**
     * @var array
     */
    protected const FAKE_ATTRIBUTE_VARIANT_MAP_4 = [
        'color' => 'black',
        'flash_memory' => '4 GB',
    ];

    /**
     * @var array
     */
    protected const FAKE_ATTRIBUTE_VARIANTS = [
        'color:white' => [
            'flash_memory:4 GB' => ['id_product_concrete' => self::FAKE_ID_PRODUCT_CONCRETE_1],
        ],
        'flash_memory:4 GB' => [
            'color:white' => ['id_product_concrete' => self::FAKE_ID_PRODUCT_CONCRETE_1],
        ],
        'color:black' => [
            'flash_memory:8 GB' => ['id_product_concrete' => self::FAKE_ID_PRODUCT_CONCRETE_2],
        ],
        'flash_memory:8 GB' => [
            'color:black' => ['id_product_concrete' => self::FAKE_ID_PRODUCT_CONCRETE_2],
        ],
    ];

    /**
     * @var \SprykerShopTest\Yves\CartPage\CartPageTester
     */
    protected $tester;

    public function testBuildMapWithTwoAvailableConcretes(): void
    {
        // Arrange
        $itemTransfers = new ArrayObject([
            (new ItemBuilder([
                ItemTransfer::ID => static::FAKE_ID_PRODUCT_CONCRETE_1,
                ItemTransfer::SKU => static::FAKE_SKU_1,
                ItemTransfer::ID_PRODUCT_ABSTRACT => 666,
            ]))->build(),
        ]);

        // Act
        $attributes = $this->getCartItemsAttributeMapper([
            static::FAKE_SKU_1 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => true],
            static::FAKE_SKU_2 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => true],
            static::FAKE_SKU_3 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => true],
            static::FAKE_SKU_4 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => true],
        ])->buildMap($itemTransfers, 'DE');

        // Assert
        $this->assertTrue($attributes[static::FAKE_SKU_1]['color']['white']['available']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['color']['white']['selected']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['color']['black']['available']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['color']['black']['selected']);

        $this->assertTrue($attributes[static::FAKE_SKU_1]['flash_memory']['4 GB']['available']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['flash_memory']['4 GB']['selected']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['flash_memory']['8 GB']['available']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['flash_memory']['8 GB']['selected']);
    }

    public function testBuildMapWithOneAvailableConcretes(): void
    {
        // Arrange
        $itemTransfers = new ArrayObject([
            (new ItemBuilder([
                ItemTransfer::ID => static::FAKE_ID_PRODUCT_CONCRETE_1,
                ItemTransfer::SKU => static::FAKE_SKU_1,
                ItemTransfer::ID_PRODUCT_ABSTRACT => 666,
            ]))->build(),
        ]);

        // Act
        $attributes = $this->getCartItemsAttributeMapper([
            static::FAKE_SKU_1 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => true],
            static::FAKE_SKU_2 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => false],
            static::FAKE_SKU_3 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => false],
            static::FAKE_SKU_4 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => false],
        ])->buildMap($itemTransfers, 'DE');

        // Assert
        $this->assertTrue($attributes[static::FAKE_SKU_1]['color']['white']['available']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['color']['white']['selected']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['color']['black']['available']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['color']['black']['selected']);

        $this->assertTrue($attributes[static::FAKE_SKU_1]['flash_memory']['4 GB']['available']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['flash_memory']['4 GB']['selected']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['flash_memory']['8 GB']['available']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['flash_memory']['8 GB']['selected']);
    }

    public function testBuildMapWithoutAvailableConcretes(): void
    {
        // Arrange
        $itemTransfers = new ArrayObject([
            (new ItemBuilder([
                ItemTransfer::ID => static::FAKE_ID_PRODUCT_CONCRETE_1,
                ItemTransfer::SKU => static::FAKE_SKU_1,
                ItemTransfer::ID_PRODUCT_ABSTRACT => 666,
            ]))->build(),
        ]);

        // Act
        $attributes = $this->getCartItemsAttributeMapper([
            static::FAKE_SKU_1 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => false],
            static::FAKE_SKU_2 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => false],
            static::FAKE_SKU_3 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => false],
            static::FAKE_SKU_4 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => false],
        ])->buildMap($itemTransfers, 'DE');

        // Assert
        $this->assertFalse($attributes[static::FAKE_SKU_1]['color']['white']['available']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['color']['white']['selected']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['color']['black']['available']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['color']['black']['selected']);

        $this->assertFalse($attributes[static::FAKE_SKU_1]['flash_memory']['4 GB']['available']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['flash_memory']['4 GB']['selected']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['flash_memory']['8 GB']['available']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['flash_memory']['8 GB']['selected']);
    }

    public function testBuildMapChecksDeprecatedMethodToKeepBC(): void
    {
        // Arrange
        $itemTransfers = new ArrayObject([
            (new ItemBuilder([
                ItemTransfer::ID => static::FAKE_ID_PRODUCT_CONCRETE_1,
                ItemTransfer::SKU => static::FAKE_SKU_1,
                ItemTransfer::ID_PRODUCT_ABSTRACT => 666,
            ]))->build(),
        ]);

        // Act
        $attributes = $this->getCartItemsAttributeMapper([
            static::FAKE_SKU_1 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => true],
            static::FAKE_SKU_2 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => true],
        ], false)->buildMap($itemTransfers, 'DE');

        // Assert
        $this->assertTrue($attributes[static::FAKE_SKU_1]['color']['white']['available']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['color']['white']['selected']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['color']['black']['available']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['color']['black']['selected']);

        $this->assertTrue($attributes[static::FAKE_SKU_1]['flash_memory']['4 GB']['available']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['flash_memory']['4 GB']['selected']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['flash_memory']['8 GB']['available']);
        $this->assertFalse($attributes[static::FAKE_SKU_1]['flash_memory']['8 GB']['selected']);
    }

    /**
     * The cart item is white with 4 GB and the only other variant is black with 8 GB, so switching a
     * single attribute cannot reach it. Neither of its values may be offered for the item.
     *
     * @return void
     */
    public function testBuildMapExcludesValuesOfVariantsDifferingInMoreThanOneAttribute(): void
    {
        // Arrange
        $itemTransfers = new ArrayObject([
            (new ItemBuilder([
                ItemTransfer::ID => static::FAKE_ID_PRODUCT_CONCRETE_1,
                ItemTransfer::SKU => static::FAKE_SKU_1,
                ItemTransfer::ID_PRODUCT_ABSTRACT => 666,
            ]))->build(),
        ]);
        $attributeVariantMap = [
            static::FAKE_ID_PRODUCT_CONCRETE_1 => static::FAKE_ATTRIBUTE_VARIANT_MAP_1,
            static::FAKE_ID_PRODUCT_CONCRETE_2 => static::FAKE_ATTRIBUTE_VARIANT_MAP_2,
        ];

        // Act
        $attributes = $this->getCartItemsAttributeMapper(
            [
                static::FAKE_SKU_1 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => true],
                static::FAKE_SKU_2 => [StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS => true],
            ],
            true,
            $attributeVariantMap,
        )->buildMap($itemTransfers, 'DE');

        // Assert
        $this->assertSame(
            ['white'],
            array_keys($attributes[static::FAKE_SKU_1]['color']),
            'Expected the color of the unreachable variant not to be offered.',
        );
        $this->assertSame(
            ['4 GB'],
            array_keys($attributes[static::FAKE_SKU_1]['flash_memory']),
            'Expected the flash memory of the unreachable variant not to be offered.',
        );
        $this->assertTrue($attributes[static::FAKE_SKU_1]['color']['white']['selected']);
        $this->assertTrue($attributes[static::FAKE_SKU_1]['flash_memory']['4 GB']['selected']);
    }

    protected function getCartItemsAttributeMapper(
        array $expectedAvailabilityMap,
        ?bool $withNewAttributeMap = true,
        ?array $attributeVariantMap = null
    ): CartItemsMapperInterface {
        return new CartItemsAttributeMapper(
            $this->getProductStorageClientMock($withNewAttributeMap, $attributeVariantMap),
            $this->getCartItemsMapperMock($expectedAvailabilityMap),
        );
    }

    /**
     * Every combination of both super attributes exists, so each variant is one switch away from the
     * cart item and all values stay selectable.
     *
     * @return array<int, array<string, string>>
     */
    protected function getCompleteAttributeVariantMap(): array
    {
        return [
            static::FAKE_ID_PRODUCT_CONCRETE_1 => static::FAKE_ATTRIBUTE_VARIANT_MAP_1,
            static::FAKE_ID_PRODUCT_CONCRETE_2 => static::FAKE_ATTRIBUTE_VARIANT_MAP_2,
            static::FAKE_ID_PRODUCT_CONCRETE_3 => static::FAKE_ATTRIBUTE_VARIANT_MAP_3,
            static::FAKE_ID_PRODUCT_CONCRETE_4 => static::FAKE_ATTRIBUTE_VARIANT_MAP_4,
        ];
    }

    /**
     * @param bool|null $withNewAttributeMap
     * @param array<int, array<string, string>>|null $attributeVariantMap
     *
     * @return \SprykerShop\Yves\CartPage\Dependency\Client\CartPageToProductStorageClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getProductStorageClientMock(
        ?bool $withNewAttributeMap = true,
        ?array $attributeVariantMap = null
    ): CartPageToProductStorageClientInterface {
        $attributeVariantMap = $attributeVariantMap ?? $this->getCompleteAttributeVariantMap();
        $productStorageClientMock = $this
            ->getMockBuilder(CartPageToProductStorageClientInterface::class)
            ->onlyMethods([
                'getBulkProductAbstractStorageDataByProductAbstractIdsAndLocaleName',
                'findProductAbstractStorageData',
                'findProductAbstractViewTransfer',
            ])
            ->getMock();

        $productStorageClientMock->expects($this->never())->method('findProductAbstractStorageData');
        $productStorageClientMock->expects($this->once())
            ->method('getBulkProductAbstractStorageDataByProductAbstractIdsAndLocaleName')
            ->willReturnCallback(function ($productAbstractIds, $localeName) use ($withNewAttributeMap, $attributeVariantMap) {
                $abstractProductData = [];

                foreach ($productAbstractIds as $idProductAbstract) {
                    $abstractProductData[$idProductAbstract] = [
                        static::KEY_ATTRIBUTE_MAP => [
                            static::PRODUCT_CONCRETE_IDS => [
                                static::FAKE_SKU_1 => static::FAKE_ID_PRODUCT_CONCRETE_1,
                                static::FAKE_SKU_2 => static::FAKE_ID_PRODUCT_CONCRETE_2,
                                static::FAKE_SKU_3 => static::FAKE_ID_PRODUCT_CONCRETE_3,
                                static::FAKE_SKU_4 => static::FAKE_ID_PRODUCT_CONCRETE_4,
                            ],
                            static::KEY_ATTRIBUTE_VARIANTS => static::FAKE_ATTRIBUTE_VARIANTS,
                        ],
                    ];

                    if ($withNewAttributeMap) {
                        $abstractProductData[$idProductAbstract][static::KEY_ATTRIBUTE_MAP][static::KEY_ATTRIBUTE_VARIANT_MAP] = $attributeVariantMap;
                    }
                }

                return $abstractProductData;
            });

        return $productStorageClientMock;
    }

    /**
     * @param array $expectedAvailabilityMap
     *
     * @return \SprykerShop\Yves\CartPage\Mapper\CartItemsMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getCartItemsMapperMock(array $expectedAvailabilityMap): CartItemsMapperInterface
    {
        $cartItemsMapperMock = $this
            ->getMockBuilder(CartItemsMapperInterface::class)
            ->onlyMethods(['buildMap'])
            ->getMock();

        $cartItemsMapperMock->expects($this->once())
            ->method('buildMap')
            ->willReturn($expectedAvailabilityMap);

        return $cartItemsMapperMock;
    }
}
