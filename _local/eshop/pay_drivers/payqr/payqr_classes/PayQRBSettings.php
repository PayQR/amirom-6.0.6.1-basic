<?php

class PayQRBSettings{

	protected static $_instance;

	private $settings = array();

	private function __construct(){}

	private function __clone(){}

	public static function initConfig() {
        // проверяем актуальность экземпляра
        if (null === self::$_instance) {
            // создаем новый экземпляр
            self::$_instance = new self();
        }
        // возвращаем созданный или существующий экземпляр
        return self::$_instance;
    }

    private function setSettings()
    {
    	//$result = AMI::getSingleton('db')->fetchCol("SELECT settings FROM `cms_pay_drivers` WHERE `name` like '%payqr%'");
        $oQuery = new DB_Query('cms_pay_drivers');
        $oQuery->addField('settings');
        $oQuery->setWhereDef(DB_Query::getSnippet('AND name = %s')->q('payqr'));
        $result = AMI::getSingleton('db')->fetchRow($oQuery);


		foreach($result as $settings)
		{
			if(unserialize($settings) != false)
			{
				$this->$settings = unserialize($settings);
			}
 		}
 		return $this->$settings;
    }

    public function getSettings()
    {
    	return $this->setSettings();
    }
}