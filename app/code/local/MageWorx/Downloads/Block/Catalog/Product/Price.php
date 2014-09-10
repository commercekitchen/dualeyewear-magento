<?php

class MageWorx_Downloads_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price
{
    protected function _toHtml()
    {
        $helper = Mage::helper('downloads');
        $isCategory = Mage::registry('current_category') && !Mage::registry('current_product');
        $toolbar = $this->getLayout()->getBlock('product_list_toolbar');
        $isGridMode = $toolbar && $toolbar->getCurrentMode() && $toolbar->getCurrentMode() == 'grid';

        $html = parent::_toHtml();

        if(!$isCategory
            || !$helper->isEnabled()
            || !$helper->isEnableFilesOnCategoryPages()
            || !$this->getProduct()
            || !$this->getProduct()->getId()){
            return $html;
        }

        $filesBlock = $this->getLayout()->createBlock('downloads/product_link', '', array('id' => $this->getProduct()->getId(), 'is_category' => true, 'is_grid_mode' => $isGridMode))->toHtml();
        $html .= $filesBlock;

        return $html;
    }
}