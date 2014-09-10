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

class MageWorx_Downloads_Model_Categories extends Mage_Core_Model_Abstract
{
    protected static $_url = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('downloads/categories');
    }

    public function getCategoriesList($type = null)
    {
        $layouts = array();
        $categories = $this->getResource()->getAccessCategories();
        if ($categories) {
            foreach ($categories as $value) {
                if (is_null($type)) {
                    $layouts[$value['category_id']] = (string)$value['title'];
                } else {
                    $layouts[$value['title']] = (string)$value['title'];
                }
            }
        }
        return $layouts;
    }

    public function getUrlInstance()
    {
        if (!self::$_url) {
            self::$_url = Mage::getModel('core/url');
        }
        return self::$_url;
    }
}