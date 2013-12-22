<?php

class Shopware_Controllers_Frontend_Easymarketingapi extends Enlight_Controller_Action{


    public function indexAction(){

    }

    private function __debug($data){
        echo '<pre>';
        print_r($data); die;
    }

    /**
     * generate the jsonstring for a Category
     */
    public function categoriesAction(){
        if ((int) Shopware()->Front()->Request()->getParam('parent_id') > 0){	
            $parent_id = Shopware()->Front()->Request()->getParam('parent_id'); 
        }
        //$cat = Shopware()->Modules()->Categories()->sGetCategories($parent_id);

        if (isset($parent_id)){
            $cat = Shopware()->Modules()->Categories()->sGetCategoryContent($parent_id);

            $sub_cats = Shopware()->Modules()->Categories()->sGetWholeCategoryTree($parent_id);
            
            if (count($sub_cats)>0){
                $i=0;
                $subcatsarray = array();
                foreach ($sub_cats as $k=>$v){
                    $subcatsarray[] = $v['id'];
                }
            }
            //$this->__debug($cat);
            $json_array = array(
                'id' => $cat['id'],
                'name' => $cat['name'],
                'google_product_category' => $cat['attribute']['attribute6'],
                'children' => $subcatsarray
            );
            $jsonoutput = json_encode($json_array);
            echo $jsonoutput; die;
        }
    }
    
    /**
     * generate the jsonstring for Products
     */
    public function productsAction(){
        
        $offset = Shopware()->Front()->Request()->getParam('offset');
        $limit = Shopware()->Front()->Request()->getParam('limit'); 
        $pids = Shopware()->Db()->fetchAll("SELECT id from s_articles LIMIT ".$offset.", ".$limit);
        
        $sql = "SELECT c.currency 
                FROM s_core_currencies c
                INNER JOIN s_core_shops s ON c.id = s.currency_id
                ";
        $currency = Shopware()->Db()->fetchOne($sql);        
        $jsonoffset = $offset;        
        foreach ($pids as $key => $val){
            $productsdata[]=$this->getProductsData($val['id']);
        }        
        $jsondata = array(
            'offset' => $offset,
            'products' => $productsdata
        );
        $jsonoutput = json_encode($jsondata);
        echo $jsonoutput; die;
        
    }
    
    
    public function productsByIdAction(){
        $sql = "SELECT c.currency 
                FROM s_core_currencies c
                INNER JOIN s_core_shops s ON c.id = s.currency_id
                ";
        $currency = Shopware()->Db()->fetchOne($sql);        
        $article_id = Shopware()->Front()->Request()->getParam('id');
        $productsdata=$this->getProductsData($article_id);
        $jsondata = $productsdata;
        $jsonoutput = json_encode($jsondata);
        echo $jsonoutput; die;
    }
    
    public function newProductsAction(){
        
        $limit = Shopware()->Front()->Request()->getParam('limit');
        $newer_than = Shopware()->Front()->Request()->getParam('newer_than');
        
        $sql = "SELECT c.currency 
                FROM s_core_currencies c
                INNER JOIN s_core_shops s ON c.id = s.currency_id
                ";
        $currency = Shopware()->Db()->fetchOne($sql);  
        $newer_than_date = date('Y-m-d', $newer_than);
        //$this->__debug($newer_than_date);
        $pids = Shopware()->Db()->fetchAll("SELECT id from s_articles WHERE datum > '".$newer_than_date."' ORDER BY datum DESC LIMIT ".$limit);
        foreach ($pids as $key => $val){
            $productsdata[]=$this->getProductsData($val['id']);
        }        
        $jsondata = array(
            'time' => $newer_than,
            'newer_than' => $limit,
            'products' => $productsdata
        );
        $jsonoutput = json_encode($jsondata);
        echo $jsonoutput; die;
        
    }
    
    public function bestProductsAction(){
        
        $limit = Shopware()->Front()->Request()->getParam('limit');
        $most_sold_since = Shopware()->Front()->Request()->getParam('most_sold_since');
        
        $sql = "SELECT c.currency 
                FROM s_core_currencies c
                INNER JOIN s_core_shops s ON c.id = s.currency_id
                ";
        $currency = Shopware()->Db()->fetchOne($sql);  
        $most_sold_since_date = date('Y-m-d H:i:s', $most_sold_since);
        $sql =  "
                SELECT od.articleID as id
                FROM s_order_details od
                INNER JOIN s_order o ON od.orderID = o.id 
                WHERE o.ordertime > '".$most_sold_since_date."'
                GROUP BY od.articleID
                ORDER BY SUM(od.quantity) DESC
                LIMIT ".$limit;
        
        //$sql = "SELECT id FROM s_articles ORDER BY pseudosales DESC LIMIT ".$limit;
        
        
        $pids = Shopware()->Db()->fetchAll($sql);
        
        foreach ($pids as $key => $val){
            $productsdata[]=$this->getProductsData($val['id']);
        }        
        $jsondata = array(
            'time' => $newer_than,
            'newer_than' => $limit,
            'products' => $productsdata
        );
        $jsonoutput = json_encode($jsondata);
        echo $jsonoutput; die;
    
    }
    
