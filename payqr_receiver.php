<?php

$AMI_ENV_SETTINGS = array(
    'response_mode'     => 'HTML',
    'response_buffered' => true,
);
 
require 'ami_env.php';

define('PAYQR_PATH_ROOT', '_local/eshop/pay_drivers/payqr/payqr_classes');

require_once PAYQR_PATH_ROOT . '/payqr_config.php';


//получаем секретные ключи из БД по нашей платежной системе
$oNewsModelList = AMI::getResourceModel('payment_drivers/table')->getList();

$oNewsModelList->addWhereDef(
 DB_Query::getSnippet("AND name LIKE %s")
 	->q('%payqr%')
);

$oNewsModelList->addColumns(array('id', 'header', 'settings'));

$oNewsModelList->load();

$payqr_settings = $payqr_id = null;

foreach ($oNewsModelList as $oNewsModelItem)
{
	$payqr_settings = unserialize($oNewsModelItem->settings);
	$payqr_id = $oNewsModelItem->id;
}

if(empty($payqr_settings))
{
	exit('PayQR settings failed');
}

payqr_config::init($payqr_settings['payqr_merchant_id'], $payqr_settings['payqr_merchant_secret_key_in'], $payqr_settings['payqr_merchant_secret_key_out']);
payqr_config::$logFile = "payqr.log";
payqr_config::$enabledLog = true;
//

require PAYQR_PATH_ROOT . '/payqr_receiver.php';