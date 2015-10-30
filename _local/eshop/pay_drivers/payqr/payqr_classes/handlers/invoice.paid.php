<?php
/**
 * @param new payqr_receiver $Payqr
 * @return bool
 */
function PaidOrderStatus($Payqr)
{
    //получаем статусы заказов
    $pqBSettings = PayQRBSettings::initConfig();
    $payqr_settings = $pqBSettings->getSettings();

    $status_accepted = isset($payqr_settings['payqr_status_paid']) && !empty($payqr_settings['payqr_status_paid'])? $payqr_settings['payqr_status_paid'] : 'accepted';

    //производим обновление заказа
    $order_id = $Payqr->objectOrder->getOrderId();

    if(isset($order_id) && !empty($order_id))
    {
        //фэйлим заказ клиента
        $oQuery = DB_Query::getUpdateQuery('cms_es_orders', array('status' => $status_accepted), DB_Query::getSnippet('WHERE id = %s')->q($order_id));
        AMI::getSingleton('db')->query($oQuery);
    }
    
    //устаналиваем сообщение для пользователя
    if(isset($payqr_settings['payqr_paid_message_text'], $payqr_settings['payqr_paid_message_imageurl'], $payqr_settings['payqr_paid_message_url']))
    {
        $Payqr->objectOrder->data->message->article = 1;
        $Payqr->objectOrder->data->message->text = $payqr_settings['payqr_paid_message_text'];
        $Payqr->objectOrder->data->message->url = $payqr_settings['payqr_paid_message_url'];
        $Payqr->objectOrder->data->message->imageUrl = $payqr_settings['payqr_paid_message_imageurl'];
    }
}