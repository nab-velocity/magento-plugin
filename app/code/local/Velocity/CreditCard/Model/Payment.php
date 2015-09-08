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
        
        if ($testmode) {
            $isTestAccount        = true;
            $identitytoken        = "PHNhbWw6QXNzZXJ0aW9uIE1ham9yVmVyc2lvbj0iMSIgTWlub3JWZXJzaW9uPSIxIiBBc3NlcnRpb25JRD0iXzdlMDhiNzdjLTUzZWEtNDEwZC1hNmJiLTAyYjJmMTAzMzEwYyIgSXNzdWVyPSJJcGNBdXRoZW50aWNhdGlvbiIgSXNzdWVJbnN0YW50PSIyMDE0LTEwLTEwVDIwOjM2OjE4LjM3OVoiIHhtbG5zOnNhbWw9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjEuMDphc3NlcnRpb24iPjxzYW1sOkNvbmRpdGlvbnMgTm90QmVmb3JlPSIyMDE0LTEwLTEwVDIwOjM2OjE4LjM3OVoiIE5vdE9uT3JBZnRlcj0iMjA0NC0xMC0xMFQyMDozNjoxOC4zNzlaIj48L3NhbWw6Q29uZGl0aW9ucz48c2FtbDpBZHZpY2U+PC9zYW1sOkFkdmljZT48c2FtbDpBdHRyaWJ1dGVTdGF0ZW1lbnQ+PHNhbWw6U3ViamVjdD48c2FtbDpOYW1lSWRlbnRpZmllcj5GRjNCQjZEQzU4MzAwMDAxPC9zYW1sOk5hbWVJZGVudGlmaWVyPjwvc2FtbDpTdWJqZWN0PjxzYW1sOkF0dHJpYnV0ZSBBdHRyaWJ1dGVOYW1lPSJTQUsiIEF0dHJpYnV0ZU5hbWVzcGFjZT0iaHR0cDovL3NjaGVtYXMuaXBjb21tZXJjZS5jb20vSWRlbnRpdHkiPjxzYW1sOkF0dHJpYnV0ZVZhbHVlPkZGM0JCNkRDNTgzMDAwMDE8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgQXR0cmlidXRlTmFtZT0iU2VyaWFsIiBBdHRyaWJ1dGVOYW1lc3BhY2U9Imh0dHA6Ly9zY2hlbWFzLmlwY29tbWVyY2UuY29tL0lkZW50aXR5Ij48c2FtbDpBdHRyaWJ1dGVWYWx1ZT5iMTVlMTA4MS00ZGY2LTQwMTYtODM3Mi02NzhkYzdmZDQzNTc8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgQXR0cmlidXRlTmFtZT0ibmFtZSIgQXR0cmlidXRlTmFtZXNwYWNlPSJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcyI+PHNhbWw6QXR0cmlidXRlVmFsdWU+RkYzQkI2REM1ODMwMDAwMTwvc2FtbDpBdHRyaWJ1dGVWYWx1ZT48L3NhbWw6QXR0cmlidXRlPjwvc2FtbDpBdHRyaWJ1dGVTdGF0ZW1lbnQ+PFNpZ25hdHVyZSB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnIyI+PFNpZ25lZEluZm8+PENhbm9uaWNhbGl6YXRpb25NZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzEwL3htbC1leGMtYzE0biMiPjwvQ2Fub25pY2FsaXphdGlvbk1ldGhvZD48U2lnbmF0dXJlTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3JzYS1zaGExIj48L1NpZ25hdHVyZU1ldGhvZD48UmVmZXJlbmNlIFVSST0iI183ZTA4Yjc3Yy01M2VhLTQxMGQtYTZiYi0wMmIyZjEwMzMxMGMiPjxUcmFuc2Zvcm1zPjxUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjZW52ZWxvcGVkLXNpZ25hdHVyZSI+PC9UcmFuc2Zvcm0+PFRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAveG1sLWV4Yy1jMTRuIyI+PC9UcmFuc2Zvcm0+PC9UcmFuc2Zvcm1zPjxEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjc2hhMSI+PC9EaWdlc3RNZXRob2Q+PERpZ2VzdFZhbHVlPnl3NVZxWHlUTUh5NUNjdmRXN01TV2RhMDZMTT08L0RpZ2VzdFZhbHVlPjwvUmVmZXJlbmNlPjwvU2lnbmVkSW5mbz48U2lnbmF0dXJlVmFsdWU+WG9ZcURQaUorYy9IMlRFRjNQMWpQdVBUZ0VDVHp1cFVlRXpESERwMlE2ZW92T2lhN0pkVjI1bzZjTk1vczBTTzRISStSUGRUR3hJUW9xa0paeEtoTzZHcWZ2WHFDa2NNb2JCemxYbW83NUFSWU5jMHdlZ1hiQUVVQVFCcVNmeGwxc3huSlc1ZHZjclpuUytkSThoc2lZZW4vT0VTOUdtZUpsZVd1WUR4U0xmQjZJZnd6dk5LQ0xlS0FXenBkTk9NYmpQTjJyNUJWQUhQZEJ6WmtiSGZwdUlablp1Q2l5OENvaEo1bHU3WGZDbXpHdW96VDVqVE0wU3F6bHlzeUpWWVNSbVFUQW5WMVVGMGovbEx6SU14MVJmdWltWHNXaVk4c2RvQ2IrZXpBcVJnbk5EVSs3NlVYOEZFSEN3Q2c5a0tLSzQwMXdYNXpLd2FPRGJJUFpEYitBPT08L1NpZ25hdHVyZVZhbHVlPjxLZXlJbmZvPjxvOlNlY3VyaXR5VG9rZW5SZWZlcmVuY2UgeG1sbnM6bz0iaHR0cDovL2RvY3Mub2FzaXMtb3Blbi5vcmcvd3NzLzIwMDQvMDEvb2FzaXMtMjAwNDAxLXdzcy13c3NlY3VyaXR5LXNlY2V4dC0xLjAueHNkIj48bzpLZXlJZGVudGlmaWVyIFZhbHVlVHlwZT0iaHR0cDovL2RvY3Mub2FzaXMtb3Blbi5vcmcvd3NzL29hc2lzLXdzcy1zb2FwLW1lc3NhZ2Utc2VjdXJpdHktMS4xI1RodW1icHJpbnRTSEExIj5ZREJlRFNGM0Z4R2dmd3pSLzBwck11OTZoQ2M9PC9vOktleUlkZW50aWZpZXI+PC9vOlNlY3VyaXR5VG9rZW5SZWZlcmVuY2U+PC9LZXlJbmZvPjwvU2lnbmF0dXJlPjwvc2FtbDpBc3NlcnRpb24+";
            $workflowid           = '2317000001';
            $applicationprofileid = 14644;  // applicationprofileid provided velocity
            $merchantprofileid    = 'PrestaShop Global HC';
        } else {
            $isTestAccount        = false;
            $identitytoken        = Mage::getStoreConfig('payment/creditcard/identity_token');
            $workflowid           = Mage::getStoreConfig('payment/creditcard/workflow_id');
            $applicationprofileid = Mage::getStoreConfig('payment/creditcard/application_profile_id');
            $merchantprofileid    = Mage::getStoreConfig('payment/creditcard/merchant_profile_id');
        }
        
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