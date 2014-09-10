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

class Belvg_Stores_Model_State extends Mage_Core_Model_Abstract{
    
    CONST EARTH_RADIUS = 6372795;
	CONST MIN_COUNT_OF_PRODUCT_NAME = 2;
    public $_table;
    
    protected function getCoreResource()
    {
        return Mage::getSingleton('core/resource');
    }
    
    
    protected function _construct()
    {
        $this->_table = $this->getCoreResource()->getTableName('belvg_stores');
        $this->_init('stores/state');        
    }

    /**
    * Get Products In Store By Store ID 
    * @param object $store
	* @return Product Arr
    */
	public function getStoreProducts($store)
    {
        $result = Mage::getResourceModel('stores/state_collection');
        $products = $result->addFieldToFilter('id', $store)->getFirstItem()->getDesc();
        return unserialize($products);
    }
    
   /**
    * Get All Data Of Store By Store ID 
	* @param int $id
    * @return array $storeData
    */
	public function getStoreData($id)
    {
        $result = Mage::getResourceModel('stores/state_collection');
        $result->addFieldToFilter('id', $id);
        $storeData = $result->getFirstItem()->getData();
		
        return $storeData;
    }

    /**
    *Check uploaded files
    */
	protected function _checkUpload(){
		try{
			if ($_FILES['file_new_preview']['error'] === 'UPLOAD_ERR_INI_SIZE') {
				$max_upload = (float)(ini_get('upload_max_filesize'));
				Mage::throwException('File bigger than you can upload, max file size: '.$max_upload.' Mb');		
			}
		} catch(Exception $e) {
			Mage::log("Image upload fail. Error:".$e->getMessage());
			Mage::getSingleton('core/session')->addError('Image has not been uploaded: '.$e->getMessage());
        }
	}
   
   
   /**
    * Save Store to Database 
	* param $string $id
	* array $data
	* string $supportedProducts
    */
	public function saveStore($id,$data,$supportedProducts)
    {
	//print_r($data);exit;
        //ini_set('upload_max_filesize', '1M');
		$this->_checkUpload();
		
		$_table        = $this->_table;

        $oDb        = Mage::getSingleton('core/resource')->getConnection('core_write');
        $aDBInfo    = $data;    
        
        if (($data['lat']=="") || ($data['lng']=="") || (isset($data['relatlng']))) {
            $latlng = $this->getLatLng($data);
            $aDBInfo['lat'] = $latlng['lat'];
            $aDBInfo['lng'] = $latlng['lng'];
        }
        
        parse_str($supportedProducts,$productToArray); 
        $prValues = array_keys($productToArray);
        
        //serialize products array
        $aDBInfo['desc'] = serialize($prValues);
        
        if (isset($aDBInfo['relatlng'])) {
            unset($aDBInfo['relatlng']);
        }
        
        if (isset($aDBInfo['is_all_products'])) {
            $aDBInfo['is_all_products'] = 1;
        } else {
            $aDBInfo['is_all_products'] = 0;
        }
        
        // file upload
        
        $path = "media/press";
    
        if(isset($_POST['file_new_preview_del'])){
            $aDBInfo['file_preview']    = '';
        }else{ 
            if(isset($_FILES['file_new_preview']['name'])) {
              
			  try {

				$uploader = new Varien_File_Uploader('file_new_preview');
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));

                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
              
                $bigFileName = 'big_'.$_FILES['file_new_preview']['name'];
                $fullPath = $path. DS .$bigFileName;
                $littleImageFullPath = $path. DS .$_FILES['file_new_preview']['name'];
                
                $uploader->save($path, $bigFileName);
                
                $imageObj = new Varien_Image($fullPath);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(TRUE);
                $imageObj->keepFrame(FALSE);
                $imageObj->resize(90, 90);
                $imageObj->save($littleImageFullPath);
                $aDBInfo['file_preview'] = $bigFileName;
                
              }catch(Exception $e) {
                //Mage::log("Image upload fail. Error:".$e->getMessage());
				//Mage::getSingleton('core/session')->addError('Image has not been uploaded: '.$e->getMessage());
              }
            }
        }
        
        if ($id!=''){
			$result = $oDb->update($_table,$aDBInfo,array('id = '.$id));
		} else {
			$result = $oDb->insert($_table,$aDBInfo);
		}
    }

    /**
    * Delete Store from Database 
    * @param int $id
    */
	public function deleteStore($id)
    {        
        $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $oDb->delete($this->_table,array('id = '.$id));
    }
    
	/**
    * Get stores only bu user query place or his location and by products in query
    * @param string $serch_place
	* @param string $serch_product
    * @return array $outStores
    */
    public function getStoresUserSearch($serch_place,$serch_product)
    {
        $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
        $good_place = substr($serch_place, 0, 64);

        // Get user query coordinates
		$latlngsearch = $this->getLatLng($good_place,1);
        
        $oDb = Mage::getResourceModel('stores/state_collection');
        $oDb->getSelect()->where('MATCH(`address`,`zip_code`,`country`,`city`,`state`) AGAINST(?)',$good_place);
        $result = $oDb->getData(); 

        $outStores = $this->prepareStores($result,$latlngsearch,$serch_product);
		return $outStores;
    }
    
    /**
    * Get All stores by products in query
    * @param string $serch_place
	* @param string $serch_product
    * @return array $outStores
    */
    public function gelAllStoresByUserDistance($serch_place,$serch_product)
    {
        $regionCollection = Mage::getModel('directory/region_api')->items("US");
        
        $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
        $google_key=Mage::getStoreConfig('stores/settings/googlemapsapi');
        
         // Get user query coordinates
        $latlngsearch = $this->getLatLng(trim($serch_place),1);

        $oDb = Mage::getResourceModel('stores/state_collection');
        $result = $oDb->getData(); 

		$outStores = $this->prepareStores($result,$latlngsearch,$serch_product);
        return $outStores;

    }
	/**
    * Save Lat Lng, Add Distance to Store Array, Add filter for products
    * @param array $storesData
	* @param array $latlngsearch
	* @param string $serch_product
    * @return array $outStores
    */
	public function prepareStores($storesData,$latlngsearch,$serch_product){
	   //echo "sfdsd";exit;
	   $soresUpdated = $this->saveLatLng($storesData,$latlngsearch['lat'],$latlngsearch['lng']);
		$addDistanceStores = $this->addDistanceToStoresArr($soresUpdated,$latlngsearch['lat'],$latlngsearch['lng']);
		$outStores = $this->getStoresFinal($serch_product,$addDistanceStores);
		usort($outStores,array($this,"cmp_distanse"));
        return $outStores;
	}
	
	/**
    * Filter Stores Array bu searching product
	* @param string $serch_product
	* @param array $arr
    * @return array $arr
    */
	public function getStoresFinal($serch_product,$arr){
		
		if ((strtolower($serch_product)!='all') && (trim($serch_product)!='')){
            $productsByUserQuery = $this->returnProducts($serch_product);

            $outStores = array();
            foreach ($arr as $oneStore) {
                $idProductInStore = unserialize($oneStore['desc']);
                $compareResult = array_intersect($productsByUserQuery,$idProductInStore);
                if ((count($compareResult)>0) || ((count($productsByUserQuery)>0) && ($oneStore['is_all_products']==1))) {
                    $outStores[] = $oneStore;
                }
            }
            $arr = $outStores;
        }
		return $arr;
	}
	
	/**
    * Save Lat Lng for stores, that haven't this data
	* @param array $storesArr
    * @return array $arr
    */
	public function saveLatLng($storesArr){
	   
	   foreach($storesArr as $row){
            if (($row['lat']=="")||($row['lng']=="")){
                $latlng = $this->getLatLng($row);
                $lat_store=$latlng['lat'];
                $lng_store=$latlng['lng'];
                $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');

                $aDBInfo['lat'] = $lat_store;        
                $aDBInfo['lng'] = $lng_store;
                $resu = $oDb->update($this->_table,$aDBInfo,array('id = '.$row['id']));       
            } else {
                $lat_store=$row['lat'];
                $lng_store=$row['lng'];
            } 
            $arr[] = $row;
        }
		return $arr;
	}
	
	/**
    * Add Distance for Stores array
	* @param array $storesArr
	* @param float $lat
	* @param float $lng
    * @return array $arrStores
    */
	public function addDistanceToStoresArr($storesArr,$lat,$lng){
		
		foreach($storesArr as $row){
			$distantion = Mage::Helper('stores')->calculateTheDistance($lat, $lng, $row['lat'], $row['lng']);
			$row['distance'] = $distantion;
			$arrStores[] = $row;
		}
		return $arrStores;
	}
	
    /**
    * Get Media Image for current Store, if there isn't return false
    *
    * @return array $media
    */
	public function getMedia($id)
    {
        $result = Mage::getResourceModel('stores/state_collection');
        $result->addFieldToFilter('id', $id);
        
        $media = $result->getFirstItem()->getData();

        return $media;
    }
    
	/**
    * Compare two distance
    * @s1 Array with Distance Param, @s2 Array with Distance Param
    * @return Compare result
    */
    public function cmp_distanse($s1, $s2)
    {
        return Mage::Helper('stores')->cmp_distanse($s1, $s2);
    }
    
    /**
    * Get Lat and Lng by Address by Google Maps Api query
    * @param array $row
	* @return array 
    */
	public function getLatLng($row,$flag = 0)
    {
        return Mage::Helper('stores')->getLatLng($row,$flag);
    }
    
	/**
    * Get Products From Magento Store that same with Customer Query
    * @param string $searchterms
	* @return $products
    */
	public function returnProducts($searchterms) {

        //echo $searchterms; die;
		$productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addAttributeToSelect('*');
        
        $lowercassearch = strtolower(trim($searchterms));
        $strlen = strlen($lowercassearch);
        
        $products = array();
        
        if ($strlen >= self::MIN_COUNT_OF_PRODUCT_NAME) {
           
			foreach ($productCollection as $product) {
                $lowercaseproduct = strtolower($product->getName());
                $keywordmatch = strpos($lowercaseproduct, $lowercassearch);
                if (!($keywordmatch === false)) {
                    $products[] = $product->getId();
                }
            }
        }
        return $products;
    }
}