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
 * @file       Components/Utilis/EasymarketingHelper.class.php
 * @version    27.03.2014 - 17:22
 */

/**
 * Class EasymarketingHelper provides some tool functions.
 */
class EasymarketingHelper
{
	
	/**
	 * get the website url
	 *
	 * @param $shop_url (string)
	 * @return string
	 */
	public static function getWebsiteURL($shop_url)
	{
        $website_url = $shop_url;
        $website_url = str_replace('http://', '', $website_url);
        $website_url = str_replace('https://', '', $website_url);
		
		return $website_url;
	}
	
	/**
	 * generate secure shop token
	 *
	 * @return string
	 */ 
	public static function generateShopToken()
	{
		return sha1(mt_rand(10,1000) . time());
	}

    public static function last(&$array, $key){
        end($array);
        return $key === key($array);
    }

}
?>