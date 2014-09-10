<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Afptc
 * @version    1.1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Afptc_Adminhtml_RulesController extends Mage_Adminhtml_Controller_Action
{
    protected function displayTitle($data = null, $root = 'Add Free Product To Cart')
    {
        if (!version_compare(Mage::getVersion(), '1.4', '<')) {
            if ($data) {
                if (!is_array($data)) {
                    $data = array($data);
                }
                $this->_title($this->__($root));
                foreach ($data as $title) {
                    $this->_title($this->__($title));
                }
            } else {
                $this->_title($this->__('Rules'))->_title($root);
            }
        }
        return $this;
    }

    protected function _initRule()
    {
        $ruleModel = Mage::getModel('awafptc/rule');
        $ruleId  = (int) $this->getRequest()->getParam('id');
        if ($ruleId) {
            try {
                $ruleModel->load($ruleId);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        if (null !== Mage::getSingleton('adminhtml/session')->getFormActionData()) {
            $ruleModel->addData(Mage::getSingleton('adminhtml/session')->getFormActionData());
            Mage::getSingleton('adminhtml/session')->setFormActionData(null);
        }
        Mage::register('awafptc_rule', $ruleModel, true);

        return $ruleModel;
    }

    public function indexAction()
    {
        $this
            ->displayTitle('Rules')
            ->loadLayout()
            ->_setActiveMenu('promo')
            ->renderLayout()
        ;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $rule = $this->_initRule();
        $rule->getConditions()->setJsFormObject('rule_conditions_fieldset');
        Mage::register('awafptc_rule', $rule, true);

        $this->displayTitle('New Rule');
        if ($rule->getId()) {
            $this->displayTitle('Edit Rule');
        }

        $this
            ->loadLayout()
            ->_setActiveMenu('promo')
            ->renderLayout()
        ;
    }
   
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('awafptc/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        $html = '';
        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        }
        $this->getResponse()->setBody($html);
    }

    public function productGridAction()
    {        
        $this->getResponse()->setBody($this->getLayout()
            ->createBlock('awafptc/adminhtml_rules_product_grid')
            ->setCheckedValues((array) $this->getRequest()->getParam('checkedValues', array()))
            ->toHtml()
        );
    }

    public function saveAction()
    {
        try {
            $rule = $this->_initRule();
            $request = new Varien_Object($this->_filterDateTime(
                $this->getRequest()->getParams(), array('start_date', 'end_date')
            ));

            if (null !== $this->getRequest()->getParam('start_date', null)
                && !Zend_Date::isDate($request->getStartDate(), Varien_Date::DATETIME_INTERNAL_FORMAT)
            ) {
                $request->setStartDate(null);
                throw new Exception('"From Date" field value is not valid datetime format.');
            }

            if (null !== $this->getRequest()->getParam('end_date', null)
                && !Zend_Date::isDate($request->getEndDate(), Varien_Date::DATETIME_INTERNAL_FORMAT)
            ) {
                $request->setEndDate(null);
                throw new Exception('"To Date" field value is not valid datetime format.');
            }
            $this
                ->_prepareDates($request)
                ->_prepareConditions($request)
            ;

            $rule
                ->addData($request->getData())
                ->loadPost($request->getData())
                ->save()
            ;
            
            if (!$rule->getProductId() && $rule->getSimpleAction() == AW_Afptc_Model_Rule::BY_PERCENT_ACTION) {
                Mage::getSingleton('adminhtml/session')->addNotice($this->__('No action product specified'));
            }
            
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Rule successfully saved'));
        } catch (Exception $e) {
            $request->setBack(true);
            $this->_prepareDates($request);
            Mage::getSingleton('adminhtml/session')
                ->addError($e->getMessage())
                ->setFormActionData($request->getData())
            ;
        }

        if ($request->getBack()) {
            return $this->_redirect('*/*/edit', array('id' => $rule->getId(), 'tab' => $request->getTab()));
        }
        return $this->_redirect('*/*/');
    }

    protected function _prepareDates(Varien_Object $request)
    {
        if (null !== $request->getStartDate()) {
            $request->setStartDate(Mage::getSingleton('core/date')
                ->gmtDate(null, $request->getStartDate())
            );
        }
        if (null !== $request->getEndDate()) {
            $request->setEndDate(Mage::getSingleton('core/date')
                ->gmtDate(null, $request->getEndDate())
            );
        }
        return $this;
    }

    protected function _prepareConditions(Varien_Object $request)
    {
        $data = $request->getData();
        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);
            $request->setData($data);
        }
        return $this;
    }
 
    public function deleteAction()
    {
        try {
            $request = $this->getRequest()->getParams();

            if (!isset($request['id'])) {
                throw new Mage_Core_Exception($this->__('Incorrect rule id'));
            }

            $ruleModel = $this->_initRule();
            $ruleModel->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Rule successfully deleted'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        return $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        try {
            $ruleIds = $this->getRequest()->getParam('rules');

            if (!is_array($ruleIds)) {
                throw new Mage_Core_Exception($this->__('Invalid rule ids'));
            }

            foreach ($ruleIds as $rule) {
                Mage::getSingleton('awafptc/rule')->setId($rule)->delete();
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('%d rule(s) have been successfully deleted', count($ruleIds))
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        try {
            $ruleIds = $this->getRequest()->getParam('rules');
            $status = $this->getRequest()->getParam('status', null);
            if (!is_array($ruleIds)) {
                throw new Mage_Core_Exception($this->__('Invalid rule ids'));
            }

            if (null === $status) {
                throw new Mage_Core_Exception($this->__('Invalid status value'));
            }

            foreach ($ruleIds as $rule) {
                Mage::getSingleton('awafptc/rule')->setId($rule)->setStatus($status)->save();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('%d rule(s) have been successfully updated', count($ruleIds))
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/promo/awafptc/rules');
    }
}