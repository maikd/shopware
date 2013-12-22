<?php


class Shopware_Plugins_Frontend_SwpEasymarketing_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    public function getLabel()
    {
        return 'Schnittstelle Easymarketing';
    }

    public function getVersion()
    {
        return '4.0.00';
    }

    public function install()
    {
    	
        $event = $this->createEvent(
 		'Enlight_Controller_Dispatcher_ControllerPath_Frontend_Easymarketingapi',
 		'onGetControllerPathFrontend'
	 	);
	$this->subscribeEvent($event);
        
        $sql =  "INSERT INTO s_core_rewrite_urls
                (org_path, path, main, subshopID)
                VALUES 
                ('sViewport=easymarketingapi&sAction=best_products','easymarketing_api/best_products', 1, 1),
                ('sViewport=easymarketingapi&sAction=new_products','easymarketing_api/new_products', 1, 1),
                ('sViewport=easymarketingapi&sAction=categories','easymarketing_api/categories', 1, 1),
                ('sViewport=easymarketingapi&sAction=products','easymarketing_api/products', 1, 1),
                ('sViewport=easymarketingapi&sAction=products_by_id','easymarketing_api/products_by_id', 1, 1)
                ;";
        Shopware()->Db()->exec($sql);
        
                
                
                
        return true;
    }

    public function uninstall()
    {
        $sql =  "DELETE FROM s_core_rewrite_urls WHERE org_path LIKE 'sViewport=easymarketingapi&sAction=%' AND path LIKE 'easymarketing_api/%';";
        Shopware()->Db()->exec($sql);
        

        return true;
    }

  
   
    public function onGetControllerPathFrontend(Enlight_Event_EventArgs $args){
    	  	
    	return $this->Path() . 'Controllers/easymarketing.php';
    	
    }  
    
    
    
      /**
	 * Plugin update method
	 */
	public function update($oldVersion) {
		return true;
	}
	
		
	/**
	 * activates the plugin
	 * @return boolean
	 */
	public function enable() {
		//$this->checkLicense();
		return true;
	}
	
	
	
	/**
	 * get the main info for the plugin
	 */
	public function getInfo() {
		return array(
			'version' => $this->getVersion(),
			'autor' => 'BuI Hinsche GmbH',
			'copyright' => 'Copyright (c) 2013, BuI Hinsche GmbH',
			'label' => $this->name,
			'support' => 'http://bui-hinsche.de',
			'link' => 'http://bui-hinsche.de'
		);
	}
	
	
	/**
	 * deactivates the plugin
	 * @return boolean
	 */
	public function disable() {
		return true;
	}
	
	
	/**
	* deactivetes the plugin - dunno if this is already done by disable...
	*/
	protected static function deactivate() {
		$id = Shopware()->Db()->fetchOne('SELECT id FROM s_core_plugins WHERE name="SwpResourceCalcVwd"');
		$sql = 'update s_core_plugins set active=0 where id=' . $id;
		Shopware()->Db()->exec($sql);
	}
    
}

?>