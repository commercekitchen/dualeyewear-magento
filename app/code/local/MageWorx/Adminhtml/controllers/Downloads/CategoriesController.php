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

class MageWorx_Adminhtml_Downloads_CategoriesController extends Mage_Adminhtml_Controller_Action
{
    protected function _init()
    {
        $sessionStore = Mage::getSingleton('adminhtml/session')->getData('store_id');
        if (!is_null($this->getRequest()->getParam('store'))) {
            Mage::getSingleton('adminhtml/session')->setData('store_id', $this->_getStoreId());
        } elseif (!is_null($sessionStore)) {
            $this->getRequest()->setParam('store', $sessionStore);
        }
        Mage::register('store_id', $this->_getStoreId());

        $this->_title($this->__('Downloads'))
            ->_title($this->__('Manage Categories'));
    }

    protected function _getStoreId()
    {
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(true)->getId();
        }
        return Mage::app()->getStore((int)$this->getRequest()->getParam('store', 0))->getId();
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('cms/downloads');
        return $this;
    }

    public function indexAction()
    {
        $this->_init();
        $this->_initAction()->renderLayout();
    }

    protected function _redirect($path, $arguments = array())
    {
        $arguments['store'] = $this->_getStoreId();
        parent::_redirect($path, $arguments);
    }

    public function editAction()
    {
        $this->_init();
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('downloads/categories')->load($id);

        if($id){
            $this->_title($model->getTitle());
        } else {
            $this->_title($this->__('New Category'));
        }

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('downloads_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('cms/downloads');
            $this->_addContent($this->getLayout()->createBlock('mageworx/downloads_categories_edit'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('downloads')->__('Categories does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $this->_init();
        $data = $this->getRequest()->getPost();
        if ($data) {
            $model = Mage::getModel('downloads/categories');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('downloads')->__('Category was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('downloads')->__('Unable to find Category to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $this->_init();
        $id = (int)$this->getRequest()->getParam('id');

        if (!Mage::helper('downloads')->isDefaultCategoryId($id)) {
            try {
                Mage::getSingleton('downloads/categories')->setId($id)->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('downloads')->__('Category was successfully deleted'));
                $this->_redirect('*/*/');

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $this->_init();
        $categoryIds = $this->getRequest()->getParam('categories');

        if (!is_array($categoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('downloads')->__('Please select Category(ies)'));
        } else {
            try {
                $i = 0;
                foreach ($categoryIds as $categoryId) {
                    if (!Mage::helper('downloads')->isDefaultCategoryId($categoryId)) {
                        $category = Mage::getModel('downloads/categories')->load($categoryId);
                        $category->delete();
                        ++$i;
                    }
                }
                if ($i) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('downloads')->__('Total of %d record(s) were successfully deleted', $i)
                    );
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $this->_init();
        $categoryIds = $this->getRequest()->getParam('categories');

        if (!is_array($categoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Category(ies)'));
        } else {
            try {
                foreach ($categoryIds as $categoryId) {
                    Mage::getSingleton('downloads/categories')
                        ->load($categoryId)
                        ->setIsActive((int)$this->getRequest()->getParam('is_active'))
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($categoryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}