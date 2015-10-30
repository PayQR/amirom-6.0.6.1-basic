<?php

class PayQR_AmiUser {

	/**
	 * @param string $email             //email    постоянно запрашивается у пользователя
	 * @param string $firstname         //Имя      - запрашивается из админки
	 * @param string $surname           //Фамилия  - запрашивается из админки
	 * @param string $patronymic        //Отчество - запрашивается из админки
	 * @param string $phone             //Телефон  - запрашивается из админки
	 * @return int|null
	 */
	
	public static function create_user($email, $firstname = "", $surname = "", $patronymic="", $phone="")
	{
		$user_id = self::get_user_id($email);

		if(is_null($user_id))
		{
			return null;
		}

		if(is_int(intval($user_id, 10)))
		{
			return $user_id;
		}

		$oUserModelItem = AMI::getResourceModel('members/table')->getItem();
		
		$oUserModelItem->username  = "user" . rand(100000000,10000000000);
		
		$oUserModelItem->nickname  = empty($firstname)? $oUserModelItem->username : $firstname;
		
		$oUserModelItem->email     = $email;
		
		$oUserModelItem->phone     = $phone;
		
		$oUserModelItem->firstname = empty($firstname)? $oUserModelItem->username . " " . $patronymic : $firstname . " " . $patronymic;

		$oUserModelItem->lastname  = $surname;
		
		$oUserModelItem->save();
		
		$user_id = $oUserModelItem->getId();

		if(!empty($user_id))
		{
			return $user_id;
		}
		return null;
	}

	/**
	 * @param string $email
	 * @return null|bool
	 */
	private static function check_isset_user($email = "")
	{
		if(!self::check_email($email))
		{
			return null;
		}
		
		$oQuery = new DB_Query('cms_members');
		$oQuery->addField('id');
		$oQuery->getSnippet('email = %s')->q($email);
		$result = AMI::getSingleton('db')->fetchRow($oQuery);

		if(!isset($result['id']) || empty($result['id']))
		{
			return false;
		}
		return true;
	}

	/**
	 * @param string $email
	 * @return null|int|bool
	 */
	public static function get_user_id($email = "")
	{
		$user_check_result = self::check_isset_user($email);

		if(is_null($user_check_result))
		{
			return false;
		}
		
		if(!$user_check_result)
		{
			return null;
		}
		
		$oQuery = new DB_Query('cms_members');
		$oQuery->addField('id');
		$oQuery->getSnippet('email = %s')->q($email);
		$result = AMI::getSingleton('db')->fetchRow($oQuery);

		if(!isset($result['id']) || empty($result['id']))
		{
			return null;
		}

		return $result['id'];
	}

	/**
	 * @param string $email
	 * @return bool
	 */
	private static function check_email($email)
	{
		if(empty($email) || preg_match("/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]+$/", $email)!=1)
		{
			return false;
		}
		return true;
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