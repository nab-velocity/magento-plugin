<?php
/**
 * Script for add a table for velocity status
 *
 * @package Velocity
 * @category CreditCard
 * @author Velocity team
 */

$installer = $this;
$installer->startSetup();

if(!$installer->getConnection()->isTableExists($installer->getTable('creditcard/card'))) {

    $table = $installer->getConnection()->newTable($installer->getTable('creditcard/card'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
        'identity' => true,
        ), 'ID')
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 220, array(
        'nullable' => false,
        'default'  => '', 
        ), 'Transaction ID')
    ->addColumn('transaction_status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
        'nullable' => false,
        ), 'Transaction Status')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
        'nullable' => false,
        ), 'Order ID')
    ->addColumn('response_obj', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
        ), 'Response Object')
    ->setComment('Velocity Transactions Table');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();