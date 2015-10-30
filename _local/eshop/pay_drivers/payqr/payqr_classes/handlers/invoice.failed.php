<?php

/**
 * @param new payqr_receiver $Payqr
 * @return bool
 */
function FailOrderStatus($Payqr)
{
	//получаем статусы заказов
	$pqBSettings = PayQRBSettings::initConfig();
	$payqr_settings = $pqBSettings->getSettings();

	$status_cancelled = isset($payqr_settings['payqr_status_canceled']) && !empty($payqr_settings['payqr_status_canceled'])? $payqr_settings['payqr_status_canceled'] : 'cancelled';

	//производим обновление заказа
    $order_id = $Payqr->objectOrder->getOrderId();

    if(isset($order_id) && !empty($order_id))
    {
        //фэйлим заказ клиента
        $oQuery = DB_Query::getUpdateQuery('cms_es_orders', array('status' => $status_cancelled), DB_Query::getSnippet('WHERE id = %s')->q($order_id));
        AMI::getSingleton('db')->query($oQuery);
    }
    return false;
}