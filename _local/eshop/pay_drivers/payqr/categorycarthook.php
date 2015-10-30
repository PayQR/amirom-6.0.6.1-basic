<?php
require_once __DIR__ . '/payqr_classes/PayQRButton.php';
require_once __DIR__ . '/payqr_classes/PayQROrder.php';
require_once __DIR__ . '/payqr_classes/PayQRBSettings.php';
require_once __DIR__ . '/payqr_classes/PayQRCart.php';
require_once __DIR__ . '/payqr_classes/PayQR_AmiUser.php';

/**
 * @param int|null $product_id
 * @param string $page
 * @param int|null $member_id
 * @param bool $debug
 * @return string
 */

function initPayQRButton( $page = "category", $product_id = null, $member_id = null, $debug  = true)
{
    $payqr_button = "";

	if($page == 'cart')
	{
        //
		$data_cart = PayQRCart::getProductCart();

		$amount = PayQRCart::getTotal();

		if($amount == 0 || empty($data_cart))
		{
			return "";
		}

		$data_cart = json_encode($data_cart,JSON_UNESCAPED_UNICODE);
	}
	else 
	{
		AMI::initModExtensions('eshop_item');

		//получаем информацию о кнопке
		$product = AMI::getResourceModel('eshop_item/table')->find($product_id);

		if(empty($product) || empty($product_id)) return "";

		/**
		* Данный код закомментирован, поскольку в системе обнаружены дубликаты артикулов
		*/
		$data_cart = array(
			"article" => /*isset($product->sku) && !empty($product->sku)? $product->sku :*/ $product_id,
			"name" =>  $product->header,
			"imageUrl" => empty($product->ext_img_popup)?"":(strpos($product->ext_img_popup, 'http')!==false?$product->ext_img_popup: "http://" . $_SERVER[HTTP_HOST] . "/" . $product->ext_img_popup),
			"amount" => PayQROrder::calcDiscount($product)/*$product->price*/,
			"quantity" => 1
		);

		$amount = PayQROrder::calcDiscount($product)/*$product->price*/;
		$data_cart = json_encode(array($data_cart),JSON_UNESCAPED_UNICODE);
	}

	$pqBSettings = PayQRBSettings::initConfig();
	$payqr_settings = $pqBSettings->getSettings();

	//производим инициализацию кнопки
	$pq_bytton = new PayQRButton($page, $payqr_settings);
	
	if($pq_bytton->isShow())
	{
		//init payqr button config
		$css = $pq_bytton->init($member_id);

		//create html code for payqr button
		$payqr_button = "<button ". $css . "
								data-scenario=\"buy\" 
								data-cart='" . $data_cart . "' 
								data-amount='" . $amount . "'
								" . $pq_bytton->initUserData(array('user_id'=>$member_id, 'page' => $page)) . "
								>
							Купить быстрее
						</button>";
	}

	return $payqr_button;
}

function getPayQRLogFile()
{
	$result = AMI::getSingleton('db')->fetchCol("SELECT settings FROM `cms_pay_drivers` WHERE `name` like '%payqr%'");

	foreach($result as $settings)
	{
		if(unserialize($settings) != false)
		{
			$payqr_settings = unserialize($settings);
		}
	}

	if(empty($payqr_settings) || empty($payqr_settings['payqr_log_url']))
	{
		return 'http://' . $_SERVER['HTTP_HOST'] . '/' . 'payqr.log';
	}

	return $payqr_settings['payqr_log_url'];
}

function getCurrentUser()
{
	$oSession = AMI::getSingleton('env/session');
	$oUser = $oSession->getUserData();
	return is_object($oUser) ? $oUser->getId() : 0;
}

function getPayQRReceiverScriptFile()
{
	return 'http://' . $_SERVER['HTTP_HOST'] . '/' . 'payqr_receiver.php';
}

/**
 * @param null $message
 * @param int $line
 * @param bool $debug
 * @param bool $delete_old_log_file
 */
function __log($message = null, $line = 0, $debug = false, $delete_old_log_file = false)
{
	if($delete_old_log_file)
	{
		@unlink("__worker.log");
	}

	if(empty($message) || !$debug)
	{
		return;
	}


	$fp = fopen("__worker.log", "a");

	fwrite($fp, "[" . $line . "]\r\n");

	fwrite($fp, "\t" . $message . "\r\n");

	fclose($fp);
}