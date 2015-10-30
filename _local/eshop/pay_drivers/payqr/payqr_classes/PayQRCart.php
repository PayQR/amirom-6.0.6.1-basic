<?php

class PayQRCart /*extends AMI_EshopCart*/ {

	public static $productCart = array();

	public function __construct()
	{
	}
	
	public static function getTotal()
	{
		$total = AMI::getResource('eshop/cart')->getTotal();

		return isset($total["price"])? $total["price"] : 0;
	}

	public static function getItems()
	{
		return AMI::getResource('eshop/cart')->getItems();
	}

	public static function getProductCart()
	{
		AMI::initModExtensions('eshop_item');

		$items = AMI::getResource('eshop/cart')->getItems();

		$productCart = array();
		
		foreach($items as $item)
		{
			$oItem = $item->getItem();
			
			/**
			* Данный код закомментирован, поскольку в системе обнаружены дубликаты артикулов
			*/
            $product_data_cart_quantity = $item->getQty();

            $product_position_amount = self::calcDiscount($oItem) * $product_data_cart_quantity;
			
            $productCart[] = array(
				"article" => /*isset($oItem->sku) && !empty($oItem->sku)? $oItem->sku :*/ $oItem->id,
				"name" =>  $oItem->header,
				"imageUrl" => empty($oItem->ext_img_popup)?"":(strpos($oItem->ext_img_popup, 'http')!==false? $oItem->ext_img_popup: "http://" . $_SERVER[HTTP_HOST] . "/" . $oItem->ext_img_popup),
				"amount" => $product_position_amount,
				"quantity" => $product_data_cart_quantity
			);
		}
		return $productCart;
	}

	/**
	 * @param array $item
	 * @return float $price
	 */
	private static function calcDiscount($item)
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
     * @param $payqr_cart
     * @return bool
     */
    public static function actualizeCart(&$payqr_cart)
    {
        foreach($payqr_cart as $payqr_product)
        {
            //получаем товар и его цену и модифицируем data-cart
            $item = AMI::getResourceModel('eshop_item/table')->find($payqr_product->article);

            if(empty($item) || empty($item->id))
            {
                return false;
            }

            $price = self::calcDiscount($item);

            $payqr_product->amount = $price;
        }
        //

        return true;
    }

    /**
     * @param array $payqr_cart
     * @param mixed $delivery_id
     * @return int
     */
    public static function getCartAmount($payqr_cart, $delivery_id = null)
    {
        $total = 0;

        foreach($payqr_cart as $cart_product)
        {
            $total += $cart_product->amount * $cart_product->quantity;
        }

        if(!is_null($delivery_id))
        {
	        //получаем способ доставки
	        $delivery = PayQRDelivery::getDelivery($delivery_id);

	        if($delivery['type'] == 'abs')
            {
                $total = $total + $delivery['amount'];
            }
            if($delivery['type'] == 'percent')
            {
                $total = $total + ($total * $delivery['amount'] / 100);
            }
	    }

        return $total;
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