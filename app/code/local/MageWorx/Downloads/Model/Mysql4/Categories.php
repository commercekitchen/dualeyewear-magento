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

class MageWorx_Downloads_Model_Mysql4_Categories extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('downloads/categories', 'category_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setStoreId($this->getStoreId());
        if (!$this->isUniqueCategory($object)) {
            Mage::throwException(Mage::helper('downloads')->__("Category '%s' already exist", $object->getTitle()));
        }
        return parent::_beforeSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        if (!Mage::helper('downloads')->isDefaultCategoryId($object->getId())) {
            $files = Mage::getModel('downloads/files');
            $data = $files->getResource()->getCategoryFiles($object->getId());
            if ($data) {
                foreach ($data as $file) {
                    $files->load($file[$files->getIdFieldName()])
                        ->setCategoryId(MageWorx_Downloads_Helper_Data::DEFAULT_CATEGORY_ID)
                        ->save();
                }
            }
        }
        return parent::_beforeDelete($object);
    }

    public function isUniqueCategory(Mage_Core_Model_Abstract $object)
    {
        $title = trim($object->getTitle());
        if (!empty($title)) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), $this->getIdFieldName())
                ->where('title = ?', $title);
            if ($object->getId()) {
                $select->where($this->getIdFieldName() . ' <> ?', $object->getId());
            }
            if ($this->_getReadAdapter()->fetchRow($select)) {
                return false;
            }
        }
        return true;
    }

    public function getAccessCategories($type = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable());
        if ($type === true) {
            $select->where('is_active = ?', MageWorx_Downloads_Helper_Data::STATUS_ENABLED);
        }
        $select->order('title ' . Varien_Data_Collection::SORT_ORDER_ASC);

        return $this->_getReadAdapter()->fetchAll($select);
    }

    protected function getStoreId()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::registry('store_id');
        } else {
            return Mage::app()->getStore()->getId();
        }
    }
}