    private function getShippingArray($product){
        
        
        //Versandkostenübermittlung überwacht nur Preis, Gewicht und Länder
        $sql =  "
                SELECT MIN(ps.value) as shippingprice, pd.* , c.countryiso as countrycode 
                FROM s_premium_dispatch pd 
                INNER JOIN s_premium_dispatch_countries pdc ON pd.id = pdc.dispatchID
                INNER JOIN s_core_countries c ON pdc.countryID = c.id
                INNER JOIN s_premium_shippingcosts ps ON pd.id = ps.dispatchID
                GROUP BY c.countryiso
                ";
        
        $shippingdata = Shopware()->Db()->fetchAll($sql);
        
        
        foreach ($shippingdata as $shippingkey=>$shipping){
            $failure = 0;
            if ($shipping['bind_weight_from']>0 && $shipping['bind_weight_from']>$product['weight']){
                $failure=1;
            }  
            if ($shipping['bind_weight_to']>0 && $shipping['bind_weight_to']<$product['weight']){
                $failure=1;
            }
            if ($shipping['bind_price_from']>0 && $shipping['bind_price_from']>$product['price']){
                $failure=1;
            }
            if ($shipping['bind_price_from']<0 && $shipping['bind_price_from']<$product['price']){
                $failure=1;
            }
            if ($failure == 0){
            	$shippingarray = new stdClass();	
                $shippingarray->country = $shipping['countrycode'];
                $shippingarray->service = $shipping['name'];
                $shippingarray->price = str_replace(',','.',$shipping['shippingprice']);
                               
                $shippingdataarray[]=$shippingarray;
            }
        }        
        return $shippingdataarray;
    }
    
    
    
    
    private function getProductsData($products_id){

    	$sql = "SELECT c.currency 
                FROM s_core_currencies c
                INNER JOIN s_core_shops s ON c.id = s.currency_id
                ";
        $currency = Shopware()->Db()->fetchOne($sql);  


        
        $product = Shopware()->Modules()->Articles()->sGetArticleById($products_id);
            
        if (is_array($product[sConfigurator])){
            $sql =  "    
                    SELECT aa.id as articledetail, acg.name as groupname, aco.name as valuename, acor.option_id as optionid
                    FROM s_article_configurator_option_relations acor
                    INNER JOIN s_articles_details aa ON acor.article_id = aa.id
                    INNER JOIN s_article_configurator_options aco ON aco.id = acor.option_id
                    INNER JOIN s_article_configurator_groups acg ON acg.id = aco.group_id
                    WHERE aa.articleID =".$products_id.";
                    ";
            $variants = Shopware()->Db()->fetchAll($sql);
            $variant_prods = array();

            foreach ($variants as $variantkey => $variantval){

                $variant_prods[$variantval['articledetail']][$variantval['groupname']]=$variantval['valuename'];

                $sql =  "
                        SELECT m.* 
                        FROM s_articles_img ai
                        INNER JOIN s_media m ON m.id = ai.media_id
                        WHERE ai.id IN (
                            SELECT ai2.parent_id
                            FROM s_articles_img ai2 
                            WHERE ai2.article_detail_id = ".$variantval['articledetail']."
                        )
                        ";
                $images = Shopware()->Db()->fetchAll($sql);
                if (count($images)>0){
                    foreach ($images as $imagekey=>$imageval){
                        $imagepath='http://'.Shopware()->Shop()->getHost();
                        $imagepath.=Shopware()->Shop()->getBaseUrl().'/';
                        $variant_prods[$variantval['articledetail']]['image_url']=$imagepath.$imageval['path'];
                    }
                }else{
                    $variant_prods[$variantval['articledetail']]['image_url']=$product['image']['src']['original'];
                }
            }

            ksort($variant_prods);
            $variant_prods_array=array();

            foreach ($variant_prods as $variantprodskey => $variantprodsval){
                foreach ($variantprodsval as $ikey=>$ival){
                    if ($ikey != 'image_url'){
                        $variant_prods_array_inner[$ikey]=$ival;
                    }
                }
                $variant_prods_array_inner['image_url']=$variantprodsval['image_url'];
                $variant_prods_array[]=$variant_prods_array_inner;
            }
            //$this->__debug($variant_prods_array);

        }
        $sql = "SELECT path FROM s_core_rewrite_urls WHERE org_path = 'sViewport=detail&sArticle=".$products_id."'";
        $rewrite_path = Shopware()->Db()->fetchOne($sql);
        
        $rewrite_url = 'http://'.Shopware()->Shop()->getHost();
        $rewrite_url .=Shopware()->Shop()->getBaseUrl().'/';
        $rewrite_url .= $rewrite_path;

        if ($product['sUpcoming']==0){
            if ($product['laststock'] == 1){
                if ($product['instock']<=0){
                    $availibility= 'out of stock';
                }elseif($product['instock']>0){
                    $availibility= 'in stock';
                }

            }else{
                if ($product['instock']<=0){
                    $availibility= 'available for order';
                }elseif($product['instock']>0){
                    $availibility= 'in stock';
                }
            }
        }else{
            $availibility= 'preorder';
        }

        $sql = "SELECT categoryID FROM s_articles_categories WHERE articleID = ".$products_id;
        $prod_cats = Shopware()->Db()->fetchAll($sql);
        $prod2cats = array();
        foreach ($prod_cats as $catkey => $catval){
            $prod2cats[]=$catval['categoryID'];
        }

        $prod_shipping_array = $this->getShippingArray($product);
        $jsonproductarray = array(
            'id' => $product['articleID'],
            'name' => $product['articleName'],
            'image_url' => $product['image']['src']['original'],
            'condition' => 'new',
            'categories' => $prod2cats,
            'availability' => $availibility,
            'price' => str_replace(',','.',$product['price']),
            'url' => $rewrite_url,
            'description' => $product['description_long'],
            'currency' => $currency,
            'shipping' => $prod_shipping_array,
            'margin' => 0.56
        );
        if (count($variant_prods_array)>0){
            $jsonproductarray['variants'] = $variant_prods_array;
        }
        $products_data = $jsonproductarray;
        return $products_data;
    }
}


?>