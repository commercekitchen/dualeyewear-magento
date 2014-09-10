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

class Belvg_Stores_FrontController extends Mage_Core_Controller_Front_Action
{

    CONST minSymolsCountForResponse = 2;
	
	protected function _initAction()
    {
		$this->loadLayout();        
        return $this;
    }   
	
	/**
    * Generate Stores page on Frontend 
    *
    */
    public function pagesAction()
    {
		if (Mage::getStoreConfig('stores/settings/enabled',Mage::app()->getStore()) == 0) $this->_redirectUrl(Mage::getBaseUrl());
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $pagesBlock = $this->getLayout()->createBlock('stores/front')->setTemplate('stores/pages/view.phtml');
		$this->getLayout()->getBlock('content')->append($pagesBlock);
        $headBlock = $this->getLayout()->getBlock('head');

        $this->renderLayout();
    }
    
    /**
    * Generate Product Helper for ajax request
	* @return string html
    */
	public function productsloadAction()
    {
        
        $searchterms = $this->getRequest()->getParams('');
        $searchterms = $searchterms['q'];
        
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', array('neq' => 1));
        
        $lowercassearch = strtolower($searchterms);
        $strlen = strlen($lowercassearch);
		$outProducts = "";
        if ($strlen > self::minSymolsCountForResponse) {
            foreach ($collection as $product) {
                $lowercaseproduct = strtolower($product->getName());
                $keywordmatch = strpos($lowercaseproduct, $lowercassearch);
                if ($keywordmatch || $keywordmatch === 0) {
                    $outProducts.= trim($product->getName())."\n";
                }
            }
		$this->getResponse()->setBody($outProducts);	
        }
    }
}
