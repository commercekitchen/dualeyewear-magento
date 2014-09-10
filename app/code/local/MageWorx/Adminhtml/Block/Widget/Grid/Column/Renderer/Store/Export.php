<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx Adminhtml extension
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_Adminhtml_Block_Widget_Grid_Column_Renderer_Store_Export extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store
{
    public function render(Varien_Object $row)
    {
        $skipAllStoresLabel = $this->_getShowAllStoresLabelFlag();
        $origStores = $row->getData($this->getColumn()->getIndex());
        $showNumericStores = (bool)$this->getColumn()->getShowNumericStores();
        $stores = array();
        if (!is_array($origStores)) {
            $origStores = array($origStores);
        }
        foreach ($origStores as $origStore) {
            if (is_numeric($origStore)) {
                if (0 == $origStore) {
                    if (!$skipAllStoresLabel) {
                        $stores[] = Mage::helper('adminhtml')->__('All Store Views');
                    }
                } elseif ($storeName = $this->_getStoreModel()->getStoreName($origStore)) {
                    if ($this->getColumn()->getStoreView()) {
                        $store = $this->_getStoreModel()->getStoreNameWithWebsite($origStore);
                    } else {
                        $store = $this->_getStoreModel()->getStoreNamePath($origStore);
                    }
                    $layers = array();
                    foreach (explode('/', $store) as $key => $value) {
                        $layers[] = ' ' . $value;
                    }
                    $stores[] = implode(' > ', $layers);
                }
                elseif ($showNumericStores) {
                    $stores[] = $origStore;
                }
            } elseif (is_null($origStore) && $row->getStoreName()) {
                $stores[] = $row->getStoreName() . ' ' . $this->__('[deleted]');
            }
            else {
                $stores[] = $origStore;
            }
        }
        return $stores ? join(' > ', $stores) : ' ';
    }
}
