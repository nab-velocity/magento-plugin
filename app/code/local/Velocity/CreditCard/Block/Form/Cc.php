<?php
/**
 * Form form block
 *
 * @category   velocity
 * @package    CreditCard
 * @author     Chetu Team
 */
class Velocity_CreditCard_Block_Form_Cc extends Mage_Payment_Block_Form_Cc {
    
    /**
     * Internal constructor
     * Set Cc template for payment form
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('creditcard/cc.phtml');              
    }
    
}