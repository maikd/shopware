<?php
/**
 * Easymarketing Plugin
 * Copyright (c) 2013, BuI Hinsche GmbH
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License, supplemented by an additional
 * permission, and of our proprietary license can be found
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, titles and interests in the
 * above trademarks remain entirely with the trademark owners.
 *
 * @copyright  Copyright (c) 2014, Easymarketing AG (http://www.easymarketing.de)
 * @author     Florian Ressel <florian.ressel@easymarketing.de>
 *
 * @file       Components/Config/EasymarketingConfig.class.php
 * @version    27.03.2014 - 17:22
 */

/**
 * Class EasymarketingConfig contains the plugin configuration.
 */
class EasymarketingConfig
{
	
	protected static $_instance;
	
	protected $configData = array();
	
	/**
	 * constructor, get all configs at the database table
	 */
	public function __construct()
	{
		$configs = Shopware()->Db()->query('
			SELECT
					`key`, `value`
				FROM easymarketing_config
		')->fetchAll();
		
		if(count($configs) > 0)
		{
			foreach($configs as $config)
			{
				$this->configData[$config['key']] = $config['value'];
			}
		}
	}
	
	/**
	 * generate a instance of this class
	 *
	 * @return object
	 */
	public static function getInstance()
	{
		if (!self::$_instance instanceof self)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * get the config complete
	 *
	 * @return array
	 */
	public function getConfigData()
	{
		return $this->configData;
	}

    /**
	 * overload function and extract the local function
	 */
	public function __call($name, $args)
	{
		if (strpos($name, 'get') === 0)
		{
			$key = substr($name, 3);
			return $this->get($key);
		} else if (strpos($name, 'set') === 0) {
			$key = substr($name, 3);

			if (!isset($args[0]))
			{
				return '';
			}

			$value = (string)$args[0];

			$this->set($key, $value);
		} else if (strpos($name, 'delete') === 0) {
			$key = substr($name, 6);
			$this->delete($key);
		}
	}

    /**
	 * get config data of a config key
	 *
	 * @param $key (string)
	 * @return string
	 */
	public function get($key)
	{
		if (isset($this->configData[$key]))
		{
			return $this->configData[$key];
		} else {
			return false;
		}
	}

	/**
	 * set config data of a config key
	 *
	 * @params $key (string), $value (string)
	 */
	public function set($key, $value)
	{
		Shopware()->Db()->query('
				REPLACE INTO easymarketing_config
					SET
						`key` = ?,
						`value` = ?
			', array(
					$key,
					$value
		));

		$this->configData[$key] = $value;
	}
	
	/**
	 * delete a config key with their value
	 */
	public function delete($key)
	{
		unset($this->configData[$key]);
		
		Shopware()->Db()->query('
				DELETE FROM easymarketing_config
					WHERE
						`key` = ?
			', array(
					$key
		));
	}
	
}

?>