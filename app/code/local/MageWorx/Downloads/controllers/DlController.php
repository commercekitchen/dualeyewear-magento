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
class MageWorx_Downloads_DlController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }

    public function fixAction()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();

        $select = $connection->select()->from($tablePrefix . 'downloads_files');
        $rows = $connection->fetchAll($select);

        foreach ($rows as $row) {
            $file = Mage::helper('downloads')->isDownloadsFile($row['file_id']);
            if ($file) {
                $pathInfo = pathinfo(current($file));
                $connection->update($tablePrefix . 'downloads_files', array('filename' => $pathInfo['basename']), 'file_id = ' . $row['file_id']);
            }
        }
    }

    public function fileAction()
    {
        $fileId = (int)$this->getRequest()->getParam('id');
        $files = Mage::getModel('downloads/files')->load($fileId);

        if ($files->getId()) {
            $helper = Mage::helper('downloads');

            if (!$helper->checkCustomerGroupAccess($files)) {
                $this->_getSession()->addNotice($helper->__("Requested file not available now"));
                return $this->_redirectReferer();
            } else {
                $files->setDownloads($files->getDownloads() + 1)->save();
            }

            $file = $helper->isDownloadsFile($files->getId());
            if (empty($file)) {
                if ($files->getUrl() != '') {
                    Mage::app()->getResponse()->setRedirect($files->getUrl());
                    return;
                } else {
                    Mage::throwException($helper->__('Sorry, there was an error getting the file'));
                    return $this->_redirectReferer();
                }
            }

            try {
                $helper->processDownload($file[0], $files);
                exit;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                return $this->_redirect('/');
            }
        } else {
            $this->_getSession()->addNotice(Mage::helper('downloads')->__("Requested file not available now"));
            return $this->_redirectReferer();
        }
        return $this->_redirectReferer();
    }

    public function getEmbedCodeAction()
    {
        $fileId = (int)$this->getRequest()->getParam('id');
        $file = Mage::getModel('downloads/files')->load($fileId);
        if ($file->getId()) {
            $file->setDownloads($file->getDownloads() + 1)->save();
            $html = '<div align="center">' . $file->getEmbedCode() . "</div>";
            echo $html;
        }

        exit();
    }

    public function updateDownloadsAction()
    {
        $id = $this->getRequest()->getParam('id');
        $file = Mage::getModel('downloads/files')->load($id);
        $file->setDownloads($file->getDownloads() + 1)->save();
        return $this;
    }
}
