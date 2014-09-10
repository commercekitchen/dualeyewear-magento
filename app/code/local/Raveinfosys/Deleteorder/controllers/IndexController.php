<?php
class Raveinfosys_Deleteorder_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/deleteorder?id=15 
    	 *  or
    	 * http://site.com/deleteorder/id/15 	
    	 */
    	/* 
		$deleteorder_id = $this->getRequest()->getParam('id');

  		if($deleteorder_id != null && $deleteorder_id != '')	{
			$deleteorder = Mage::getModel('deleteorder/deleteorder')->load($deleteorder_id)->getData();
		} else {
			$deleteorder = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($deleteorder == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$deleteorderTable = $resource->getTableName('deleteorder');
			
			$select = $read->select()
			   ->from($deleteorderTable,array('deleteorder_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$deleteorder = $read->fetchRow($select);
		}
		Mage::register('deleteorder', $deleteorder);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}