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
 * @package    Belvg_All
 * @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Stores_Helper_Data extends Mage_Core_Helper_Abstract{
    
    const EARTH_RADIUS = 6372795;
    const LATITUDE_KEY = 0;
	const LONGITUDE_KEY = 1;
    
	/**
    * Calculate distance function between two point on Earth
    * @param float $fA
	* @param float $lAA
	* @param float $fB
	* @param float $lAB
	* @return float $dist
    */
	public function calculateTheDistance ($fA, $lAA, $fB, $lAB)
    {
        $lat1 = $fA * M_PI / 180;
        $lat2 = $fB * M_PI / 180;
        $long1 = $lAA * M_PI / 180;
        $long2 = $lAB * M_PI / 180;

        $cl1 = cos($lat1);
        $cl2 = cos($lat2);
        $sl1 = sin($lat1);
        $sl2 = sin($lat2);
        $delta = $long2 - $long1;
        $cdelta = cos($delta);
        $sdelta = sin($delta);

        $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;
        $ad = atan2($y, $x);
        $dist = $ad * self::EARTH_RADIUS;
        
        return $dist;
    }
    
	/**
    * Get Lat and Lng by Address by Google Maps Api query
    * @param array $row
	* @return array $result
    */
    public function getLatLng($row,$flag=0)
    {
        
        $result = array();
        $result['lat'] = 0;
        $result['lng'] = 0;
           
        if ($flag == 1){
            $new_url = urlencode($row);
            $result['eng_addr'] = $row;
        } else {
            $new_url = urlencode($row['country']." ".$row['state']." ".$row['city']." ".$row['address']." ".$row['zip_code']);        
        }
        
        $eng_addr = "";
        $url="http://maps.google.com/maps/geo?q=".$new_url."&key=".""."&sensor=false&hl=en";
        
        $pars_address=file_get_contents($url);
        $p_array=json_decode($pars_address,true);
        
        $lat_lng=(array)$p_array;
        $array_iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($lat_lng));
        
        foreach($array_iterator as $key=>$value){
            if ($key === self::LATITUDE_KEY) {$result['lat'] = $value;}
            if ($key === self::LONGITUDE_KEY) {$result['lng'] = $value;}
            if ($key==='address') {
                if ($key == ""){
                    $result['eng_addr'] = $row;
                } else {
                    $result['eng_addr'] = $value;   
                }
            }
        }
        return $result;
    }
    
    /**
    * Campare two distance 
    * @param array $s1
	* @param array $s1
	* @return int result
    */
	
	public function cmp_distanse($s1, $s2)
    {
        if ($s1['distance'] == $s2['distance']) {
            return 0;
        }
        return ((int)$s1['distance'] < (int)$s2['distance']) ? -1 : 1;
    }
    
    /**
    * Get Countries Collection
    * @return array $counrtyArr
    */
	public function getCountryCollection()
    {
        $countryCollection = Mage::getModel('directory/country_api')->items();
        $counrtyArr = array();
        foreach ($countryCollection as $counrty) {
            $counrtyArr[$counrty['name']] = $counrty['name'];
        }
        return $counrtyArr;
    }
	
	/**
    * Get google maps loacale array
    * @return array of countries
    */
	public function getLoacaleUser()
    {
        return array('ar','eu','bg','bn','ca','cs','da','de','el','en','en-AU','en-GB','es','eu','fa','fi','fil','fr','gl','gu','hi','hr','hu','id','it','iw','ja','kn','ko','lt','lv','ml','mr','nl','nn','no','or','pl','pt','pt-BR','pt-PT','rm','ro','ru','sk','sl','sr','sv','tl','ta','te','th','tr','uk','vi','zh-CN','zh-TW');
    }
}