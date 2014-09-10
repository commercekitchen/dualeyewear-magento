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
class MageWorx_Downloads_Model_Mysql4_Relation extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('downloads/relation', 'id');
    }

    public function deleteFile($id)
    {
        $this->_getReadAdapter()->delete(
            $this->getMainTable(),
            $this->_getReadAdapter()->quoteInto('file_id = ?', $id, 'INTEGER')
        );
        return $this;
    }

    public function deleteFilesProduct($productId)
    {
        $this->_getReadAdapter()->delete(
            $this->getMainTable(),
            $this->_getReadAdapter()->quoteInto('product_id = ?', $productId, 'INTEGER')
        );
        return $this;
    }

    public function deleteFileProducts($fileId)
    {
        $this->_getReadAdapter()->delete(
            $this->getMainTable(),
            $this->_getReadAdapter()->quoteInto('file_id = ?', $fileId, 'INTEGER')
        );
        return $this;
    }

    public function getFileIds($productId, $onlyActive = false)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('r' => $this->getMainTable()), new Zend_Db_Expr('DISTINCT r.file_id'));

        if ($onlyActive === true) {
            $files = Mage::getResourceSingleton('downloads/files');
            $select->join(array('f' => $files->getMainTable()), 'r.file_id = f.file_id', array())
                ->where('f.is_active = ?', MageWorx_Downloads_Helper_Data::STATUS_ENABLED);
        }
        $select->where('r.product_id = ?', $productId);

        $result = array();
        $data = $this->_getReadAdapter()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }
        return $result;
    }

    public function getProductIds($fileId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('DISTINCT `product_id`'))
            ->where('file_id = ?', $fileId);

        $result = array();
        $data = $this->_getReadAdapter()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }
        return $result;
    }


    public function getCategoryIds($productId)
    {
        $files = Mage::getResourceSingleton('downloads/files');
        $select = $this->_getReadAdapter()->select()
            ->from(array('r' => $this->getMainTable()), new Zend_Db_Expr('DISTINCT f.category_id'))
            ->join(array('f' => $files->getMainTable()), 'r.file_id = f.file_id', array())
            ->where('r.product_id = ?', $productId);

        $result = array();
        $data = $this->_getReadAdapter()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }
        return $result;
    }

}
