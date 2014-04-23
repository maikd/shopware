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
 * @copyright  Copyright (c) 2013, BuI Hinsche GmbH
 
 * @modified_by Easymarketing AG, Florian Ressel <florian.ressel@easymarketing.de>
 *
 * @file       Controller/Backend/Easymarketing.php
 * @version    25.03.2014 - 17:22
 */

require_once(dirname(__FILE__) . '/../../Components/Config/EasymarketingConfig.class.php');
require_once(dirname(__FILE__) . '/../../Components/Utilis/EasymarketingHelper.class.php');
require_once(dirname(__FILE__) . '/../../Components/API/APIClient.class.php');

class Shopware_Controllers_Backend_Easymarketing extends Shopware_Controllers_Backend_ExtJs 
{
	
	protected $WebsiteURL = '';
	protected $Categories = array();
	
	/*
	 * init
	 */
    public function init() 
	{
        $this->View()->addTemplateDir(dirname(__FILE__) . "/../../Views/");
		
		$this->WebsiteURL = EasymarketingHelper::getWebsiteURL($this->Front()->Router()->assemble(array('module' => 'frontend', 'controller' => 'index')));
		
        parent::init();
    }
    
	/*
	 * get all configuration
	 *
	 * @return config
	 */
    public function getConfigsAction()
	{
		$config = EasymarketingConfig::getInstance();
		
		$extractionStatusData = $this->getExtractionStatus();
		
		if($extractionStatusData)
		{	
			$config->setEasymarketingLastCrawlDate($extractionStatusData['updated_at']);
			$config->setEasymarketingLastCrawlCategoriesCount($extractionStatusData['num_categories']);
			$config->setEasymarketingLastCrawlProductsCount($extractionStatusData['num_products']);
		}
		
		$configData = $config->getConfigData();
		
		if(!isset($configData['RootCategoryID']) or $configData['RootCategoryID'] == 0)
		{
			$configData['RootCategoryID'] = (int)$this->getShopRootCategoryID();
		}
		
		$this->View()->assign(array(
			'success' => true,
			'data' => $configData
		));
	}
	
	/*
	 * save the configuration
	 */
	public function saveConfigsAction()
	{
		$config = EasymarketingConfig::getInstance();
	
		$validAPIToken = false;
		
		$config->setShopToken(EasymarketingHelper::generateShopToken());
		$config->setWebsiteURL($this->WebsiteURL);

		// if the api token is set, check the api token
		if(!empty($this->Request()->APIToken))
		{
			$validAPIToken = $this->checkAPIToken($this->Request()->APIToken);
		}
		
		// if the api token is invalid, return a error message
		if(empty($this->Request()->APIToken) or !$validAPIToken)
		{
			return $this->View()->assign(array(
				'success' => true,
				'message' => 'Der API Token ist ungültig!',
				'sub_message' => 'Bitte geben Sie einen gültigen Token ein um die Installation abzuschließen.'
			));
		}
		 
		$config->setAPIStatus((int)$validAPIToken);
		$config->setAPIToken($this->Request()->APIToken);
		$config->setRootCategoryID($this->Request()->RootCategoryID);
		$config->setShowFacebookLikeBadge($this->Request()->ShowFacebookLikeBadge);
		$config->setRetargetingAdScaleStatus($this->Request()->RetargetingAdScaleStatus);
		
		// execute the setup of the plugin
		$this->executeSetup();
	
		$this->View()->assign(array(
			'success' => true,
			'data' => $config->getConfigData(),
			'message' => 'Einrichtung erfolgreich abgeschlossen',
			'sub_message' => 'Die Installation und Konfiguration wurde soeben abgeschlossen.'
		));
	}
	
	/*
	 * get all categories of the shop
	 *
	 * @return array
	 */
	public function getCategoriesAction()
	{
		$shop_root_category_id = $this->getShopRootCategoryID();
		
		$this->Categories[] = array('id' => (int)$shop_root_category_id, 'name' => 'Root Kategorie vom Shop verwenden');
		
		$this->getCategoryTree($shop_root_category_id, 0);
		
		$this->View()->assign(array(
			'success' => true,
			'data' => array_values($this->Categories)
		));
	}
	
