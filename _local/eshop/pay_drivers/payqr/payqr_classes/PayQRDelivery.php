<?php

class PayQRDelivery {

	/**
	 * @return array
	 */
	public static function getDeliveries()
	{
            $parents = array();
            
            $AMI_parents = self::getParentDeliveries();
            
            foreach($AMI_parents as $parent)
            {
                $oQuery = new DB_Query('cms_es_shipping_methods');
                $oQuery->addFields(array('amount', 'id', 'name', 'type', 'delivery_time', 'comments', 'max_total', 'max_value', 'custom_conditions'));
                $oQuery->addWhereDef(
                    DB_Query::getSnippet("AND hidden = 0 AND lang = %s AND amount >= 0 ")->q('ru')
                );
                $oQuery->addWhereDef(
                    DB_Query::getSnippet("AND id_parent = %s")->q($parent['id'])
                );
                $oQuery->addWhereDef(
                    DB_Query::getSnippet("AND custom_conditions <> %s")->q('value')
                );
                $oQuery->setOrder('max_total', 'desc');
                
                $result = AMI::getSingleton('db')->select($oQuery);
                
                $deliveries = array();
                
                if( !empty($result->count()) )
                {
                    foreach($result as $delivery)
                    {
                        $deliveries[] = array(
                            'max_total' => (int)$delivery['max_total'] == 0 ? 1000000 : (int)$delivery['max_total'],
                            'amount' => $delivery['amount'],
                            'name' => $delivery['name'],
                            'type' => $delivery['type'],
                            'delivery_time' => $delivery['delivery_time'],
                            'comments' => $delivery['comments'],
                            'id' => $delivery['id'],
                            'custom_conditions' => $delivery['custom_conditions']
                        );
                    }
                    asort($deliveries);
                }
                else
                {
                    $oQuery = new DB_Query('cms_es_shipping_methods');
                    $oQuery->addFields(array('amount', 'id', 'name', 'type', 'delivery_time', 'comments', 'max_total', 'max_value', 'custom_conditions'));
                    $oQuery->addWhereDef(
                        DB_Query::getSnippet("AND hidden = 0 AND lang = %s AND amount >= 0 ")->q('ru')
                    );
                    $oQuery->addWhereDef(
                        DB_Query::getSnippet("AND id = %s")->q($parent['id'])
                    );
                    
                    $result = AMI::getSingleton('db')->select($oQuery);
                    
                    foreach($result as $delivery)
                    {
                        $deliveries[] = array(
                                'amount' => $delivery['amount'],
                                'name' => $delivery['name'],
                                'type' => $delivery['type'],
                                'delivery_time' => $delivery['delivery_time'],
                                'comments' => $delivery['comments'],
                                'id' => $delivery['id'],
                                'max_total' => $delivery['max_total'],
                                'custom_conditions' => $delivery['custom_conditions']
                        );
                    }
                }
                
                $parents[$parent['id']] = $deliveries;
            }
            
            return $parents;
	}
        
        public static function getParentDeliveries()
        {
            $oQuery = new DB_Query('cms_es_shipping_methods');
            $oQuery->addFields(array('id'));
            $oQuery->addWhereDef(
                DB_Query::getSnippet("AND hidden = 0 AND lang = %s AND id_parent = 0 ")->q('ru')
            );
            $oQuery->addWhereDef(
                DB_Query::getSnippet("AND name <> %s")->q('Самовывоз')
            );
            
            $result = AMI::getSingleton('db')->select($oQuery);
            
            return $result;
        }

        /**
	 * @param int $delivery_id
	 * @return array
	 */
	public static function getDelivery($delivery_id = null)
	{
		if(is_null($delivery_id))
		{
			return null;
		}

		$oQuery = new DB_Query('cms_es_shipping_methods');
		$oQuery->addFields(array('id', 'name', 'amount', 'type', 'delivery_time', 'comments'));
		$oQuery->addWhereDef(
			DB_Query::getSnippet("AND id = %s")->q($delivery_id)
		);

		$delivery = AMI::getSingleton('db')->fetchRow($oQuery);

		return $delivery;
	}

	/**
	 * @param int $delivery_id
	 * @return array
	 */
	public static function getSysInfoDelivery($delivery_id = null)
	{
		$delivery = self::getDelivery($delivery_id);

		if(empty($delivery))
		{
			return array();
		}

		//формируем информацию о доставке для заказа
		$delivery = array(
                    "person_type" => "natural",
		    "ip" => $_SERVER['REMOTE_ADDR'],
		    "driver" => "stub",
		    "fee_percent" => "0.00",
		    "fee_curr" => "",
		    "fee_const" => "0.00"
		);

		return $delivery;
	}

	/**
	 * @param payqr_invoice new $Payqr
         * @param int $delivery_id
         * @param string $contact
	 * @return array
	 */
	public static function getCustInfoDelivery($Payqr, $delivery_id = null)
	{
            $delivery = self::getDelivery($delivery_id);
            
            //очищаем ненужную информацию для вывода
            if(isset($delivery["amount"]))
            {
                unset($delivery["amount"]);
            }
            
            //формируем информацию о доставке для заказа
            $delivery['get_type_name'] = isset($delivery['name'])? $delivery['name'] : "";
            
            $cData = $Payqr->objectOrder->getCustomer();
            $delivery['contact'] = isset($cData->phone)? $cData->phone : "";

            $user_delivery = $Payqr->objectOrder->getDelivery();
            
            if(!empty($user_delivery))
            {
                if(isset($user_delivery->street))
                {
                        $delivery['street'] = $user_delivery->street;
                }
                if(isset($user_delivery->house))
                {
                        $delivery['house'] = $user_delivery->house;
                }
                if(isset($user_delivery->building))
                {
                        $delivery['building'] = $user_delivery->building;
                }
                if(isset($user_delivery->floor))
                {
                        $delivery['floor'] = $user_delivery->floor;
                }
                if(isset($user_delivery->zip))
                {
                        $delivery['code'] = $user_delivery->zip;
                }
                if(isset($user_delivery->flat))
                {
                        $delivery['app'] = $user_delivery->flat;
                }
                if(isset($user_delivery->hallway))
                {
                        $delivery['entrance'] = $user_delivery->hallway;
                }
            }
            $delivery['delivery_date'] = '';
            $delivery['delivery_time'] = '';
            $delivery['station'] = '';
            $delivery['shipping_conflicts'] = 'show_intersection';

            return $delivery;
	}

	/**
	 * @param int $delivery_id
	 * 
	 */
	public static function calculateDeliveryAmount($delivery_id, $cart_amount)
	{
		$shipping_cost = 0;

		$delivery = self::getDelivery($delivery_id);

		if(empty($delivery))
		{
			return $shipping_cost;
		}

		$delivery = PayQRDelivery::getDelivery($delivery_id);

		if(!isset($delivery['type']) || !isset($delivery['amount']))
		{
			return $shipping_cost;
		}

		if($delivery['type'] == 'abs')
		{
			$shipping_cost = $delivery['amount'];
		}
		if($delivery['type'] == 'percent')
		{
			$shipping_cost = round($cart_amount * $delivery['amount'] / 100, 1);
		}

		return $shipping_cost;
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