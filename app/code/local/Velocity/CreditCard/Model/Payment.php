<?php

/**
 *  This is include SDK for velocity gateway for the payment.
 */
require_once 'Velocity/CreditCard/sdk/Velocity.php';

/**
 * Model Payment
 *
 * @category   velocity
 * @package    CreditCard
 * @author     Chetu Team
 */

/**
 * Description this class perform the authorizeandcapture transaction for the 
 * fronend form and returnbyid transaction for refund the payment by. 
 */
class Velocity_CreditCard_Model_Payment extends Mage_Payment_Model_Method_Cc
{
    /**
     * Here are examples of flags that will determine functionality availability
     * of this module to be used by frontend and backend.
     *
     * @see all flags and their defaults in Mage_Payment_Model_Method_Abstract
     *
     */
     
    /**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway               = true;
    
    /**
     * Can authorize online?
     */
    protected $_canAuthorize            = true;
    
    /**
     * Can capture funds online?
     */
    protected $_canCapture              = true;
    
    /**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal          = true;
    
    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout          = true;
    
    /**
     * Can refund online?
     */
    protected $_canRefund               = true;
    
    /**
     * Can capture partial amounts online?
     */
    protected $_canCapturePartial       = false;
    
    /**
     * Can refund partial invoice online?
     */
    protected $_canRefundInvoicePartial = false;
    
    /**
     * Can void online?
     */
    protected $_canVoid                 = false;
    
    /**
     * Can use for muliple shipping online?
     */
    protected $_canUseForMultishipping  = false;
    
    /**
     * Can save Cc online?
     */
    protected $_canSaveCc               = false;
    
    /**
     * Can fetch the transaction information online?
     */
    protected $_canFetchTransactionInfo = false;
    
    /**
     * Can initialization nneded online?
     */
    protected $_isInitializeNeeded      = false;
    
    /**
     * Set currency code for transaction.
     */
    protected $_allowCurrencyCode       = array('USD');
    
    /**
    * unique internal payment method identifier
    */
    protected $_code                    = 'creditcard';
    
    /**
     * set form path for payment.
     */
    protected $_formBlockType           = 'creditcard/form_cc';
   
    private   $velocityProcessor;
    
    /** 
    * This method use to create velocity Processor class object
    *
    */
    private function _callVelocityGateway() 
    {
        $testmode             = Mage::getStoreConfig('payment/creditcard/test');
        $identitytoken        = Mage::getStoreConfig('payment/creditcard/identity_token');
        $workflowid           = Mage::getStoreConfig('payment/creditcard/workflow_id');
        $applicationprofileid = Mage::getStoreConfig('payment/creditcard/application_profile_id');
        $merchantprofileid    = Mage::getStoreConfig('payment/creditcard/merchant_profile_id');
        if ($testmode)
           $isTestAccount     = true;
        else
           $isTestAccount     = false;
        
        try {            
            $this->velocityProcessor = new VelocityProcessor( $applicationprofileid, $merchantprofileid, $workflowid, $isTestAccount, $identitytoken );
            Mage::log($this->velocityProcessor, 1);
        } catch (Exception $e) {
            
            $errorMsg = $this->_getHelper()->__($e->getMessage());
            Mage::throwException($errorMsg);
        }
    }
    
