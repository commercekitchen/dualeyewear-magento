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

class MageWorx_Downloads_Block_Sales_Order_Email_Invoice_Items extends Mage_Sales_Block_Order_Email_Invoice_Items
{
    public function _toHtml()
    {
        $html = parent::_toHtml();

        if (!Mage::helper('downloads')->isEnabledInEmails()) {
            return $html;
        }

        $fileIds = array();
        $order = $this->getOrder();
        foreach ($order->getAllItems() as $item) {
            $ids = Mage::getResourceSingleton('downloads/relation')->getFileIds($item->getProductId());
            $fileIds = array_merge($fileIds, $ids);
        }

        $fileIds = array_unique($fileIds);

        $files = Mage::getResourceModel('downloads/files_collection');
        $files->addResetFilter()
            ->addFilesFilter($fileIds)
            ->addStatusFilter()
            ->addCategoryStatusFilter()
            ->addStoreFilter()
            ->addSortOrder();

        if (!$files->count()) {
            return $html;
        }

        $filesBlock = $this->getLayout()->createBlock('downloads/link', 'downloads', array('ids' => $fileIds, 'is_email' => 1));
        $html .= $filesBlock->toHtml();
        return $html;
    }
}
