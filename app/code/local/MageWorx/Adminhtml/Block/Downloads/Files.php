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
class MageWorx_Adminhtml_Block_Downloads_Files extends MageWorx_Adminhtml_Block_Downloads_Abstract
{
    protected function _prepareLayout()
    {
        $this->setChild('add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('downloads')->__('Upload File'),
                'onclick' => "setLocation('" . $this->getUrl('*/*/new', array('store' => $this->getStoreId())) . "')",
                'class' => 'add'
            ))
        );

        $this->setChild('multi_upload_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('downloads')->__('Multi Upload'),
                'onclick' => "setLocation('" . $this->getUrl('*/*/multiupload', array('store' => $this->getStoreId())) . "')",
                'class' => 'add'
            ))
        );
        $this->setChild('grid', $this->getLayout()->createBlock('mageworx/downloads_files_grid', 'files.grid'));
        return parent::_prepareLayout();
    }

    public function getMultiUploadButtonHtml()
    {
        return $this->getChildHtml('multi_upload_button');
    }

    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

}