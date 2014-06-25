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
 * @file       Controller/Frontend/Easymarketing.php
 * @version    25.03.2014 - 17:22
 */
 
require_once(dirname(__FILE__) . '/../../Components/Config/EasymarketingConfig.class.php');

class Shopware_Controllers_Frontend_Easymarketingapi extends Enlight_Controller_Action
{

	/*
	 * index action
	 */
    public function indexAction()
	{
		// do nothing
    }
    
	/*
	 * check the given shop token
	 */
    private function checkShopToken() 
	{	
		$shop_token = Shopware()->Front()->Request()->getParam('shop_token');
		
        if(isset($shop_token) && !empty($shop_token)) 
		{
            $config = EasymarketingConfig::getInstance();

            if($shop_token != $config->getShopToken()) 
			{
                header('HTTP/1.0 400 Bad Request');
                echo 'Wrong Shop Token!';
                die;
            }
        } else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Shop Token Missing!';
            die;
        }
        
        return true;
    }
    
	/*
	 * get the shopsystem infos
	 */
    public function shopsystemInfoAction()
	{
        $this->checkShopToken();
        
        $shopsystem = 'shopware';
        $shopsystem_human = Shopware()->App().' '.Shopware()->Config()->version;
        $shopsystem_version = Shopware()->Config()->version;
        $api_version = Shopware_Plugins_Frontend_SwpEasymarketing_Bootstrap::getVersion();
 
        $jsondata = array(
            'shopsystem' => $shopsystem,
            'shopsystem_human' => $shopsystem_human,
            'shopsystem_version' => $shopsystem_version,
            'api_version' => $api_version            
        );
        
       $this->printOutput($jsondata);
    }    

    /**
     * get the categories
     */
    public function categoriesAction()
	{
        $this->checkShopToken();
		
		$id = Shopware()->Front()->Request()->getParam('id');
        
        if ($id > 0)
		{	
            $parent_id = $id; 
        }

        if (isset($parent_id))
		{
            $cat = Shopware()->Modules()->Categories()->sGetCategoryContent($parent_id);
			
			$sql = "SELECT path FROM s_core_rewrite_urls WHERE org_path = 'sViewport=cat&sCategory=".$parent_id."'";
        	$rewrite_path = Shopware()->Db()->fetchOne($sql);
        
        	$rewrite_url = $this->Front()->Router()->assemble(array('module' => 'frontend', 'controller' => 'index'));
        	$rewrite_url .= $rewrite_path;

            $sub_cats = Shopware()->Modules()->Categories()->sGetWholeCategoryTree($parent_id);
            
            if (count($sub_cats) > 0)
			{
                $i = 0;
                $subcatsarray = array();
				
                foreach ($sub_cats as $k => $v)
				{
                    $subcatsarray[] = $v['id'];
                }
            }
            
            $json_array = array(
                'id' => $cat['id'],
                'name' => $cat['name'],
				'url' => $rewrite_url,
                'google_product_category' => $cat['attribute']['attribute6'],
                'children' => $subcatsarray
            );
           
		   $this->printOutput($json_array);
        }
    }
    
    /**
     * get the products
     */
    public function productsAction()
	{
        $this->checkShopToken();
		
		$config = EasymarketingConfig::getInstance();
        
        $offset = Shopware()->Front()->Request()->getParam('offset');
        $limit = Shopware()->Front()->Request()->getParam('limit'); 
        $pids = Shopware()->Db()->fetchAll("SELECT sacro.articleID AS id FROM s_articles_categories_ro sacro LEFT JOIN s_articles sa ON sa.id = sacro.articleID WHERE sa.active = 1 AND (sacro.categoryID = '".$config->getRootCategoryID()."' OR sacro.parentCategoryID = '".$config->getRootCategoryID()."') GROUP BY sacro.articleID ORDER BY sacro.articleID LIMIT ".$offset.", ".$limit); 
		      
        $jsonoffset = $offset;  
		      
        foreach ($pids as $key => $val)
		{
            $productsdata[] = $this->getProductsData($val['id']);
        }  
		      
        $jsondata = array(
            'offset' => $offset,
            'products' => $productsdata
        );
      
	  	$this->printOutput($jsondata);
    }
    
	/*
	 * get a product by id
	 */
    public function productsByIdAction()
	{
        $this->checkShopToken();
		
		$config = EasymarketingConfig::getInstance(); 
        $article_id = Shopware()->Front()->Request()->getParam('id');
		
		$checkArticle = Shopware()->Db()->fetchOne("SELECT sacro.articleID AS id FROM s_articles_categories_ro sacro LEFT JOIN s_articles sa ON sa.id = sacro.articleID WHERE sa.active = 1 AND (sacro.categoryID = '".$config->getRootCategoryID()."' OR sacro.parentCategoryID = '".$config->getRootCategoryID()."') AND sacro.articleID = '".$article_id."' GROUP BY sacro.articleID ORDER BY sacro.articleID LIMIT 1");
		
		if($checkArticle)
		{
			$productsdata=$this->getProductsData($article_id);
			$jsondata = $productsdata;
			
			$this->printOutput($productsdata);
		} else {
			header('HTTP/1.0 404 Article not found');
            echo 'The article with the id '.$article_id.' was not found!';
            die;
		}
    }
    
	/*
	 * get new products
	 */
    public function newProductsAction()
	{
        $this->checkShopToken();
		
		$config = EasymarketingConfig::getInstance();
  
        $limit = Shopware()->Front()->Request()->getParam('limit');
        $newer_than = Shopware()->Front()->Request()->getParam('newer_than');  
        $newer_than_date = date('Y-m-d', $newer_than);
		
		$pids = Shopware()->Db()->fetchAll("SELECT sacro.articleID AS id FROM s_articles_categories_ro sacro LEFT JOIN s_articles sa ON sa.id = sacro.articleID WHERE sa.active = 1 AND sa.datum > '".$newer_than_date."' AND (sacro.categoryID = '".$config->getRootCategoryID()."' OR sacro.parentCategoryID = '".$config->getRootCategoryID()."') GROUP BY sacro.articleID ORDER BY sa.datum DESC LIMIT ".$limit);
		
        foreach ($pids as $key => $val)
		{
            $productsdata[] = $this->getProductsData($val['id']);
        }  
		      
        $jsondata = array(
            'limit' => $limit,
            'newer_than' => $newer_than,
            'products' => $productsdata
        );
        
		$this->printOutput($jsondata);
	}
    
	/*
	 * get the best products
	 *
	 */
    public function bestProductsAction()
	{
        $this->checkShopToken();
		
		$config = EasymarketingConfig::getInstance();
 
        $limit = Shopware()->Front()->Request()->getParam('limit');
        $most_sold_since = Shopware()->Front()->Request()->getParam('most_sold_since');
        $most_sold_since_date = date('Y-m-d H:i:s', $most_sold_since);						
											
		$pids = Shopware()->Db()->fetchAll("SELECT sacro.articleID AS id, (SELECT SUM(od.quantity) FROM s_order_details od WHERE od.articleID = sacro.articleID AND od.modus = 0) as sales
												FROM s_articles_categories_ro sacro                                                
                                                INNER JOIN s_order_details od ON sacro.articleID = od.articleID
                								INNER JOIN s_order o ON od.orderID = o.id
												INNER JOIN s_articles sa ON od.articleID = sa.id
                                                WHERE o.ordertime > '".$most_sold_since_date."' AND sa.active = 1 AND (sacro.categoryID = '".$config->getRootCategoryID()."' OR sacro.parentCategoryID = '".$config->getRootCategoryID()."')
                                                GROUP BY sacro.articleID
                								ORDER BY SUM(od.quantity) DESC
												LIMIT ".$limit);
											
        $i = 0;
		
		$productsdata = array();
		
		if(count($pids) > 0)
		{
			foreach ($pids as $key => $val)
			{
				$productsdata[$i] = array('id' => $val['id'], 'sales' => (int)$val['sales']);
				$i++;
			}   
		} else {
			$test_product_id = Shopware()->Db()->fetchOne("SELECT sacro.articleID AS id FROM s_articles_categories_ro sacro LEFT JOIN s_articles sa ON sa.id = sacro.articleID WHERE sa.active = 1 AND (sacro.categoryID = '".$config->getRootCategoryID()."' OR sacro.parentCategoryID = '".$config->getRootCategoryID()."') GROUP BY sacro.articleID ORDER BY sacro.articleID LIMIT 1");
			$productsdata[0] = array('id' => $test_product_id['id'], 'sales' => 0);
		}
		
        $jsondata = array(
            'limit' => $limit,
            'most_sold_since' => $most_sold_since,
            'products' => $productsdata
        );
        
		$this->printOutput($jsondata);
    }
    
	/*
	 * get shopping informations
	 *
	 * @return array
	 */
    private function getShippingArray($product)
	{
		$shippingdataarray = array();
		
        //Versandkostenuebermittlung ueberwacht nur Preis, Gewicht und Laender 
        $shippingdata = Shopware()->Db()->fetchAll("SELECT MIN(ps.value) as shippingprice, pd.* , c.countryiso as countrycode 
                										FROM s_premium_dispatch pd 
                										INNER JOIN s_premium_dispatch_countries pdc ON pd.id = pdc.dispatchID
                										INNER JOIN s_core_countries c ON pdc.countryID = c.id
                										INNER JOIN s_premium_shippingcosts ps ON pd.id = ps.dispatchID
                									GROUP BY c.countryiso");
  
        foreach ($shippingdata as $shippingkey => $shipping)
		{
            $failure = 0;
			
            if ($shipping['bind_weight_from'] > 0 && $shipping['bind_weight_from'] > $product['weight'])
			{
                $failure = 1;
            }  
			
            if ($shipping['bind_weight_to'] > 0 && $shipping['bind_weight_to'] < $product['weight'])
			{
                $failure = 1;
            }
			
            if ($shipping['bind_price_from'] > 0 && $shipping['bind_price_from'] > $product['price'])
			{
                $failure = 1;
            }
			
            if ($shipping['bind_price_from'] < 0 && $shipping['bind_price_from'] < $product['price'])
			{
                $failure = 1;
            }
			
            if ($failure == 0)
			{
            	$shippingarray = new stdClass();	
                $shippingarray->country = $shipping['countrycode'];
                $shippingarray->service = $shipping['name'];
                $shippingarray->price = floatval(str_replace(',','.',$shipping['shippingprice']));
                               
                $shippingdataarray[] = $shippingarray;
            }
        }  
		      
        return $shippingdataarray;
    }
    
	/*
	 * get the data of an product
	 *
	 * @params $product_id (integer)
	 * @return array
	 */
    private function getProductsData($products_id)
	{
    	$currency = Shopware()->Db()->fetchOne("SELECT c.currency FROM s_core_currencies c INNER JOIN s_core_shops s ON c.id = s.currency_id"); 

        $product = Shopware()->Modules()->Articles()->sGetArticleById($products_id);
            
        if (is_array($product[sConfigurator]))
		{
            $variants = Shopware()->Db()->fetchAll("SELECT 
														aa.id as articledetail, acg.name as groupname, aco.name as valuename, acor.option_id as optionid
                    								FROM s_article_configurator_option_relations acor
                    								INNER JOIN s_articles_details aa ON acor.article_id = aa.id
                    								INNER JOIN s_article_configurator_options aco ON aco.id = acor.option_id
                    								INNER JOIN s_article_configurator_groups acg ON acg.id = aco.group_id
                    								WHERE aa.articleID =".$products_id);
            $variant_prods = array();

            foreach ($variants as $variantkey => $variantval)
			{
                $variant_prods[$variantval['articledetail']][$variantval['groupname']]=$variantval['valuename'];

                $images = Shopware()->Db()->fetchAll("SELECT 
															m.* 
                        								FROM s_articles_img ai
                        								INNER JOIN s_media m ON m.id = ai.media_id
                        								WHERE ai.id IN (
                            								SELECT ai2.parent_id
                            								FROM s_articles_img ai2 
                            								WHERE ai2.article_detail_id = ".$variantval['articledetail']."
                        							)");
				
                if (count($images) > 0)
				{
                    foreach($images as $imagekey=>$imageval)
					{
                        $imagepath = 'http://'.Shopware()->Shop()->getHost();
                        $imagepath .= Shopware()->Shop()->getBaseUrl().'/';
                        $variant_prods[$variantval['articledetail']]['image_url'] = $imagepath.$imageval['path'];
                    }
                } else {
                    $variant_prods[$variantval['articledetail']]['image_url'] = $product['image']['src']['original'];
                }
            }

            ksort($variant_prods);
            $variant_prods_array=array();

            foreach($variant_prods as $variantprodskey => $variantprodsval)
			{
                foreach($variantprodsval as $ikey => $ival)
				{
                    if ($ikey != 'image_url')
					{
                        $variant_prods_array_inner[$ikey] = $ival;
                    }
                }
                $variant_prods_array_inner['image_url'] = $variantprodsval['image_url'];
                $variant_prods_array[] = $variant_prods_array_inner;
            }
        }

        $rewrite_path = Shopware()->Db()->fetchOne("SELECT path FROM s_core_rewrite_urls WHERE org_path = 'sViewport=detail&sArticle=".$products_id."'");
        
        $rewrite_url = $this->Front()->Router()->assemble(array('module' => 'frontend', 'controller' => 'index'));
        $rewrite_url .= $rewrite_path;

        if ($product['sUpcoming'] == 0)
		{
            if ($product['laststock'] == 1)
			{
                if ($product['instock'] <= 0)
				{
                    $availibility = 'out of stock';
                } elseif($product['instock'] > 0) {
                    $availibility = 'in stock';
                }

            } else {
                if ($product['instock'] <= 0)
				{
                    $availibility = 'available for order';
                } elseif($product['instock'] > 0) {
                    $availibility = 'in stock';
                }
            }
        } else {
            $availibility = 'preorder';
        }

        $prod_cats = Shopware()->Db()->fetchAll("SELECT categoryID FROM s_articles_categories WHERE articleID = ".$products_id);
        $prod2cats = array();
		
        foreach ($prod_cats as $catkey => $catval)
		{
            $prod2cats[] = $catval['categoryID'];
        }
		
		$price = floatval(str_replace(',','.',$product['price']));
		$pseudoprice = (isset($product['pseudoprice'])  && $product['pseudoprice'] > 0) ? floatval(str_replace(',','.',$product['pseudoprice'])) : $price;
		$discout_absolute = ($pseudoprice > $price) ? ($pseudoprice - $price) : 0;

        $prod_shipping_array = $this->getShippingArray($product);
        $jsonproductarray = array(
            'id' => $product['articleID'],
            'name' => $product['articleName'],
            'image_url' => $product['image']['src']['original'],
            'condition' => 'new',
            'categories' => $prod2cats,
            'availability' => $availibility,
            'price' => $price,
			'rrp' => floatval($pseudoprice),
			'discount_absolute' => $discout_absolute, 
            'url' => $rewrite_url,
            'description' => $product['description_long'],
            'currency' => $currency,
            'shipping' => $prod_shipping_array,
            'margin' => 0.56,
            'gtin' => $product['ean'],
			'brand' => $product['supplierName'],
			'mpn' => $product['ordernumber']
        );
		
        if (count($variant_prods_array) > 0)
		{
            $jsonproductarray['variants'] = $variant_prods_array;
        }
		
        $products_data = $jsonproductarray;
        return $products_data;
    }
	
	/*
	 * return the json object 
	 *
	 * @params $jsondata (json object)
	 * @return json_object
	 */
	protected function printOutput($jsondata)
	{
		$jsonoutput = json_encode($jsondata);
        header('Content-type: application/json');
        echo $jsonoutput; die;
	}
	
}

?>