<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Stores
 * @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Stores_Block_Front  extends  Mage_Core_Block_Template
{
    protected  $_collection;
    
    public $searchPlace = "";
    public $searchProduct = "";
    public $whyNotError = array();
    
    //Get an array with geoip-infodata
    
	/**
    * Get google key fom Admin. For supporting old version of Google maps API 
    *
    * @return string $key
    */
    public function getGoogleMapsKey()
    {
        return  "";//Mage::getStoreConfig('stores/settings/googlemapsapi');
    }
    
	/**
    * Get Distance view param
    *
    * @return int $param
    */
	public function getKmMiles()
    {
        return  Mage::getStoreConfig('stores/settings/enabledmiles');
    }
	
	/**
    * Get Title
    *
    * @return string $param
    */
	public function getPageStoreTitle()
    {
        return  Mage::getStoreConfig('stores/settings/pagetitle');
    }
    
    /**
    * Get Search current search Place
    *
    * @return string $place
    */
	public function getSearchPlace()
    {
        return $this->searchPlace;
    }
    
	/**
    * Get search current product
    *
    * @return string $product
    */
    public function getSearchProduct()
    {
        return $this->searchProduct;
    }
	
	/**
    * Get is all stores will be shown in front
    *
    * @return int
    */
    public function getIsAllStores()
    {
        return  Mage::getStoreConfig('stores/settings/googlemapsisall');
    }
    
    /**
    * Get Error if they are
    *
    * @return array
    */
	public function getProductErrors()
    {
        return  $this->whyNotError;
    }
    
    /**
    * Get products for Ajax helper
    * @param string $searchterms
	* @param string $productcollection
    * @return array $products
    */
    public function returnProducts($searchterms, $productcollection)
    {

        $lowercassearch = strtolower($searchterms);
        $strlen = strlen($lowercassearch);
        
        $products = array();
        
        if ($strlen > 1) {
            foreach ($productcollection as $product) {
                $lowercaseproduct = strtolower($product->getName());
                $keywordmatch = strpos($lowercaseproduct, $lowercassearch);
                if (!($keywordmatch === false)) {
                    $products[] = $product->getId()." ".$product->getName();
                }
            }
        }
        
        return $products;
    }

    /**
    * Generate Html Error String
    * @return string $text
    */
	public function getHtmlErrors()
    {
        $text = "";
        foreach($this->whyNotError as $error)
        {
            echo $text.=$error."<br>";
        }
        $this->whyNotError = null;
    }
    
    /**
    * Get Stores by User Query
    * @return array $page_stores
    */
	public function getStoresCollection()
    {
        
        $serch_product = Mage::getSingleton('core/app')->getRequest()->getParam('search_product');
        $isFirstTime = Mage::getSingleton('core/app')->getRequest()->getParam('search_place',-1);
        
        if (Mage::getSingleton('core/app')->getRequest()->getParam('isuserall',-1)!=-1) {
            $userWantAll = false;    
        } else {
            $userWantAll = true;    
        }
        
       
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addAttributeToSelect('*');
        
        if ($isFirstTime == -1){
            $serch_place = $this->getUserPosition();
        } else {
            $serch_place = Mage::getSingleton('core/app')->getRequest()->getParam('search_place');
        }
         //Is Correct Product
        if (($serch_product!="") && (strtolower($serch_product)!="all")) {
            
			$is_real = $this->returnProducts($serch_product,$productCollection);
            if (!count($is_real)){
                $this->searchPlace = $serch_place;
                $this->searchProduct = $serch_product;
                $this->whyNotError[] = "This Product wasn't found in Stores";
                return false;
            }   
        }
        //-----------------

        if (($serch_place!="") && (strtolower($serch_place)!="all")) {
			if (($this->getIsAllStores()==1) || ($userWantAll)){
               $page_stores=$this->gelAllStoresByUserDistance($serch_place,$serch_product); 
            } else {
                $page_stores=$this->getUserStoresSearch($serch_place,$serch_product);    
            } 
        }
        else{
            $serch_place = "all";
            
            if ($serch_product==""){
                $serch_product='all';    
            }
            
            $current_serch_place = $this->getUserPosition();
            $page_stores=$this->gelAllStoresByUserDistance($current_serch_place,$serch_product);

        }
        
        $this->searchPlace = $serch_place;
        $this->searchProduct = $serch_product;
        
        return $page_stores;
    }
    
    /**
    * Get User Position By IP Addr
    * @return string $currentSerchPlace
    */
    public function getUserPosition ()
    {
        
        $user_position=$this->geoCheckIP($_SERVER['REMOTE_ADDR']);
        
        if ($user_position) {
			if ($user_position['town']!=""){
					$currentSerchPlace= $user_position['country']." ".$user_position['town'];
				}
			else {
				if ($user_position['region']!="") {
					$currentSerchPlace = $user_position['region'];
				} else {
					$currentSerchPlace = $user_position['country'];
				}    
			}
			return $currentSerchPlace;
        } else {
			return "";
		}
    }
    
    /**
    * Check Posiotion By Ip Address
    * @param string $ip
    * @return array $ipInfo
    */
    public function geoCheckIP($ip)
    {

        $geoip = Mage::getBaseDir('lib')."/storegeoip/geoip.inc";
    
        $geoipcity = Mage::getBaseDir('lib')."/storegeoip/geoipcity.inc";
        $geoipregionvars = Mage::getBaseDir('lib')."/storegeoip/geoipregionvars.php";
        $dat = Mage::getBaseDir('lib')."/storegeoip/GeoLiteCity.dat";
        
        include_once($geoip);
        include_once($geoipcity);
        include_once($geoipregionvars);
        
        $giCity = geoip_open($dat,GEOIP_STANDARD);
		
		$record = geoip_record_by_addr($giCity, $ip);    

		if(!is_null($record)) {
			$ipInfo['town'] = trim($record->city);
			$ipInfo['country'] = trim($record->country_name);
			$ipInfo['region'] = trim($GEOIP_REGION_NAME[$record->country_code][$record->region]);
		} else {
			$ipInfo = false;
		}
        geoip_close($giCity);
        
		return $ipInfo;
       }
    
    /**
    * Get stores only bu user query place or his location and by products in query
    * @param string $search_place
	* @param string $serch_product
    * @return array $storesCollection
    */
    public function getUserStoresSearch($serch_place,$serch_product)
    { 
        $storesCollection = Mage::getModel('stores/state')->getStoresUserSearch($serch_place,$serch_product); 
        $this->_collection = $storesCollection;
        return $storesCollection;
    }
	
    /**
    * Get All stores by products in query
    * @param string $search_place
	* @param string $serch_product
    * @return array $storesCollection
    */
    public function gelAllStoresByUserDistance($serch_place,$serch_product)
    {
        $storesCollection = Mage::getModel('stores/state')->gelAllStoresByUserDistance($serch_place,$serch_product); 
           $this->_collection = $storesCollection;
        return $storesCollection;
    }
    
    /**
    * Get Administrative Area of User by Google Api
    *
    * @return string $adminstrative_area
    */
	public function getAdministativeArea()
    {

        $url="http://maps.google.com/maps/geo?q=".urlencode($this->searchPlace)."&key=".$this->getGoogleMapsKey()."&sensor=false";
    
        $pars_address=file_get_contents($url);
        $p_array=json_decode($pars_address,true);
        $adminstrative_area=(array)$p_array;
        $array_iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($adminstrative_area));

        foreach($array_iterator as $key=>$value){
            if ($key==='address') {$adminstrative_area=$value;}
        }
        return $adminstrative_area;
    }
 
}