<?php

class Browsewire_Cmbannerslider_Block_Adminhtml_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$imagename = $this->_getValue($row);
	        $data = "<img src='".Mage::getBaseUrl("media").DS.'cmbannerslider'.DS.$imagename."' width='253' height='163'/>";
		return $data;
	}	
} 

?>
