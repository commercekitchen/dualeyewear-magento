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
class MageWorx_Downloads_Block_Link extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('downloads/block_file_links.phtml');
    }

    protected function _prepareLayout()
    {
        if ($this->getIsEmail()) {
            $this->setTemplate('downloads/email_file_links.phtml');
        }

        $title = trim($this->getTitle());
        if (empty($title)) {
            $this->setTitle(Mage::helper('downloads')->getFileDownloadsTitle());
        }

        $id = $this->getId();
        if (empty($id) && $this->getIds()) {
            $id = implode(',', $this->getIds());
        }

        if (empty($id)) {
            return '';
        } else {
            $ids = explode(',', $id);
            $files = Mage::getResourceModel('downloads/files_collection');
            $files->addResetFilter()
                ->addFilesFilter($ids)
                ->addStatusFilter()
                ->addCategoryStatusFilter()
                ->addStoreFilter()
                ->addSortOrder();

            $items = $files->getItems();
            foreach ($items as $k => $item) {
                if (!Mage::helper('downloads')->checkCustomerGroupAccess($item) && Mage::helper('downloads')->isHideFiles()) {
                    unset($items[$k]);
                }
            }

            if (Mage::helper('downloads')->getGroupByCategory()) {
                $items = $this->groupFiles($items);
            }

            $this->setItems($items);
        }

        return parent::_prepareLayout();
    }

    public function groupFiles($files)
    {
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

    protected function _toHtml()
    {
        if (!Mage::helper('downloads')->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }

}
