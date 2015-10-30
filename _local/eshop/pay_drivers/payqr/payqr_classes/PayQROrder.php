<?php

class PayQROrder {
	
	/**
	 * @param int $member_id
	 * @param string $contact
	 * @param payqr_receiver $Payqr
	 * @param int $clean_total
	 * @param int $delivery_id
	 * @param string $orderStatus
	 * 
	 * @return int|null
	 */
	public static function setOrderData($member_id, $contact, $Payqr, $delivery_id = null, $orderStatus = 'draft')
	{
            $customer_info = $Payqr->objectOrder->getCustomer();
            
            $total = $Payqr->objectOrder->getAmount();

            $oOrderModelItem = AMI::getResourceModel('eshop_order/table')->getItem();
            $oOrderModelItem->name = 'PayQR заказ';
            $oOrderModelItem->id_member = $member_id;
            $oOrderModelItem->statuses_history = self::getStatusHistory($orderStatus);
            $oOrderModelItem->custinfo = self::getCustInfo($contact, $delivery_id, $Payqr);
            $oOrderModelItem->sysinfo = self::getSysInfo($delivery_id);
            $oOrderModelItem->status = $orderStatus;
            $oOrderModelItem->comments = 'Заказ создан при использовании PayQR. ' . $contact;
            $oOrderModelItem->adm_comments = '';
            $oOrderModelItem->email = isset($customer_info->email)? $customer_info->email : "";
            $oOrderModelItem->firstname = isset($customer_info->firstName)? $customer_info->firstName : "";
            $oOrderModelItem->lastname = isset($customer_info->lastName)? $customer_info->lastName : "";
            $oOrderModelItem->username = isset($customer_info->firstName)? $customer_info->firstName : "";
            
            $oOrderModelItem->sysinfo = serialize(array(
                'person_type' => 'natural',
                'ip' => $_SERVER['REMOTE_ADDR'],
                'driver' => 'payqr',
                'fee_percent' => 0.00,
                'fee_curr' => 0.00,
                'fee_const' => 0.00
            ));
            
            $oOrderModelItem->ext_data = self::getExtData($total);
            $oOrderModelItem->lang = 'ru';
            $oOrderModelItem->total = self::calcCleanTotal($Payqr);
            $oOrderModelItem->shipping = PayQRDelivery::calculateDeliveryAmount($delivery_id, self::calcCleanTotal($Payqr));
            $oOrderModelItem->save();

            $order_id = $oOrderModelItem->getId();		

            return !empty($order_id)? $order_id : null;
	}

    /**
     * @param $data_cart_products
     * @param $order_id
     */
    public static function setOrderItemsData($data_cart_products, $order_id)
	{
		foreach($data_cart_products as $product)
		{
			$product_id = self::checkProduct($product);

			if(is_null($product_id))
			{
				continue;
			}

			$ext_data = self::getExtDataOrderItems($product_id);

			$oQuery = DB_Query::getInsertQuery('cms_es_order_items', 
												array(
													'id_order' => $order_id,
													'id_product' => $product_id,
													'id_prop' => 0,
													'owner_name' => 'eshop',
													'price' => $product->amount,
													'price_number' => "",
													'qty' => $product->quantity,
													'ext_data' => $ext_data
												)
											);

			AMI::getSingleton('db')->query($oQuery);
		}
	}

	/**
	 * @param string $status
	 * @return array
	 */
	private static function getStatusHistory($status = 'draft')
	{
		return serialize(
				array(
					1 =>array( // 1-> необходимо разобраться, что-за параметр
					'type' => 'user',
					'status' => $status, // этот параметр будет меняться при изменении статусов заказа, см. поля в админке
					'ip' => $_SERVER['REMOTE_ADDR'],
					'comments' => 'Заказ создан при использовании PayQR'
				)
			)
		);
	}

	/**
	 * @param string $contact
	 * @param int $delivery_id
	 * @return array
	 */
	private static function getCustInfo($contact, $delivery_id = null, $Payqr = array())
	{
		$sysinfo = PayQRDelivery::getCustInfoDelivery($Payqr, $delivery_id);

		return serialize($sysinfo);
	}

	/**
	 * @param int $delivery_id
	 * @return array
	 */
	private static function getSysInfo($delivery_id = null)
	{
		$sysinfo = PayQRDelivery::getSysInfoDelivery($delivery_id);

		return serialize($sysinfo);
	}

