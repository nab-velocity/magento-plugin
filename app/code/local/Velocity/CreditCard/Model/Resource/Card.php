<?php

/**
 * Model Resource Card
 *
 * @category   velocity
 * @package    CreditCard
 * @author     Chetu Team
 */

/**
 * Description this is use to save the transaction response 
 * in velocity_transactions custom table on the basis of id. 
 */

class Velocity_CreditCard_Model_Resource_Card extends Mage_Core_Model_Mysql4_Abstract {
    
    /**
     * This constructor use for set model connectivity
     * for database intraction.
     */	 
    public function _construct() {
        $this->_init('creditcard/card', 'id');
    }
}

?>