	/*
	 * get the shop root category id
	 *
	 * @return integer
	 */
	protected function getShopRootCategoryID()
	{
		$shop_data = Shopware()->Db()
					->fetchOne('
						SELECT category_id FROM s_core_shops WHERE `default` = 1 LIMIT 1
					');
					
		return $shop_data['category_id'];
	}	
	
	/*
	 * get the category tree
	 *
	 * @params $parent_id (integer), $level (integer), $sub_category (boolean)
	 */
	protected function getCategoryTree($parent_id, $level, $sub_category = false)
	{		
		if($sub_category)
		{
			$name_prefix = str_pad('', $level, '-');
		} else {
			$name_prefix = '';
		}
				
		$result = Shopware()->Db()
					->fetchAll('
						SELECT id, description as name FROM s_categories WHERE parent = "'.$parent_id.'" AND active = 1 ORDER BY position
					');
					
		foreach($result as $category)
		{	
			if(!$sub_category)
			{
				$level = 0;
			}
			
			$this->Categories[] = array('id' => (int)$category['id'], 'name' => $name_prefix . $category['name']);
			
			$check = Shopware()->Db()
					->fetchAll('
						SELECT id FROM s_categories WHERE parent = "'.$category['id'].'" AND active = 1 ORDER BY id
					');
					
			if(count($check) > 0)
			{
				$this->getCategoryTree($category['id'], ++$level, true);
			}
		}
	}
	
	/*
	 * check the api token
	 *
	 * @return boolean
	 */
	protected function checkAPIToken($APIToken)
	{
		$config = EasymarketingConfig::getInstance();
		
		$APIClient = new APIClient($APIToken, $config->getShopToken(), $config->getWebsiteURL());
		$response = $APIClient->performRequest('extraction_status');
		
		if(isset($response['status']) && $response['status'] != 401)
		{
			return true;
		} else {
			return false;
		}			
	}

	/*
	 * execute the setup
	 */
	protected function executeSetup()
	{
		$this->resetExistingConfigs();
		
		$this->setAPIEndpoints();
		$this->getGoogleConversionTracker();
		$this->getLeadTracker();
		$this->performGoogleSiteVerification();
		$this->getExtractionStatus();
		$this->getFacebookBadge();
		$this->getRetargetingIds();
	}
	
	/* 
	 * reset the existing configuration, if the setup is called again
	 */
	protected function resetExistingConfigs()
	{
		$config = EasymarketingConfig::getInstance();
		
		$parameters = array(
						'ConfigureEndpointsStatus',
						'GoogleConversionTrackerStatus',
						'LeadTrackerStatus',
						'GoogleSiteVerificationStatus',
						'FacebookLikeBadgeCode',
						'RetargetingAdScaleID'
					);
					
		foreach($parameters as $parameter)
		{
			$config->set($parameter, '');
		}
	}
	
	/*
	 * set the api endpoints
	 */
	protected function setAPIEndpoints()
	{
		$config = EasymarketingConfig::getInstance();
		
		$website_url = $this->Front()->Router()->assemble(array('module' => 'frontend', 'controller' => 'index'));
    
		$params = array(
            'website_url' => $this->WebsiteURL,
            'access_token' => $config->getAPIToken(),
            'shop_token' => $config->getShopToken(),
            'categories_api_endpoint' => $website_url.'easymarketing_api/categories',
            'shop_category_root_id' => $config->getRootCategoryID(),
            'products_api_endpoint' => $website_url.'easymarketing_api/products',
            'product_by_id_api_endpoint' => $website_url.'easymarketing_api/products_by_id',
            'best_products_api_endpoint' => $website_url.'easymarketing_api/best_products',
            'new_products_api_endpoint' => $website_url.'easymarketing_api/new_products',
            'shopsystem_info_api_endpoint' => $website_url.'easymarketing_api/shopsystem_info',
            'api_setup_test_single_product_id' => Shopware()->Db()->fetchOne("SELECT id FROM s_articles WHERE active = 1 LIMIT 1")
        );
		
		$response = APIClient::getInstance()->performRequest('configure_endpoints', $params, 'POST');
		
		if($response['status'] == 200)
		{
			$config->setConfigureEndpointsStatus(1);
		}
	}
	
	/*
	 * get the google conversion tracker
	 */
	protected function getGoogleConversionTracker()
	{
		$config = EasymarketingConfig::getInstance();
		
		$response = APIClient::getInstance()->performRequest('conversion_tracker');
		
		if($response['status'] == 200)
		{
			$config->setGoogleConversionTrackerCode($response['data']['code']);
			$config->setGoogleConversionTrackerImg($response['data']['img']);
			
			$config->setGoogleConversionTrackerStatus(1);
		}
	}
	
	/*
	 * get the lead tracker
	 */
	protected function getLeadTracker()
	{
		$config = EasymarketingConfig::getInstance();
		
		$response = APIClient::getInstance()->performRequest('lead_tracker');
		
		if($response['status'] == 200)
		{
			$config->setLeadTrackerCode($response['data']['code']);
			$config->setLeadTrackerStatus(1);
		}
	}
	
	/*
	 * perform google site verification
	 */
	protected function performGoogleSiteVerification()
	{
		$config = EasymarketingConfig::getInstance();
		
		$response = APIClient::getInstance()->performRequest('site_verification_data');
		
		if($response['status'] == 200)
		{	
			$config->setGoogleSiteVerificationMetaTag($response['data']['meta_tag']);
			
			$params = array(
            	'verification_type' => 'META'
        	);
			
			$response = APIClient::getInstance()->performRequest('perform_site_verification', $params, 'POST');
			
			if($response['status'] == 200)
			{
				$config->setGoogleSiteVerificationStatus($status);
			}
		}
	}
	
	/*
	 * get the extraction status
	 */
	public function getExtractionStatus()
	{
		$response = APIClient::getInstance()->performRequest('extraction_status');
		
		if($response['status'] == 200 || $response['status'] == 400)
		{
			return $response['data'];
		}
	}
	
	/*
	 * get the facebook like badge
	 */
	public function getFacebookBadge()
	{
		$response = APIClient::getInstance()->performRequest('facebook_badge');
		
		if($response['status'] == 200)
		{
			EasymarketingConfig::getInstance()->setFacebookLikeBadgeCode($response['data']);
		}
	}
	
	/*
	 * get the retargeting ids
	 */
	protected function getRetargetingIds()
	{
		$response = APIClient::getInstance()->performRequest('retargeting_id');
		
		if($response['status'] == 200)
		{
			EasymarketingConfig::getInstance()->setRetargetingAdScaleID($response['data']['adscale_id']);
			EasymarketingConfig::getInstance()->setRetargetingAdScaleConversionID($response['data']['conversion_id']);
		}
	}
    
}
?>