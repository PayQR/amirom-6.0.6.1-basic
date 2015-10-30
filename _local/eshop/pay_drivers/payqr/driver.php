<?php
/**
 * @copyright Amiro.CMS. All rights reserved.
 * @category  AMI
 * @package   Driver_PaymentSystem
 * @version   $Id: driver.php 61249 2013-08-05 11:20:24Z Leontiev Anton $
 * @since     5.10.0
 */

/**
 * Example pay driver.
 * 
 * How to create your own pay driver:
 * 
 * <ol>
 * <li>Copy the directory "_local/eshop/pay_drivers/example" to another, i.e. "_local/eshop/pay_drivers/my_driver"</li>
 * <li>Open driver.php in your path and:
 * <ul>
 * <li>Rename driver class name to your name (MyDriver_PaymentSystemDriver). Naming rule: all words delimeted by _ should be started from uppercase letter. Not _ allowed in driver name.</li>
 * <li>In some cases you should to check or add some fields when printing payment button on checkout page. You can manipulate with $aData array in getPayButton method for it. The same for pay system button with autoredirect but the method is getPayButtonParams</li>
 * <li>User will come back to the site from payment system by special URL and system already has the checks for setting correct order status. If you want to make your manual checking do it in payProcess method.</li>
 * <li>For payment system background requests for order approving there is payCallback method. You need to override this method with you own check of payment data.</li>
 * <li>If get or post field are differ from order_id, id or item_number you need to override getProcessOrder method that will return valid order id from POST and GET request.
 Also, you have to implement the getOrderIdVarName() method, that will return real field name:
 <pre>
    public static function getOrderIdVarName(){
        return 'ID_ORDER_FIELD_NAME';
    }
 </pre>
 </li>
 * </ul>
 * </li>
 * <li>Open driver.tpl and modify sets:
 * <ul>
 * <li>settings_form - part of form that will be insertted to driver form when you open your driver for editing.</li>
 * <li>checkout_form - button that will be shown on checkout page after the list of items. ##hiddens## field is required.</li>
 * <li>pay_form - form that will be submitted to payment system. In most cases this form is made with autoredirect.</li>
 * <li>Also modify path to driver.lng.</li>
 * </ul>
 * </li>
 * <li>Captions for drivers you can set in driver.lng.</li>
 * <li>After all these steps install your driver in Settings/Pay drivers page of admin panel and edit parameters. Then include your diver for the payment in option "Allowed payment drivers" of Catalog : Orders setting.</li>
 * </ol>
 * 
 * @package Driver_PaymentSystem
 */
class Payqr_PaymentSystemDriver extends AMI_PaymentSystemDriver{

    /**
     * Get checkout button HTML form
     *
     * @param array $aRes Will contain "error" (error description, 'Success by default') and "errno" (error code, 0 by default). "forms" will contain a created form
     * @param array $aData The data list for button generation
     * @param bool $bAutoRedirect If form autosubmit required (directly from checkout page)
     * @return bool true if form is generated, false otherwise
     */
    public function getPayButton(&$aRes, $aData, $bAutoRedirect = false){
        // Format fields
        foreach(Array("return", "description") as $fldName){
            $aData[$fldName] = htmlspecialchars($aData[$fldName]);
        }

        $hiddens = '';
        foreach ($aData as $key => $value) {
            $hiddens .= '<input type="hidden" name="' . $key . '" value="' . (is_null($value) ? $aData[$key] : $value) .'" />' . "\n";
        }
        $aData['hiddens'] = $hiddens;

        // Disable to process order using example button
        $aData["disabled"] = "disabled";
        
        // Set your fields of $aData here

        return parent::getPayButton($aRes, $aData, $bAutoRedirect);
    }
    
    /**
     * Get the form that will be autosubmitted to payment system. This step is required for some shooping cart actions.
     *
     * @param array $aData The data list for button generation
     * @param array $aRes Will contain "error" (error description, 'Success by default') and "errno" (error code, 0 by default). "forms" will contain a created form
     * @return bool true if form is generated, false otherwise
     */
    public function getPayButtonParams($aData, &$aRes){
        // Check parameters and set your fields here

        return parent::getPayButtonParams($aData, $aRes);
    }

    /**
     * Verify the order from user back link. In success case 'accepted' status will be setup for order.
     *
     * @param array $aGet $_GET data
     * @param array $aPost $_POST data
     * @param array $aRes reserved array reference
     * @param array $aCheckData Data that provided in driver configuration
     * @param array $aOrderData order data that contains such fields as id, total, order_date, status
     * @return bool true if order is correct and false otherwise
     * @see AMI_PaymentSystemDriver::payProcess(...)
     */
    public function payProcess($aGet, $aPost, &$aRes, $aCheckData, $aOrderData){
        // See implplementation of this method in parent class
        
        return parent::payProcess($aGet, $aPost, $aRes, $aCheckData, $aOrderData);
    }

    /**
     * Verify the order by payment system background responce. In success case 'confirmed' status will be setup for order.
     *
     * @param array $aGet $_GET data
     * @param array $aPost $_POST data
     * @param array $aRes reserved array reference
     * @param array $aCheckData Data that provided in driver configuration
     * @param array $aOrderData order data that contains such fields as id, total, order_date, status
     * @return int -1 - ignore post, 0 - reject(cancel) order, 1 - confirm order
     * @see AMI_PaymentSystemDriver::payCallback(...)
     */
    public function payCallback($aGet, $aPost, &$aRes, $aCheckData, $aOrderData){
        // See implplementation of this method in parent class

        return parent::payCallback($aGet, $aPost, $aRes, $aCheckData, $aOrderData);
    }

    /**
     * Return real system order id from data that provided by payment system.
     *
     * @param array $aGet $_GET data
     * @param array $aPost $_POST data
     * @param array $aRes reserved array reference
     * @param array $aAdditionalParams reserved array
     * @return int order Id
     * @see AMI_PaymentSystemDriver::getProcessOrder(...)
     */
    public function getProcessOrder($aGet, $aPost, &$aRes, $aAdditionalParams){
        // See implplementation of this method in parent class
        
        return parent::getProcessOrder($aGet, $aPost, $aRes, $aAdditionalParams);
    }

    /**
     * Return var name for system order id from data that provided by payment system.
     *
     * @return string Var name
     */
    /*
    public static function getOrderIdVarName(){
        return 'ID_ORDER_FIELD_NAME';
    }
    */
}
