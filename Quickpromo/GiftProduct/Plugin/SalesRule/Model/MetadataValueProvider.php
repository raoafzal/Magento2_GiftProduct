<?php

namespace Quickpromo\GiftProduct\Plugin\SalesRule\Model;

use Quickpromo\GiftProduct\SalesRule\Action\GiftAction;
use \Magento\SalesRule\Model\Rule\Metadata\ValueProvider as Source;

/**
 * @category   Quickpromo
 * @package    Quickpromo_GiftProduct
 * @author     Rao <raoafzal25@gmail.com>
 * @copyright  idealwebz.com
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class MetadataValueProvider
{
    /**
     * Add the Gift action option to SalesRule
     *
     * @see \Magento\SalesRule\Model\Rule\Metadata\ValueProvider::getMetadataValues
     * @plugin after
     * @param Source $subject
     * @param array $resultMetadataValues
     * @return array
     */
    public function afterGetMetadataValues(Source $subject, $resultMetadataValues)
    {
        $resultMetadataValues['actions']['children']['simple_action']['arguments']['data']['config']['options'][] = [
            'label' => __('Add a Gift'), 'value' =>  GiftAction::ACTION
        ];

        return $resultMetadataValues;
    }
}