	/**
	 * 
	 * @return array
	 */
	private static function getExtData($price)
	{
		return 
			serialize(
				array(
				"base_currency" => array(
	            	"code" => "RUR",
	            	"exchange" => 1
	        	),
	    		"buy_currency" => array(
	            	"code" => "RUR",
	            	"exchange" => 1
	        	),
	    		"shipping_const" => 0,
	    		"currency" => array(
		            "" => array(
		                    "name" => "",
		                    "prefix" => "",
		                    "postfix" => "&nbsp;руб",
		                    "exchange" => "1",
		                    "is_base" => "",
		                ),
		            "RUR" => array(
		                    "name" => "Руб",
		                    "code_digit" => $price,
		                    "prefix" => "",
		                    "postfix" => "р.",
		                    "exchange" => "1",
		                    "source" => "",
		                    "fault_attempts" => "0",
		                    "is_base" => "1",
		                    "on_small" => "0",
		                    "id" => "1",
		                ),
		            "USD" => array(
		                    "name" => "USD",
		                    "code_digit" => $price * 0.03749,
		                    "prefix" => "",
		                    "postfix" => "",
		                    "exchange" => "0.03749",
		                    "source" => "",
		                    "fault_attempts" => "0",
		                    "is_base" => "0",
		                    "on_small" => "1",
		                    "id" => "3",
		                ),
	        	),
	    		"discountForUser" => array()
	    	)
		);
	}

	/**
	 * @param int $product_id
	 * @return string
	 */
	private static function getExtDataOrderItems($product_id)
	{
		AMI::initModExtensions('eshop_item');

		//получаем товар по данным, которые находятся в data_cart PayQR
		$product = AMI::getResourceModel('eshop_item/table')->find($product_id);

		if(empty($product) || empty($product_id)) return "";

		return serialize(
				array(
					"price_currency" => array(
						$product->id => array(
							"0" => array(
								"code" => "RUR",
								"exchange" => 1
							)
						)
					),
					"name" => $product->header,
					"currency" => array(
						"code" => "RUR",
						"exchange" => 1
					),
					"item_info" => array(
						"id" => $product->id,
						"rest" => 0,
						"item_type" => "eshop_goods",
						"variable_price" => 0,
						"cat_id" => $product->id_category,
						"id_prop" => 0,
						"id_external" => "Amiro_gen_" . $product->id,
						"name" => $product->header,
						"cat_name" => "",
						"prop_info" => array(),
						"sku" => $product->sku,
						"sublink" => $product->sublink,
						"descr_empty" => 1,
						"price" => $product->price,
						"cur_price" => self::calcDiscount($product),
						"cur_price_tax" => $product->price,
						"order_price" => self::calcDiscount($product),
						"original_price" => $product->price,
						"absolute_discount" => $product->price - self::calcDiscount($product),
						"percentage_discount" => $product->discount,
						"shipping_method_name" => "",
						"tax" => 0,
						"tax_item_value" => 0,
						"tax_type" => "percent",
						"tax_item" => 0,
						"shipping" => 0,
						"shipping_type" => "abs",
						"shipping_item" => 0,
						"price_number" => 0,
						"price_null" => "",
						"currency" => "RUR",
						"owner_name" => "eshop",
						"weight" => 0,
						"size" => "",
						"picture" => "",
						"small_picture" => "",
						"popup_picture" => $product->ext_img_popup
					),
					"property_caption" => "",
					"absolute_discount" => "0.00",
					"percentage_discount" => "0.00",
				)
		);
	}

	/**
	 * @param array $data_cart_product
	 * @return int|null $product_id
	 */
	private static function checkProduct($data_cart_product)
	{
		$product_id = null;

		if(!isset($data_cart_product->article) && empty($data_cart_product->article))
		{
			return null;
		}

		/**
		* Данный код закомментирован, поскольку в системе обнаружены дубликаты артикулов
		*/
		// $oQuery = new DB_Query('cms_es_items');
		// $oQuery->addField('id');
		// $oQuery->setWhereDef(DB_Query::getSnippet('AND sku = %s')->q($data_cart_product->article));
		// $result = AMI::getSingleton('db')->fetchRow($oQuery);

		// if(!isset($result['id']) || empty($result['id']))
		// {
			$result = AMI::getResourceModel('eshop_item/table')->find($data_cart_product->article);

			if(empty($result) || empty($result->id)) 
			{
				return null;
			}

			$product_id = $result->id;
		// }
		// else 
		// {
		// 	$product_id = $result['id'];
		// }

		return $product_id;
	}

	/**
	 * @param array $item
	 * @return float $price
	 */
	public static function calcDiscount($item)
	{
		$price = 0;

		if(isset($item->price) && !empty($item->price))
		{
			$price = $item->price;
		}

		if(isset($item->discount) && !empty($item->discount) && !empty($item->discount_type))
		{
			if($item->discount_type == "percent")
			{
				$price = $price - ($price * $item->discount/100);
			}
		}

		return $price;
	}

	/**
	 * @param payqr_receiver $Payqr
	 * 
	 * 
	 */
	private static function calcCleanTotal($Payqr)
	{
		$clean_total = 0;

		foreach($Payqr->objectOrder->getCart() as $product)
		{
			$clean_total += $product->amount * $product->quantity;
		}

		return $clean_total;
	}

	/**
	 * @param string  $message optional null
	 * @param integer $line optional 0
	 * @param bool    $debug optional false
	 * @param bool    $delete_old_log_file optional false
	 */
	private static function __log($message = null, $line = 0, $debug = false, $delete_old_log_file = false)
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
}