<?php

namespace Quickpromo\GiftProduct\SalesRule\Validator;

use Quickpromo\GiftProduct\SalesRule\Action\GiftAction;

/**
 * @category   Quickpromo
 * @package    Quickpromo_GiftProduct
 * @author     Rao <raoafzal25@gmail.com>
 * @copyright  idealwebz.com
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class GiftItemExcluder implements \Zend_Validate_Interface
{
    /**
     * Gift items should not be processed by SalesRules
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return bool
     */
    public function isValid($item)
    {
        return $item->getOptionByCode(GiftAction::ITEM_OPTION_UNIQUE_ID) == null;
    }

    /**
     * @inheritdoc
     */
    public function getMessages()
    {
        return [];
    }
}