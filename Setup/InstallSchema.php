<?php

namespace Decimal\Decimal\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('decimal_table')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('decimal_table')
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'ID'
                )
                ->addColumn(
                    'order_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => true
                    ],
                    'Order ID'
                )
                ->addColumn(
                    'coin',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Coin'
                )
                ->addColumn(
                    'invoice_address',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [ 'nullable' => true],
                    'Invoice address'
                )
                ->addColumn(
                    'total_paid',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    255,
                    [ 'nullable' => true],
                    'Total paid'
                )
                ->addColumn(
                    'total_paid_coins',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    255,
                    [ 'nullable' => true],
                    'Total paid coins'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => true],
                    'Order Status'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Created At'
                )->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                    'Updated At')
                ->setComment('Decimal Table');
            $installer->getConnection()->createTable($table);

        }
        $installer->endSetup();
    }
}
