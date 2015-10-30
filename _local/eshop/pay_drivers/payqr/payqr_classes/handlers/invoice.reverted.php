<?php
/**
 * Код в этом файле будет выполнен, когда интернет-сайт получит уведомление от PayQR о полной отмене счета (заказа) после его оплаты.
 * Это означает, что посредством запросов в PayQR интернет-сайт либо одной полной отменой, либо несколькими частичными отменами вернул всю сумму денежных средств по конкретному счету (заказу).
 *
 * $Payqr->objectOrder содержит объект "Счет на оплату" (подробнее об объекте "Счет на оплату" на https://payqr.ru/api/ecommerce#invoice_object)
 *
 * Ниже можно вызвать функции своей учетной системы, чтобы особым образом отреагировать на уведомление от PayQR о событии invoice.reverted.
 *
 * Получить orderId из объекта "Счет на оплату", по которому произошло событие, можно через $Payqr->objectOrder->getOrderId();
 */

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
    
//устаналиваем сообщение для пользователя
if(isset($payqr_settings['payqr_reverted_message_text'], $payqr_settings['payqr_reverted_message_imageurl'], $payqr_settings['payqr_reverted_message_url']))
{
    $Payqr->objectOrder->data->message->article = 1;
    $Payqr->objectOrder->data->message->text = $payqr_settings['payqr_reverted_message_text'];
    $Payqr->objectOrder->data->message->url = $payqr_settings['payqr_reverted_message_url'];
    $Payqr->objectOrder->data->message->imageUrl = $payqr_settings['payqr_reverted_message_imageurl'];
}