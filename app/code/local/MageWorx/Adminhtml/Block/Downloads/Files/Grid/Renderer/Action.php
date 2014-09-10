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
class MageWorx_Adminhtml_Block_Downloads_Files_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('downloads');
        $actions = array();
        $actions[] = array(
            '@' => array(
                'href' => $this->getUrl('*/*/edit', array(
                        'id' => $row->getId(),
                        'store' => Mage::registry('store_id')
                    )
                ),
            ),
            '#' => $helper->__('Edit')
        );
        $actions[] = array(
            '@' => array(
                'href' => $this->getUrl('*/*/download', array(
                        'id' => $row->getId(),
                        'store' => Mage::registry('store_id')
                    )
                ),
            ),
            '#' => $helper->__('Download')
        );
        $actions[] = array(
            '@' => array(
                'href' => '#',
                'onclick' => "alert('{$helper->getDownloadLink($row)}'); return false;"
            ),
            '#' => $helper->__('Get Link')
        );

        return $this->_actionsToHtml($actions);
    }

    protected function _actionsToHtml(array $actions)
    {
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode('<span class="separator">&nbsp;|&nbsp;</span>', $html);
    }

}