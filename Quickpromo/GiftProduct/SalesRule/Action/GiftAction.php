<?php

namespace Quickpromo\GiftProduct\SalesRule\Action;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule\Action\Discount;

use Psr\Log\LoggerInterface;

/**
 * Handles applying a "Add a Gift" type SalesRule.
 *
 * @category   Quickpromo
 * @package    Quickpromo_GiftProduct
 * @author     Rao <raoafzal25@gmail.com>
 * @copyright  idealwebz.com
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class GiftAction implements Discount\DiscountInterface
{
    const ACTION = 'add_gift';

    const ITEM_OPTION_UNIQUE_ID = 'freeproduct_gift_unique_id';
    const RULE_DATA_KEY_SKU = 'gift_sku';
    const PRODUCT_TYPE_FREEPRODUCT = 'freeproduct_gift';
    const APPLIED_FREEPRODUCT_RULE_IDS = '_freeproduct_applied_rules';

    /**
     * @var Discount\DataFactory
     */
    private $discountDataFactory;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Discount\DataFactory $discountDataFactory
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(Discount\DataFactory $discountDataFactory,
                                ProductRepositoryInterface $productRepository,
                                LoggerInterface $logger)
    {
        $this->discountDataFactory = $discountDataFactory;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param AbstractItem $item
     * @param float $qty
     * @return Discount\Data
     */
    public function calculate($rule, $item, $qty)
    {
        $appliedRuleIds = $item->getAddress()->getData(static::APPLIED_FREEPRODUCT_RULE_IDS);

        if ($item->getAddress()->getAddressType() != Address::TYPE_SHIPPING
            || ($appliedRuleIds != null && isset($appliedRuleIds[$rule->getId()])))
        {
            return $this->getDiscountData($item);
        }

        $sku = $rule->getData(static::RULE_DATA_KEY_SKU);

        try
        {
            $quoteItem = $item->getQuote()->addProduct($this->getGiftProduct($sku), $rule->getDiscountAmount());

            if (is_string($quoteItem))
            {
                throw new \Exception($quoteItem);
            }

            $this->addAppliedRuleId($rule->getRuleId(), $item->getAddress());
        } catch (\Exception $e)
        {
            $this->logger->error(
                sprintf('Exception occurred while adding gift product %s to cart. Rule: %d, Exception: %s', $sku, $rule->getId(), $e->getMessage()),
                [__METHOD__]
            );
        }

        return $this->getDiscountData($item);
    }

    /**
     * @inheritdoc
     */
    public function fixQuantity($qty, $rule)
    {
        return $qty;
    }

    /**
     * @param int $ruleId
     * @param Address $address
     */
    private function addAppliedRuleId(int $ruleId, Address $address)
    {
        $appliedRules = $address->getData(static::APPLIED_FREEPRODUCT_RULE_IDS);

        if ($appliedRules == null)
        {
            $appliedRules = [];
        }

        $appliedRules[$ruleId] = $ruleId;

        $address->setData(static::APPLIED_FREEPRODUCT_RULE_IDS, $appliedRules);
    }

    /**
     * Get and prepare the gift product
     *
     * @param string $sku
     * @return ProductInterface|Product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getGiftProduct(string $sku): ProductInterface
    {
        /** @var Product $product */
        $product = $this->productRepository->get($sku);
        /**
         * Makes it unique, to avoid merging
         * @see \Magento\Quote\Model\Quote\Item::representProduct
         */
        $product->addCustomOption(static::ITEM_OPTION_UNIQUE_ID, uniqid());
        $product->addCustomOption(CartItemInterface::KEY_PRODUCT_TYPE, static::PRODUCT_TYPE_FREEPRODUCT);

        return $product;
    }

    /**
     * No discount is changed by GiftAction, but the existing has to be preserved
     *
     * @param AbstractItem $item
     * @return Discount\Data
     */
    private function getDiscountData(AbstractItem $item): Discount\Data
    {
        return $this->discountDataFactory->create([
            'amount' => $item->getDiscountAmount(),
            'baseAmount' => $item->getBaseDiscountAmount(),
            'originalAmount' => $item->getOriginalDiscountAmount(),
            'baseOriginalAmount' => $item->getBaseOriginalDiscountAmount()
        ]);
    }
}