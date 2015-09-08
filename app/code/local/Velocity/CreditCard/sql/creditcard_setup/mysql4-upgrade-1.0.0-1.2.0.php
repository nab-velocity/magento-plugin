<?php
/**
 * Script for alter table for velocity
 *
 * @package Velocity
 * @category CreditCard
 * @author Velocity team
 */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
        ->addColumn($installer->getTable('creditcard/card'), 'request_obj', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => false,
        'comment' => 'Request Object'
    ));

$installer->endSetup();
