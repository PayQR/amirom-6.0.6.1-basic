<?php

class PayQRButton {
	
	//место расположение кнопки, данные параметры учиываются в форме настройки кнопки
	private $page_prefix_list = array("cart", "product", "category");

	//заполняем настройками, получаемыми из БД
	private $db_payqr_button_config = array();

	//текущая обрабатыаемая страница
	private $page;

	//каталог имеющихся значения css классов
	private $button_config_css = array(
		"payqr_button_color",
		"payqr_button_form",
		"payqr_button_shadow",
		"payqr_button_gradient",
		"payqr_button_font_trans",
		"payqr_button_font_width",
		"payqr_button_text_case"
	);

	//
	private $button_config_style = array(
		"payqr_button_height",
		"payqr_button_width"
	);
	
	private $button_config_attr = array(
		"payqr_require_firstname",
		"payqr_require_lastname",
		"payqr_require_middlename",
		"payqr_require_phone",
		"payqr_require_email",
		"payqr_require_delivery",
		"payqr_require_deliverycases",
		"payqr_require_pickpoints",
		"payqr_require_promo"
	);

	public function __construct($page, $payqr_settings)
	{
		$this->page = $page;
		$this->getDBPayQRButtonConfig($payqr_settings);
	}

	/**
	 * @param int|null $member_id
	 * 
	 * Производим инициализацию кнопки 
	 */
	public function init($member_id = null)
	{
		$css = $attr = $style = "";

		if(!isset($this->db_payqr_button_config[$this->page]) || empty($this->db_payqr_button_config[$this->page]))
		{
			return "class='payqr-button'";
		}

		$_db_payqr_button_config = array_merge($this->db_payqr_button_config[$this->page], $this->db_payqr_button_config['common']);

		foreach($_db_payqr_button_config as $property_name => $property_value)
		{
			$property_type  = $this->checkPropertyType($property_name);

			if(!in_array($property_type, array('attr', 'css', 'style')))
			{
				continue;
			}

			if(in_array($property_value, array('default', 'auto')) || empty($property_value))
			{
				continue;
			}

			switch ($property_type)
			{
				case 'css':
					$css .= 'payqr-button_' . $property_value;
					break;
				case 'attr':
					// пользователь не гость, не запрашиваем email
					if(strpos($property_name, 'email') !== false && !empty($member_id))
					{
						break;
					}

					$attr .= ' data-' . str_replace('payqr_require_', '', $property_name) . "-required='" . $property_value . "' ";
					break;
				case 'style':
					
					preg_match_all("/[0-9]*/i", $property_value, $matches);
					
					if(isset($matches[0]) && !empty($matches[0]))
					{
						$style .= ' '. str_replace('payqr_'.$this->page.'_button_', '', $property_name).':'. implode($matches[0], '') .'px;';
					}
					break;
				default:
					break;
			}
		}


		if(!empty($css)) $css = " class='payqr-button " . $css . "' ";
		else $css = " class='payqr-button' ";

		if(!empty($attr)) $attr = $attr;

		if(!empty($style)) $style = " style='" . $style . "' ";

		return $css . $attr . $style;
	}

	public function initUserData(array $user_data)
	{
		foreach($user_data as $key => $data)
		{
			if(empty($data))
			{
				unset($user_data[$key]);
			}
		}

		$oSession = AMI::getSingleton('env/session');

		if(!$oSession->isStarted())
		{
   			$res = $oSession->start();
		}

		$user_data['session_id'] = $oSession->getId();

		return empty($user_data)? "" : "data-userdata='".json_encode(array($user_data))."'";
	}

	/**
	 * @param array $payqr_settings
	 * @return mixed
	 * 
	 * Заполняем переменную зарактеристик
	 */
	public function getDBPayQRButtonConfig($payqr_settings)
	{
		if(empty($payqr_settings))
		{
			$this->db_payqr_button_config  = array();

			return;
		}

		foreach($this->page_prefix_list as $page)
		{
			foreach($payqr_settings as $key => $setting)
			{
				if(strpos($key, $page) !== false)
				{
					$this->db_payqr_button_config[$page][$key] = $setting;
				}
				else 
				{
					$this->db_payqr_button_config['common'][$key] = $setting;
				}
			}
		}
		return;
	}

	/**
	 * @param string  $message optional null
	 * @param integer $line optional 0
	 * @param bool    $debug optional false
	 * @param bool    $delete_old_log_file optional false
	 */
	private function __log($message = null, $line = 0, $debug = false, $delete_old_log_file = false)
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

	/**
	 * @return bool
	 */
	public function isShow()
	{
		if(empty($this->db_payqr_button_config) || !isset($this->db_payqr_button_config[$this->page]))
		{
			return false;
		}

		if($this->db_payqr_button_config[$this->page]['payqr_button_show_on_' . $this->page] == "no")
		{
			return false;
		}

		return true;
	}

	/**
	 * @param string $property
	 * @return string ("attr" | "css" | "style")
	 */
	private function checkPropertyType($property)
	{
		if($this->isPropertyAttr($property))
		{
			return "attr";
		}
		if($this->isPropertyCss($property))
		{
			return "css";
		}
		if($this->isPropertyStyle($property))
		{
			return "style";
		}
	}

	/**
	 * @param string $property
	 * @return bool
	 */
	private function isPropertyCss($property)
	{
		$bytton_property = str_replace( '_' . $this->page, '', $property);

		return in_array($bytton_property, $this->button_config_css)? true : false;
	}

	/**
	 * @param string $property
	 * @return bool
	 */
	private function isPropertyAttr($property)
	{
		return in_array($property, $this->button_config_attr)? true : false;
	}

	/**
	 * @param string $property
	 * @return bool
	 */
	private function isPropertyStyle($property)
	{
		$bytton_property = str_replace( '_' . $this->page, '', $property);

		return in_array($bytton_property, $this->button_config_style)? true : false;
	}
}