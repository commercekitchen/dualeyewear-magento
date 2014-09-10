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

class MageWorx_Downloads_Model_Mysql4_Files extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('downloads/files', 'file_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        $object->setCustomerGroups(implode(',', (array)$object->getCustomerGroups()));
        if (!$object->getId()) {
            $object->setDateAdded(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setDateModified(Mage::getSingleton('core/date')->gmtDate());

        $origData = $object->getOrigData();
        if (!isset($origData)) {
            Mage::getResourceSingleton('downloads/relation')->deleteFile($object->getId());
        }
        return parent::_beforeSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        Mage::getSingleton('downloads/files')->removeDownloadsFile($object->getId());
        Mage::getResourceSingleton('downloads/relation')->deleteFile($object->getId());

        return parent::_beforeDelete($object);
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $productIds = '';
        if ($object->getId()) {

            if (!is_array($object->getStoreIds())) {
                $object->setStoreIds(explode(',', $object->getStoreIds()));
            }

            $groups = $object->getCustomerGroups();
            if ($groups) {
                $object->setCustomerGroups(explode(',', $groups));
            }

            $product = Mage::getResourceSingleton('downloads/relation')->getProductIds($object->getId());
            if ($product) {
                $productIds = implode(',', $product);
            }
            $object->setInProducts($productIds);
        }
        return parent::_afterLoad($object);
    }

    public function getCategoryFiles($categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('category_id = ?', $categoryId);

        return $this->_getReadAdapter()->fetchAssoc($select);
    }

    public function getCountFiles($categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('COUNT(' . $this->getIdFieldName() . ')'))
            ->where('category_id = ?', $categoryId);

        return $this->_getReadAdapter()->fetchOne($select);
    }
}