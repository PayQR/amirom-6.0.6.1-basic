<?php
/**
 * Код в этом файле будет выполнен, когда интернет-сайт получит уведомление от PayQR о необходимости предоставить покупателю пункты самовывоза конкретного заказа.
 * Это означает, что интернет-сайт на уровне кнопки PayQR активировал этап выбора пункта самовывоза покупателем, и сейчас покупатель дошел до этого этапа.
 *
 * $Payqr->objectOrder содержит объект "Счет на оплату" (подробнее об объекте "Счет на оплату" на https://payqr.ru/api/ecommerce#invoice_object)
 *
 * Ниже можно вызвать функции своей учетной системы, чтобы особым образом отреагировать на уведомление от PayQR о событии invoice.pickpoints.updating.
 *
 * Важно: на уведомление от PayQR о событии invoice.pickpoints.updating нельзя реагировать как на уведомление о создании заказа, так как иногда оно будет поступать не от покупателей, а от PayQR для тестирования доступности функционала у конкретного интернет-сайта, т.е. оно никак не связано с реальным формированием заказов. Также важно, что в ответ на invoice.pickpoints.updating интернет-сайт может передать в PayQR только содержимое параметра pickPoints объекта "Счет на оплату". Передаваемый в PayQR от интернет-сайта список пунктов самовывоза может быть многоуровневым.
 *
 * Пример массива способов доставки:
 * $pick_points_cases = array(
 *          array(
 *              'article' => '1001',
 *               'number' => '1.1',
 *               'name' => 'Наш пункт самовывоза 1',
 *               'description' => 'с 10:00 до 22:00',
 *               'amountFrom' => '90',
 *               'amountTo' => '140',
 *              ),
 *          .....
 *  );
 * $Payqr->objectOrder->setPickPointsCases($pick_points_cases);
 */

function getPickPoints($Payqr)
{
	//получаем статусы заказов
	$pqBSettings = PayQRBSettings::initConfig();
	$payqr_settings = $pqBSettings->getSettings();

	$pickpoints_cases = array();

	if(isset($payqr_settings) && !empty($payqr_settings['payqr_require_pickpoints'])
		&& $payqr_settings['payqr_require_pickpoints'] != "deny")
	{
		//в Амиро CMS не предусмотрена возможность работы с пунктами самовывоза
	}

	return $pickpoints_cases;
}