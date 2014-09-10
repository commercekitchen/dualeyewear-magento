<?php
/**
 * @author Ocodewire (ocodewire.com)
 * @copyright  Copyright (c)  ocodewire
 * @version : 1.0.2
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Browsewire_Cmbannerslider_Adminhtml_CmbannersliderController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('cmbannerslider/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}   

	public function indexAction() {
		$this->_initAction();
		 $this->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('cmbannerslider/cmbannerslider')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('cmbannerslider_data', $model);
			$this->loadLayout();
			$this->_setActiveMenu('cmbannerslider/items');
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('cmbannerslider/adminhtml_cmbannerslider_edit'))
				->_addLeft($this->getLayout()->createBlock('cmbannerslider/adminhtml_cmbannerslider_edit_tabs'));
			$this->renderLayout();

		} else {

			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cmbannerslider')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');			

					// Any extention would work
	           			$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$newName    = time() . $_FILES['filename']['name'];

					// Set the file upload mode 

					// false -> get the file directly in the specified folder

					// true -> get the file in the product like folders 

					//	(file.jpg will go in something like /media/f/i/file.jpg)

					$uploader->setFilesDispersion(false);

					// We set media/cmbannerslider as the upload dir

					$path = Mage::getBaseDir('media') . DS .'cmbannerslider'. DS ;	
					$uploader->save($path, $newName);

				} catch (Exception $e) {
		        }

		        //this way the name is saved in DB
				
				$data['filename'] = $newName; 
		}
			$model = Mage::getModel('cmbannerslider/cmbannerslider');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	

				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cmbannerslider')->__('Item was successfully saved'));
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

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cmbannerslider')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}

 

	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			        $bannerId = $this->getRequest()->getParam('id');
				$bannerDBTableCollection= Mage::getModel('cmbannerslider/cmbannerslider')->getCollection();
				$fileDetails = $bannerDBTableCollection->getItemById($bannerId);
				$pathToFile = Mage::getBaseDir('media') . DS .'cmbannerslider'. DS .$fileDetails['filename'] ;
				//$path2 = Mage::getBaseDir('media') . DS ;
				if (file_exists($pathToFile)){ // if file exists
				chmod ($pathToFile, 0755);
				unlink($pathToFile); // delete file
				}

			try {
				$model = Mage::getModel('cmbannerslider/cmbannerslider');
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {

        $bannersliderIds = $this->getRequest()->getParam('cmbannerslider');
        if(!is_array($bannersliderIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));

        } else {
            try {
                foreach ($bannersliderIds as $bannersliderId) {

			        // $bannerId = $this->getRequest()->getParam('id');
				$bannerDBTableCollection= Mage::getModel('cmbannerslider/cmbannerslider')->getCollection();
				$fileDetails = $bannerDBTableCollection->getItemById($bannersliderId);
				$pathToFile = Mage::getBaseDir('media') . DS .'cmbannerslider'. DS .$fileDetails['filename'] ;
				//$path2 = Mage::getBaseDir('media') . DS ;
				if (file_exists($pathToFile)){ // if file exists
				chmod ($pathToFile, 0755);
				unlink($pathToFile); // delete file
				}


                    $bannerslider = Mage::getModel('cmbannerslider/cmbannerslider')->load($bannersliderId);
                    $bannerslider->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($bannersliderIds)
                    )
                );

            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $bannersliderIds = $this->getRequest()->getParam('cmbannerslider');
        if(!is_array($bannersliderIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannersliderIds as $bannersliderId) {
                    $bannerslider = Mage::getSingleton('cmbannerslider/cmbannerslider')
                        ->load($bannersliderId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }

                $this->_getSession()->addSuccess(

                    $this->__('Total of %d record(s) were successfully updated', count($bannersliderIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'cmbannerslider.csv';
        $content    = $this->getLayout()->createBlock('cmbannerslider/adminhtml_cmbannerslider_grid')
            ->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'cmbannerslider.xml';
        $content    = $this->getLayout()->createBlock('cmbannerslider/adminhtml_cmbannerslider_grid')
            ->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
