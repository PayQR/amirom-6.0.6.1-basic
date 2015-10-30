<?php
/**
 * @param new payqr_receiver $Payqr
 * @return bool
 */
function SucceedOrderStatus($Payqr)
{
    //получаем статусы заказов
    $pqBSettings = PayQRBSettings::initConfig();
    $payqr_settings = $pqBSettings->getSettings();

    $status_succeed = isset($payqr_settings['payqr_status_completed']) && !empty($payqr_settings['payqr_status_completed'])? $payqr_settings['payqr_status_completed'] : 'confirmed_done';

    //производим обновление заказа
    $order_id = $Payqr->objectOrder->getOrderId();

    if(isset($order_id) && !empty($order_id))
    {
        //фэйлим заказ клиента
        $oQuery = DB_Query::getUpdateQuery('cms_es_orders', array('status' => $status_succeed), DB_Query::getSnippet('WHERE id = %s')->q($order_id));
        AMI::getSingleton('db')->query($oQuery);

        return true;
    }
    return false;
}