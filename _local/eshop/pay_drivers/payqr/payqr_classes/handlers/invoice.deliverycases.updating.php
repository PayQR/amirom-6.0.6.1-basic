<?php
/**
 * Код в этом файле будет выполнен, когда интернет-сайт получит уведомление от PayQR о необходимости предоставить покупателю способы доставки конкретного заказа.
 * Это означает, что интернет-сайт на уровне кнопки PayQR активировал этап выбора способа доставки покупателем, и сейчас покупатель дошел до этого этапа.
 *
 * $Payqr->objectOrder содержит объект "Счет на оплату" (подробнее об объекте "Счет на оплату" на https://payqr.ru/api/ecommerce#invoice_object)
 *
 * Ниже можно вызвать функции своей учетной системы, чтобы особым образом отреагировать на уведомление от PayQR о событии invoice.deliverycases.updating.
 *
 * Важно: на уведомление от PayQR о событии invoice.deliverycases.updating нельзя реагировать как на уведомление о создании заказа, так как иногда оно будет поступать не от покупателей, а от PayQR для тестирования доступности функционала у конкретного интернет-сайта, т.е. оно никак не связано с реальным формированием заказов. Также важно, что в ответ на invoice.deliverycases.updating интернет-сайт может передать в PayQR только содержимое параметра deliveryCases объекта "Счет на оплату". Передаваемый в PayQR от интернет-сайта список способов доставки может быть многоуровневым.
 *
 * Пример массива способов доставки:
 * $delivery_cases = array(
 *          array(
 *              'article' => '2001',
 *               'number' => '1.1',
 *               'name' => 'DHL',
 *               'description' => '1-2 дня',
 *               'amountFrom' => '0',
 *               'amountTo' => '70',
 *              ),
 *          .....
 *  );
 * $Payqr->objectOrder->setDeliveryCases($delivery_cases);
 */

function getDeliveryCases($Payqr)
{
    //получаем статусы заказов
    $pqBSettings = PayQRBSettings::initConfig();
    $payqr_settings = $pqBSettings->getSettings();

    $delivery_cases = array();

    if(isset($payqr_settings) && !empty($payqr_settings['payqr_require_deliverycases'])
        && $payqr_settings['payqr_require_deliverycases'] != "deny")
    {       
        $parents_delivery = PayQRDelivery::getDeliveries();
        
        __log(print_r($parents_delivery, true), __LINE__, true);

        $i = 1;

        foreach($parents_delivery as $parent_key => $child_deliveries)
        {
            $amount = 0;
            $iStep = 1;
            $iFirstdeliveryAmount = 0;
            
            foreach($child_deliveries as $child_delivery)
            {
                __log("Сравниваем : " . $child_delivery['name'] . ": " . (int)$child_delivery['max_total'] . " & " . $Payqr->objectOrder->getAmount(), __LINE__, true);
                
                if( (int)$child_delivery['max_total'] > $Payqr->objectOrder->getAmount() && in_array($child_delivery['custom_conditions'], array("total")))
                {
                    if($child_delivery['type'] == 'abs')
                    {
                        $amount = $child_delivery['amount'];
                    }
                    if($child_delivery['type'] == 'percent')
                    {
                        $amount = ($Payqr->objectOrder->getAmount() * $child_delivery['amount'] / 100);
                    }

                    $delivery_cases[] = array(
                            'article' => $child_delivery['id'],
                            'number' => $i++,
                            'name' => $child_delivery['name'],
                            'description' => $child_delivery['delivery_time'] . ' ' . $child_delivery['comments'],
                            'amountFrom' => $amount,
                            'amountTo' => $amount,
                    );
                    break;
                }
                if(in_array($child_delivery['custom_conditions'], array("none")))
                {
                    if($child_delivery['type'] == 'abs')
                    {
                        $amount = $child_delivery['amount'];
                    }
                    if($child_delivery['type'] == 'percent')
                    {
                        $amount = ($Payqr->objectOrder->getAmount() * $child_delivery['amount'] / 100);
                    }

                    $delivery_cases[] = array(
                            'article' => $child_delivery['id'],
                            'number' => $i++,
                            'name' => $child_delivery['name'],
                            'description' => $child_delivery['delivery_time'] . ' ' . $child_delivery['comments'],
                            'amountFrom' => $amount,
                            'amountTo' => $amount,
                    );
                    break;
                }
            }
        }
    }
    
    __log(print_r($delivery_cases, true), __LINE__, true);
    
    $Payqr->objectOrder->setDeliveryCases($delivery_cases);
}