    /**
     * 
     * @param Varien_Object $payment
     * @param type $amount
     * @return of type Velocity_CreditCard_Model_Payment class $this object.
     */
    public function capture(Varien_Object $payment, $amount) {
        $this->_callVelocityGateway();
        try {
            $order = $payment->getOrder();
            $types = Mage::getSingleton('payment/config')->getCcTypes();

            if (isset($types[$payment->getCcType()])) {
                $type = $types[$payment->getCcType()];
            }

            $billingaddress = $order->getBillingAddress();
            $totals         = number_format($amount, 2, '.', '');
            $orderId        = $order->getIncrementId();

            $avsData = array (
                 'Street'       => $billingaddress->getData('street'),
                 'City'         => $billingaddress->getData('city'),
                 'StateProvince'=> $billingaddress->getData('region'),
                 'PostalCode'   => $billingaddress->getData('postcode'),
                 'Country'      => $billingaddress->getData('country_id')
             );
            
            $cardData = array(
                  'cardtype'    => ucfirst(str_replace(' ', '', $type)), 
                  'pan'         => $payment->getCcNumber(), 
                  'expire'      => sprintf("%02d", $payment->getCcExpMonth()).substr($payment->getCcExpYear(), -2), 
                  'cvv'         => $payment->getCcCid(),
                  'track1data'  => '', 
                  'track2data'  => ''
            );

          
            $response = $this->velocityProcessor->verify(array(  
                    'amount'       => $totals,
                    'avsdata'      => $avsData, 
                    'carddata'     => $cardData,
                    'entry_mode'   => 'Keyed',
                    'IndustryType' => 'Ecommerce',
                    'Reference'    => 'xyz',
                    'EmployeeId'   => '11'
            )); 

            if (isset($response['Status']) && $response['Status'] == 'Successful') {
                
                try {
                    $cap_response = $this->velocityProcessor->authorizeAndCapture( array(
                            'amount'       => $totals, 
                            'avsdata'      => $avsData,
                            'token'        => $response['PaymentAccountDataToken'], 
                            'order_id'     => $orderId,
                            'entry_mode'   => 'Keyed',
                            'IndustryType' => 'Ecommerce',
                            'Reference'    => 'xyz',
                            'EmployeeId'   => '11'
                    ));
                    
                    Mage::log(print_r($cap_response, 1));
                    
                    if ( is_array($cap_response) && !empty($cap_response) && isset($cap_response['Status']) && $cap_response['Status'] == 'Successful') {
                        $payment->setTransactionId($cap_response['TransactionId']);
                        $payment->setIsTransactionClosed(1);
                        
                        $xml = VelocityXmlCreator::authorizeandcaptureXML( array(
                                'amount'       => $totals, 
                                'avsdata'      => $avsData,
                                'token'        => $response['PaymentAccountDataToken'], 
                                'order_id'     => $orderId,
                                'entry_mode'   => 'Keyed',
                                'IndustryType' => 'Ecommerce',
                                'Reference'    => 'xyz',
                                'EmployeeId'   => '11'
                        ));
                        
                        $req     = $xml->saveXML();
                        $obj_req = serialize($req);
                        
                        $insertData = array(
                           'transaction_id'     => $cap_response['TransactionId'],
                           'transaction_status' => $cap_response['Status'],
                           'order_id'           => $orderId,
                           'request_obj'        => $obj_req,
                           'response_obj'       => serialize($cap_response)
                        );

                        $collectionSet = Mage::getModel('creditcard/card');
                        $collectionSet->setData($insertData)->save();
                    } else if ( is_array($cap_response) && !empty($cap_response) ) {
                        $errorMsg = $this->_getHelper()->__($cap_response['StatusMessage']);
                    } else {
                        $errorMsg = $this->_getHelper()->__($cap_response);
                    }
                } catch(Exception $e) {
                    Mage::throwException($e->getMessage());
                }
            } else if ((isset($response['Status']) && $response['Status'] != 'Successful')) {
                $errorMsg = $this->_getHelper()->__($response['StatusMessage']);
            } else {
                $errorMsg = $this->_getHelper()->__($response);
            }
            
    	} catch(Exception $e) {
            Mage::throwException($e->getMessage());
	}
        
        if (isset($errorMsg) && !empty($errorMsg)) {
            Mage::throwException($errorMsg);
        }
 
        return $this;
    }
    
    /**
     * 
     * @param Varien_Object $payment
     * @param type $amount
     * @return of type Velocity_CreditCard_Model_Payment class $this object.
     */
    public function refund(Varien_Object $payment, $amount) 
    {
        Mage::log($amount, 1);
        try {
            $this->_callVelocityGateway();
            $response = $this->velocityProcessor->returnById(array(  
                'amount'        => $amount,
                'TransactionId' => $payment->_data['last_trans_id']
           ));
            
           $xml = VelocityXmlCreator::returnByIdXML(number_format($amount, 2, '.', ''), $payment->_data['last_trans_id']);  // got ReturnById xml object.  

           $req = $xml->saveXML();
           $obj_req = serialize($req);
           Mage::log(print_r($response, 1));
           
           if (is_array($response) && !empty($response) && isset($response['Status']) && $response['Status'] == 'Successful') {
                $payment->setTransactionId($response['TransactionId']);
                $payment->setIsTransactionClosed(1); 

                $insertData = array(
                    'transaction_id'     => $response['TransactionId'],
                    'transaction_status' => $response['Status'],
                    'order_id'           => $response['OrderId'],
                    'request_obj'        => $obj_req,
                    'response_obj'       => json_encode($response)
                 );

                 $collectionSet = Mage::getModel('creditcard/card');
                 $collectionSet->setData($insertData)->save();
           } else if(is_array($response) && !empty($response)) {
                 $errorMsg = $this->_getHelper()->__($response['StatusMessage']);
           } else {
                 $errorMsg = $this->_getHelper()->__($response);
           }
           
        } catch(Exception $e) {
            Mage::throwException($e->getMessage());
        }
        
        if (isset($errorMsg) && !empty($errorMsg)) {
            Mage::throwException($errorMsg);
        }
        
        return $this;
   }
   
}