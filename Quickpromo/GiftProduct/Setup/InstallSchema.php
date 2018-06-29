<?php

namespace Quickpromo\GiftProduct\Setup;

use Quickpromo\GiftProduct\SalesRule\Action\GiftAction;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Add a field for Gift SKU into SalesRule entity
 *
 * @category   Quickpromo
 * @package    Quickpromo_GiftProduct
 * @author     Rao <raoafzal25@gmail.com>
 * @copyright  idealwebz.com
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->addColumn($setup->getTable('salesrule'), GiftAction::RULE_DATA_KEY_SKU, [
            'type' => Table::TYPE_TEXT,
            'length' => 255,
            'comment' => 'Sku of Gift product'
        ]);

        $setup->endSetup();
    }
}
