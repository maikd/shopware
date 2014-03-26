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
 * @file       Components/API/APIClient.class.php
 * @version    27.03.2014 - 17:22
 */

class APIClient
{
	protected static $_instance;
	
	protected $APIURL;
	protected $APIToken;
	protected $ShopToken;
	protected $WebsiteURL;
	
	/*
	 * constructor, set the api config
	 */
	public function __construct($APIToken = '', $ShopToken = '', $WebsiteURL = '')
	{
		$this->APIURL 		= 'https://api.easymarketing.de';
		$this->APIToken 	= $APIToken;
		$this->ShopToken 	= $ShopToken;
		
		$this->WebsiteURL 	= $WebsiteURL;
	}
	
	/*
	 * generate a instance of this class
	 *
	 * @return object
	 */
	public static function getInstance()
	{
		if (!self::$_instance instanceof self)
		{
			$config = EasymarketingConfig::getInstance();
			self::$_instance = new self($config->getAPIToken(), $config->getShopToken(), $config->getWebsiteURL());
		}
		return self::$_instance;
	}
	
	/*
	 * perform a request
	 *
	 * @params $action (string), $params (array), $method (string)
	 * @return array
	 */
	public function performRequest($action, $params = array(), $method = 'GET')
	{
		if(!$this->validateSettings())
		{
			return false;
		}
		
		if(!function_exists('curl_init')) 
		{
			return false;
		}
		
		$ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
		
		$url_param_string = 'website_url=' . $this->WebsiteURL;
		
		if(!empty($params) && count($params) > 0)
		{
			if($method == 'POST')
			{
				$params[] = array('website_url' => $this->WebsiteURL);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
			} else {
				$url_params = array();
				$url_params[] = $url_param_string;
				
				foreach($params as $param => $value)
				{
					$url_params[] = $param . '=' . $value;
				}
				
				$url_param_string = implode('&', $url_params);
			}
		}
		
        curl_setopt($ch, CURLOPT_URL, $this->APIURL.'/'.$action.'?'.$url_param_string.'&access_token='.$this->APIToken);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

        $response = curl_exec($ch);
		$response_infos = curl_getinfo($ch);
		
		curl_close($ch);
				
        $content = substr($response, $response_infos['header_size']);
		$content = $this->convertResponse($content);
		
		$success = false;
		$errors = array();
		
		switch ($response_infos['http_code']) 
		{
            case 401:
                $errors[] = 'Invalid access! Please check the api access token before perform request!';
                break;
            case 400:
                if(is_array($content['errors']) && count($content['errors']) > 0) 
				{
                    foreach($content['errors'] as $error) 
					{
                        $errors[] = $error;
					}	
					unset($content['errors']);
				}
                break;
        	case 200:
			 	$success = true;
                break;
		}
		
		return array(
						'status' => $response_infos['http_code'], 
						'success' => $success, 
						'error' => (count($errors) > 0 or !$success) ? true : false, 
						'errors' => $errors, 
						'data' => (isset($content) && !empty($content)) ? $content : false
					);
	}
	
	/*
	 * validate settings
	 *
	 * @return boolean
	 */
	protected function validateSettings()
	{
		if(!empty($this->APIToken) && !empty($this->ShopToken) && !empty($this->WebsiteURL))
		{
			return true;
		}
		
		return false;	
	}
	
	/* 
	 * convert response, if the response isn't a array
	 *
	 * @return array
	 */
	protected function convertResponse($content)
	{
		if(json_decode($content) != null)
		{
			$result = json_decode($content, true);
		} elseif(is_array($content) || is_object($content)) {
			$result = $this->makeObjectToArray($content);
		} else {
			$result = $content;
		}
		
		return $result;
	}
	
	/*
	 * convert object to array
	 *
	 * @return array
	 */
	protected function makeObjectToArray($content)
	{
		if (is_array($content) || is_object($content))
		{
			$result = array();
			foreach ($content as $key => $value)
			{
				$result[$key] = $this->makeObjectToArray($value);
			}
			return $result;
		}
		return $content;
	}
	
}
		
?>