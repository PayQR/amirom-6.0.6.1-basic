<?php

function CreateOrder($Payqr)
{
	$user_id = null;

	$cData = $Payqr->objectOrder->getCustomer();
	$uData = json_decode($Payqr->objectOrder->getUserData());
	$uData = isset($uData[0])? $uData[0] : $uData;

	if(!isset($uData->user_id) && empty($uData->user_id)) 
	{
		if(!isset($cData->email) && !isset($uData->email))
		{
			//фэйлим заказ клиента
			//система не передала email клиента
			return false;
		}

		//создаем пользователя в системе
		$user_email = isset($cData->email)? $cData->email : $uData->email;

		$user_id = PayQR_AmiUser::create_user($user_email);

		if(is_null($user_id))
		{
			//фэйлим заказ клиента
			//не получилось создать пользователя
			return false;
		}
	}

	//имеем клиента, от имени которого будем создавать заказ в системе
	if(is_null($user_id))
	{
		$user_id = $uData->user_id;
	}

	//получаем информацию о товарах от PayQR
	//получаем объекты товаров по данным присланным от сервере в переменной $payqr_cart
	$payqr_cart = $Payqr->objectOrder->getCart();

    //производим актуализацию корзины
    if(!PayQRCart::actualizeCart($payqr_cart))
    {
        //не получилось актуализировать корзину
        return false;
    }

    //проверяем была ли выбрана доставка пользователем
    $delivery = $Payqr->objectOrder->getDeliveryCasesSelected();
    $delivery_id = null;

    if(isset($delivery, $delivery->article) && !empty($delivery->article))
    {
    	$amount = PayQRCart::getCartAmount($payqr_cart, $delivery->article);
    	$delivery_id = $delivery->article;
    }
    else
    {
    	$amount = PayQRCart::getCartAmount($payqr_cart, null);
    }
    
    $Payqr->objectOrder->setAmount($amount);

	//Формируем информацию о покупателе
	$user_for_order_comment = "";

	if(isset($cData->firstName) && !empty($cData->firstName))
	{
		$user_for_order_comment .= "Имя покупателя: " . $cData->firstName . ". ";
	}
	
	if(isset($cData->lastName) && !empty($cData->lastName))
	{
		$user_for_order_comment .= "Фамилия покупателя: " . $cData->lastName . ". ";
	}
	
	if(isset($cData->middlename) && !empty($cData->middlename))
	{
		$user_for_order_comment .= "Отчество покупателя: " . $cData->middlename . ". ";
	}
	
	if(isset($cData->delivery) && !empty($cData->delivery))
	{
		$user_for_order_comment .= "Доставка покупателя: " . $cData->delivery . ". ";
	}
	
	if(isset($cData->promo) && !empty($cData->promo))
	{
		$user_for_order_comment .= "Промо-код покупателя: " . $cData->promo . ". ";
	}

	if(isset($cData->email) && !empty($cData->email))
	{
		$user_for_order_comment .= "Email покупателя: " . $cData->email . ". ";
	}

	if(isset($cData->phone) && !empty($cData->phone))
	{
		$user_for_order_comment .= "Телефон покупателя: " . $cData->phone . ". ";
	}

	//получаем статусы заказов
	$pqBSettings = PayQRBSettings::initConfig();
	$payqr_settings = $pqBSettings->getSettings();

	$status_created = isset($payqr_settings['payqr_status_creatted']) && !empty($payqr_settings['payqr_status_creatted'])? $payqr_settings['payqr_status_creatted'] : 'draft';

	//создаем заказ на основе актуализированным данных
	$order_id = PayQROrder::setOrderData($user_id, "", $Payqr, $delivery_id, $status_created );

	if(is_null($order_id))
	{
		//фэйлим заказ клиента
		//не получилось создать заказ
		return false;
	}

	//формируем данные для таблицы order_items
	PayQROrder::setOrderItemsData($payqr_cart, $order_id);

	$Payqr->objectOrder->setOrderId($order_id);

	//проверяем в каком контексте был приобретен товар
	if(isset($uData->page) && in_array($uData->page, array('category', 'product')))
	{
		//корзину не будем очищать, пропускаем данную ветку
	}
	else 
	{
		//производим очистку корзины
                $oQuery = DB_Query::getUpdateQuery('cms_sessions', array('data'=>''), DB_Query::getSnippet('WHERE id_member = %s')->q($user_id));
		AMI::getSingleton('db')->query($oQuery);

		//если ползователь гость, то производим удаление по session_id
		if(isset($uData->session_id) && !empty($uData->session_id))
		{
			$oQuery = DB_Query::getUpdateQuery('cms_sessions', array('data'=>''), DB_Query::getSnippet('WHERE id = %s')->q($uData->session_id));
			
			AMI::getSingleton('db')->query($oQuery);
		}
	}

	$userdata = array(
				"user_id" => $user_id,
				"session_id" => $uData->session_id,
				"order_id" => $order_id
	);

	//Так как пользователь новый, создаем пометку о создании профиля
	if(!isset($uData->user_id))
	{
		$userdata['new_account'] = true;
	}
	
	$Payqr->objectOrder->setUserData(json_encode($userdata));
        
        //устаналиваем сообщение для пользователя
        if(isset($payqr_settings['payqr_ocreating_message_text'], $payqr_settings['payqr_ocreating_message_imageurl'], $payqr_settings['payqr_ocreating_message_url']))
        {
            $Payqr->objectOrder->data->message->article = 1;
            $Payqr->objectOrder->data->message->text = $payqr_settings['payqr_ocreating_message_text'];
            $Payqr->objectOrder->data->message->url = $payqr_settings['payqr_ocreating_message_url'];
            $Payqr->objectOrder->data->message->imageUrl = $payqr_settings['payqr_ocreating_message_imageurl'];
        }
}