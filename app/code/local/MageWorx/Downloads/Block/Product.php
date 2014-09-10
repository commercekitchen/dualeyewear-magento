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
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Downloads extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
class MageWorx_Downloads_Block_Product extends Mage_Core_Block_Template
{
    protected $_template = 'downloads/block_file_links.phtml';

    protected function _prepareLayout()
    {
        $this->setData('template', $this->_template);
        $helper = Mage::helper('downloads');
        if (!$helper->isEnabled()) {
            return $this;
        }
        if ($productBlock = $this->getLayout()->getBlock('product.info')) {
            $product = $productBlock->getProduct();
        } else {
            return $this;
        }

        $productDownloadsTitle = trim($this->helper('catalog/output')->productAttribute($product, $product->getDownloadsTitle(), 'downloads_title'));
        if ($productDownloadsTitle) {
            $this->setTitle($productDownloadsTitle);
        } else {
            $title = trim($this->getTitle());
            if (empty($title)) {
                $this->setTitle(Mage::helper('downloads')->getProductDownloadsTitle());
            }
        }

        $productId = (int)$product->getId();

        $items = $this->getProductFiles($productId);

        if (Mage::helper('downloads')->getGroupByCategory() && $items && count($items)) {
            $items = $this->groupFiles($items);
        }

        if (count($items)) {
            $this->setItems($items);
        }

        if ($this->getNameInLayout() != 'downloads.tab') {
            $position = $helper->getBlockPosition();
            //print_r($position);
            //exit();
            if (in_array(1, $position)) {
                //$this->getLayout()->createBlock('downloads/product')->setTemplate('downloads/block_file_links.phtml')->toHtml();       
                /*     KTPL Updates	*/                
                $productBlock->append($this, 'other');
/*            KTPL Updates	*/               
            }
            if (in_array(2, $position)) {
                if ($additionalBlock = $this->getLayout()->getBlock('product.info.additional')) {
                    $additionalBlock->insert($this, '', false, 'downloads');
                }
            }
            if (in_array(3, $position)) {
                if ($tabsBlock = $this->getLayout()->getBlock('product.info.tabs')) {
                    $tabsBlock->addTab('downloads.tab', $this->getTitle(), $this->getType(), $this->_template);
                }
            }
        }
        return $this;
    }

    public function getProductFiles($productId)
    {
        $_helper = Mage::helper('downloads');
        $ids = Mage::getResourceSingleton('downloads/relation')->getFileIds($productId);

        if (is_array($ids) && $ids) {
            $files = Mage::getResourceSingleton('downloads/files_collection');
            $files->addResetFilter()
                ->addFilesFilter($ids)
                ->addStatusFilter()
                ->addCategoryStatusFilter()
                ->addStoreFilter()
                ->addSortOrder($_helper->getSortOrder());

            $items = $files->getItems();
            foreach ($items as $k => $item) {
                if (!$_helper->checkCustomerGroupAccess($item) && $_helper->isHideFiles()) {
                    unset($items[$k]);
                }
            }

            return $items;
        }

        return false;
    }

    public function groupFiles($files)
    {
        if (!is_array($files)) {
            return $files;
        }

        $grouped = array();

        foreach ($files as $item) {
            $grouped[$item->getCategoryId()]['files'][] = $item;
            $grouped[$item->getCategoryId()]['title'] = '';
        }

        foreach ($grouped as $id => $cat) {
            if ($catModel = Mage::getModel('downloads/categories')->load($id)) {
                $grouped[$id]['title'] = $catModel->getTitle();
            }
        }

        return $grouped;
    }

}
