<?php

require_once(dirname(__FILE__) . '/Components/Config/EasymarketingConfig.class.php');
require_once(dirname(__FILE__) . '/Components/API/APIClient.class.php');
require_once(dirname(__FILE__) . '/Components/Utilis/EasymarketingHelper.class.php');

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
 * @file       Bootstrap.php
 * @version    25.03.2014 - 17:22
 */
class Shopware_Plugins_Frontend_EasymIntegration_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

	/**
	 * get the label of the plugin
	 *
	 * @return string
	 */	
	public function getLabel()
    {
        return 'easymarketing Integration';
    }

	/**
	 * get the version of the plugin
	 *
	 * @return string
	 */	 
    public function getVersion()
    {
        return '4.1.8';
    }

	/**
	 * installs the plugin.
	 *
	 * @return array
	 */
    public function install()
    {
		if (!$this->assertVersionGreaterThen('4.0'))
    	{
    		return array(
				'success' => false,
				'message' => 'Das Plugin benötigt mindestens die Shopware Version 4.0.'
			);
    	}
		
    	$this->createDataBase();
		$this->createEvents();
        $this->createMenu();        
           
        return array('success' => true, 'invalidateCache' => array('backend', 'frontend'));
    }
	
	/**
	 * uninstall method
	 *
	 * @return array
	 */
	public function uninstall()
    {
		Shopware()->Db()->exec(
				'TRUNCATE easymarketing_config'
				);
				
		Shopware()->Db()->exec('			
			REPLACE INTO `easymarketing_config` SET `key` = "APIStatus", `value` = "0"
		');
			
        Shopware()->Db()->exec(
				'DELETE FROM s_core_rewrite_urls WHERE org_path LIKE "sViewport=easymarketingapi&sAction=%" AND path LIKE "easymarketing_api/%"
			');

        return array('success' => true, 'invalidateCache' => array('backend', 'frontend'));
    }
	
	/**
	 * update method does nothing.
	 *
     * @param oldVersion
	 * @return boolean
	 */
	public function update($oldVersion) 
	{
		return true;
	}
		
	/**
	 * activates the plugin
	 *
	 * @return boolean
	 */
	public function enable() 
	{
		return true;
	}
	
	/**
	 * deactivates the plugin
	 *
	 * @return boolean
	 */
	public function disable() 
	{
		return true;
	}
    
    /***
	 * creates the database tables
	 */
	protected function createDataBase()
	{
		Shopware()->Db()->exec("
			CREATE TABLE IF NOT EXISTS `easymarketing_config` (
			  `key` varchar(125) NOT NULL DEFAULT '',
			  `value` text NOT NULL,
			  PRIMARY KEY (`key`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  		");
		
		Shopware()->Db()->exec("			
			REPLACE INTO `easymarketing_config` SET `key` = 'APIStatus', `value` = '0'
		");
		
		$sql =  "INSERT INTO s_core_rewrite_urls
                (org_path, path, main, subshopID)
                VALUES 
                ('sViewport=easymarketingapi&sAction=best_products','easymarketing_api/best_products', 1, 1),
                ('sViewport=easymarketingapi&sAction=new_products','easymarketing_api/new_products', 1, 1),
                ('sViewport=easymarketingapi&sAction=categories','easymarketing_api/categories', 1, 1),
                ('sViewport=easymarketingapi&sAction=products','easymarketing_api/products', 1, 1),
                ('sViewport=easymarketingapi&sAction=products_by_id','easymarketing_api/products_by_id', 1, 1),
                ('sViewport=easymarketingapi&sAction=shopsystem_info','easymarketing_api/shopsystem_info', 1, 1)
                ;";
        Shopware()->Db()->exec($sql);
	}
	
	/**
	 * create the events
	 */
	protected function createEvents()
	{
		$this->subscribeEvent(
        'Enlight_Controller_Action_PostDispatch',
        'onPostDispatchFrontendIndexRemarketing'
        );
		$event = $this->createEvent(
 		'Enlight_Controller_Dispatcher_ControllerPath_Backend_Easymarketing',
 		'onGetControllerPathBackend'
	 	);
		$this->subscribeEvent($event);
		
		$event = $this->createEvent(
        'Enlight_Controller_Action_PostDispatch_Backend_Index',
        'onPostDispatchBackendIndex'
        );
		$this->subscribeEvent($event);
		
		$event = $this->createEvent(
 		'Enlight_Controller_Dispatcher_ControllerPath_Frontend_Easymarketingapi',
 		'onGetControllerPathFrontend'
	 	);
		$this->subscribeEvent($event);
		
		$event = $this->createEvent(
 		'Enlight_Controller_Action_PostDispatch_Frontend_Index',
 		'onPostDispatchIndex'
	 	);
		$this->subscribeEvent($event);
		
        $event = $this->createEvent(
 		'Shopware_Controllers_Frontend_Checkout::finishAction::after',
 		'onAfterFinishAction'
	 	);
		$this->subscribeEvent($event);
		
		$event = $this->createEvent(
        	'Enlight_Controller_Action_PostDispatch',
        	'onPostDispatchIndexRetargeting'
        );
		$this->subscribeEvent($event);
	}
    
    /**
     * create navigation menu items for backend
     */
    public function createMenu() 
	{
        $parent = $this->Menu()->findOneBy('label', 'Marketing');

        $this->createMenuItem(array(
            'label' => $this->getLabel(),
            'active' => 1,
            'parent' => $parent,
        	'controller' => 'Easymarketing',
        	'action' => 'Index',
			'class' => 'easymarketing-icon'
        ));
    }

    /**
     * add a template snippet with the google remarketing code in the frontend
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchFrontendIndexRemarketing(Enlight_Event_EventArgs $args){
		$caller = $args->getSubject();
		$request = $caller->Request();
		$view = $caller->View();
        $logger = Shopware()->Pluginlogger();
	
		$config = EasymarketingConfig::getInstance()->getConfigData();
		$view->EasymarketingConfig = $config;

        /* get the current basket content */
        $tpl_vars = $view->template()->smarty->tpl_vars;
        $basket = null;
        if(isset($tpl_vars['sBasket'])){
            $basket = $tpl_vars['sBasket']->value->content;
        }
        $basket = $tpl_vars['sBasket'];
        $basketContent = $basket->value['content'];

        /* creates json array from $basketItems or single value
            and sets template vars with:
            - ecomm_prodid = ordernumbers
            - ecomm_quantity = quantity
        */
        $ecomm_prodid = "";
        $ecomm_quantity = "";
        if(count($basketContent) > 1){
            $ecomm_prodid = "[";
            $ecomm_quantity = "[";
            foreach($basketContent as $basketKey => $basketItem){
                $ecomm_prodid .= "'".$basketItem['ordernumber']."'";
                $ecomm_quantity .= "'".$basketItem['quantity']."'";
                if(!EasymarketingHelper::last($basketContent, $basketKey)){
                    $ecomm_prodid .= ",";
                    $ecomm_quantity .= ",";
                }
            }
            $ecomm_quantity  .= "]";
            $ecomm_prodid .= "]";
        }else{
            $ecomm_prodid = "'".$basketContent[0]['ordernumber']."'";
            $ecomm_quantity  = "'".$basketContent[0]['quantity']."'";
        }
        $view->ecomm_prodid = $ecomm_prodid;
        $view->ecomm_quantity = $ecomm_quantity;

        /*check if a current user is logged in*/
        try{
            if(Shopware()->Modules()->Admin()->sCheckUser()){
                $view->hasaccount = "y";
            }
        }catch(Exception $e){
            $logger->info("Not able to load the user data. Failure: ".$e->getMessage());
        }

        $view->addTemplateDir($this->Path() . 'Views/');
		$args->getSubject()->View()->extendsTemplate('frontend/plugins/easymarketing/remarketing.tpl');
	}

	/**
	 * get the path of the backend controller
	 *
	 * @params Enlight_Event_EventArgs $args
	 * @return string
	 */
    public function onGetControllerPathBackend(Enlight_Event_EventArgs $args) 
	{
        return dirname(__FILE__) . '/Controllers/Backend/Easymarketing.php';
    } 
	
	/**
	 * load the css for backend plugin
	 *
	 * @params Enlight_Event_EventArgs $args
	 */
	public function onPostDispatchBackendIndex(Enlight_Event_EventArgs $args)
	{ 
      $view = $args->getSubject()->View();
      $args->getSubject()->View()->addTemplateDir($this->Path() . 'Views/');
      $view->extendsTemplate('backend/index/easymarketing_header.tpl');
   	}

	/**
	 * get the path of the frontend controller
	 *
	 * @params Enlight_Event_EventArgs $args
	 * @return string
	 */
    public function onGetControllerPathFrontend(Enlight_Event_EventArgs $args)
	{ 	
    	return $this->Path() . 'Controllers/Frontend/Easymarketing.php';
    }  
	
	/**
	 * add a template snippet in the frontend
	 */
	public function onPostDispatchIndex(Enlight_Event_EventArgs $args) 
	{
        $caller = $args->getSubject();
		$request = $caller->Request();
		$view = $caller->View();
		
		$config = EasymarketingConfig::getInstance()->getConfigData();
		
        $view->EasymarketingConfig = $config;
        $view->addTemplateDir($this->Path() . 'Views/'); 
        $view->extendsTemplate('frontend/index/easymarketing_header.tpl');
    }

    /**
	 * add a template snippet in the checkout, if the order is finished
	 */
    public function onAfterFinishAction(Enlight_Hook_HookArgs $args) 
	{
        $caller = $args->getSubject();
		$request = $caller->Request();
		$view = $caller->View();
        
		$config = EasymarketingConfig::getInstance()->getConfigData();
		
        $view->EasymarketingConfig = $config;
        $view->addTemplateDir($this->Path() . 'Views/');
        $view->extendsTemplate('frontend/checkout/easymarketing_finish.tpl');              
    }
	
	/**
	 * add a template snippet in the frontend for retargeting
	 */
	public function onPostDispatchIndexRetargeting(Enlight_Event_EventArgs $args)
	{
		$caller = $args->getSubject();
		$request = $caller->Request();
		$view = $caller->View();
	
		$config = EasymarketingConfig::getInstance()->getConfigData();
	
		$view->EasymarketingConfig = $config;
        $view->addTemplateDir($this->Path() . 'Views/');
		$args->getSubject()->View()->extendsTemplate('frontend/plugins/easymarketing/retargeting.tpl');
		$args->getSubject()->View()->extendsTemplate('frontend/plugins/easymarketing/leadtracker.tpl');
	}
	
	/**
	 * get the main info for the plugin
	 */
	public function getInfo() 
	{
		return array(
					'version' => $this->getVersion(),
					'autor' => 'BuI Hinsche GmbH',
					'copyright' => 'Copyright (c) 2013, BuI Hinsche GmbH',
					'label' => $this->getLabel(),
					'support' => 'http://bui-hinsche.de',
					'link' => 'http://bui-hinsche.de'
				);
	}
	
	/**
	 * get the capabilities of the plugin
	 */
	public function getCapabilities()
	{
		return array(
			'install'   => true,
			'uninstall' => true,
			'update'    => true,
			'enable'    => true,
			'disable'   => true,
		);
	}
	
}

?>