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
class MageWorx_Adminhtml_Downloads_FilesController extends Mage_Adminhtml_Controller_Action
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
            ->_title($this->__('Manage Files'));
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
        $this->loadLayout()
            ->_setActiveMenu('cms/downloads');

        return $this;
    }

    public function indexAction()
    {
        $this->_init();
        Mage::getSingleton('adminhtml/session')->setIds(array());
        $this->_initAction()->renderLayout();
    }

    protected function _redirect($path, $arguments = array())
    {
        $arguments['store'] = $this->_getStoreId();
        parent::_redirect($path, $arguments);
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_init();
        Mage::getSingleton('adminhtml/session')->setUploadFiles(array());

        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('downloads/files')->load($id);

        if($id){
            $this->_title($model->getName());
        } else {
            $this->_title($this->__('New File'));
        }

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
                if ($id) {
                    $model->setId($id);
                }
            }
            Mage::register('downloads_data', $model);

            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('mageworx/downloads_files_edit'))
                ->_addLeft($this->getLayout()->createBlock('mageworx/downloads_files_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('File do not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function multiuploadAction()
    {
        $this->_init();
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('downloads/files')->load($id);
        Mage::getSingleton('adminhtml/session')->setUploadFiles(array());

        if($id){
            $this->_title($model->getName());
        } else {
            $this->_title($this->__('Multi Upload'));
        }

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
                if ($id) {
                    $model->setId($id);
                }
            }
            Mage::register('downloads_data', $model);

            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('mageworx/downloads_files_edit'))
                ->_addLeft($this->getLayout()->createBlock('mageworx/downloads_files_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('File do not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function uploadAction()
    {
        $fileId = $this->getRequest()->getParam('file_id', 0);
        $fileName = $this->getRequest()->getParam('qqfile', '');
        if (!is_dir(Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath())) {
            mkdir(Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath(), 0777, true);
        }
        $filePath = Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath() . DS . $fileName . uniqid('_', true);

        $registerKey = 'files_' . Mage::getSingleton('adminhtml/session')->getSessionId();
        $files = Mage::getSingleton('adminhtml/session')->getUploadFiles();
        if ($files == null) {
            $files = array();
            $files[$registerKey] = array();
        }

        $file = array(
            'name' => $fileName,
            'path' => $filePath
        );

        $this->uploadFile($filePath);
        $files[$registerKey][] = $file;
        Mage::getSingleton('adminhtml/session')->setUploadFiles($files);

        $result = array("success" => true);
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    protected function uploadFile($path)
    {
        $input = fopen("php://input", "r");

        $output = fopen($path, "w");

        while ($content = fread($input, 1024)) {
            fwrite($output, $content);
        }
        fclose($input);
        fclose($output);
    }

    public function quickSaveAction()
    {
        $data = Mage::app()->getRequest()->getParams();

        $fileSession = Mage::getSingleton('adminhtml/session')->getUploadFiles();
        $registerKey = 'files_' . Mage::getSingleton('adminhtml/session')->getSessionId();

        if (isset($fileSession[$registerKey])) {
            foreach ($fileSession[$registerKey] as $fileDesc) {
                if (!file_exists($fileDesc['path'])) {
                    continue;
                }
                $file = Mage::getSingleton('downloads/files');
                $data['fd_file']['is_active'] = 1;
                $file->setData($data['fd_file']);
                $file->setName(substr($fileDesc['name'], 0, strrpos($fileDesc['name'], '.')));
                $file->setType(substr($fileDesc['name'], strrpos($fileDesc['name'], '.') + 1));
                $file->setFilename($fileDesc['name']);
                $file->setSize(filesize($fileDesc['path']));
                $file->save();

                $this->_moveFile($file, $fileDesc);

                $this->_setProductsFileRelation(array($data['product_id']), $file->getId(), false);
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('downloads')->__('Files(s) where successfully uploaded'));
            Mage::getSingleton('adminhtml/session')->setUploadFiles(array());
        }

    }

    public function saveAction()
    {
        $this->_init();
        $helper = Mage::helper('downloads');
        $data = $this->getRequest()->getPost();
        $id = (int)$this->getRequest()->getParam('id');
        $fileIds = Mage::getSingleton('adminhtml/session')->getIds();

        if ($fileIds) {
            try {
                $productIds = $this->_prepareProductIds($data);
                foreach ($fileIds as $fileId) {
                    $this->_setProductsFileRelation($productIds, $fileId);
                }
                Mage::getSingleton('adminhtml/session')->setIds(array());
                Mage::getSingleton('adminhtml/session')->addSuccess($helper->__('Files were successfully saved'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                if ($e->getMessage()) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
                Mage::getSingleton('adminhtml/session')->setIds(array());
                $this->_redirect('*/*/');
                return;
            }
        }
        if ($data) {
            $data = $helper->getFilter($data);

            $fileSession = Mage::getSingleton('adminhtml/session')->getUploadFiles();
            $registerKey = 'files_' . Mage::getSingleton('adminhtml/session')->getSessionId();
            $multiUpload = false;

            try {
                if ($_FILES['file'] && !empty($_FILES['file']['tmp_name']) && $_FILES['file']['error']  > 0) {
                    throw new Exception('An error occured during file uploading. [Error code: '.$_FILES['file']['error'].']');
                }

                if (($_FILES['file']['size'] > 0 && isset($data['general']['url']))
                    || isset($fileSession[$registerKey])
                    || ($_FILES['file']['size'] == 0 && isset($data['general']['url']) && $data['general']['url'] == '')
                ) {
                    unset($data['general']['url']);
                    unset($data['general']['embed_code']);
                }

                $productIds = $this->_prepareProductIds($data);

                if (isset($fileSession[$registerKey])) {
                    $multiUpload = true;

                    foreach ($fileSession[$registerKey] as $fileDesc) {

                        $file = Mage::getSingleton('downloads/files');
                        $file->setData($data['general']);
                        $file->setName(substr($fileDesc['name'], 0, strrpos($fileDesc['name'], '.')));
                        $file->setType(substr($fileDesc['name'], strrpos($fileDesc['name'], '.') + 1));
                        $file->setFilename($fileDesc['name']);
                        $file->setSize(filesize($fileDesc['path']));
                        $file->save();

                        $this->_moveFile($file, $fileDesc);

                        $this->_setProductsFileRelation($productIds, $file->getId(), $id);
                    }
                    Mage::getSingleton('adminhtml/session')->setUploadFiles(array());
                } else {
                    $file = Mage::getSingleton('downloads/files');
                    $file->setData($data['general']);
                    if ($id) {
                        $file->setId($id);
                    }
                    $file->save();
                }
                // Upload File
                if ($_FILES['file']['size'] > 0) {
                    $upload = $this->_uploadFile('file', $file->getId());
                    if (!empty($upload['error']) && !$id) {
                        $file->delete();
                        throw new Exception($this->__('Unable to save file'));
                    }
                    if ($upload) {
                        $file->setUrl('');
                        $file->setEmbedCode('');
                        $file->setFilename($_FILES['file']['name']);
                        $file->addData($upload)
                            ->setId($file->getId())
                            ->save();
                    }
                } else if (isset($data['general']['url'])) {
                    $files = $helper->getFiles($helper->getDownloadsPath($file->getId()));
                    if (!empty($files)) {
                        foreach ($files as $fileName) {
                            unlink($fileName);
                        }
                    }

                    $url = parse_url($data['general']['url']);
                    if (!isset($url['path'])) {
                        $url['path'] = '';
                    }
                    $ext = pathinfo($url['path'], PATHINFO_EXTENSION);
                    if (!$ext && !empty($data['general']['embed_code'])) {
                        $type = 'video';
                    } elseif (!$ext && empty($data['general']['embed_code'])) {
                        $type = 'link';
                    } else {
                        $type = $ext;
                    }

                    $file->setType($type);
                    $file->setFilename('');
                    $file->setSize(0);
                    $file->save();
                } elseif (isset($data['general']['filename'])) {
                    $file->setType(substr($data['general']['filename'], strrpos($data['general']['filename'], '.') + 1));
                    if ($file->getId()) {
                        $file->setSize(filesize(Mage::getBaseDir() . DS . 'media' . DS . 'downloads' . DS . $file->getId() . DS . $file->getFilename()));
                    }
                    $file->save();
                }

                $this->_setProductsFileRelation($productIds, $file->getId(), $id);

                $successMessage = $multiUpload ? $helper->__('Files were successfully saved') : $helper->__('File was successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($successMessage);
                Mage::getSingleton('adminhtml/session')->setData('downloads_data');

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $file->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                if ($e->getMessage()) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
                Mage::getSingleton('adminhtml/session')->setData('downloads_data', $data);
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError($helper->__('Unable to find File to save'));
        $this->_redirect('*/*/');
    }

    private function _uploadFile($keyFile, $fileId)
    {
        $result = array();
        if (isset($_FILES[$keyFile]['name']) && $_FILES[$keyFile]['name'] != '') {
            try {
                Mage::getSingleton('downloads/files')->removeDownloadsFile($fileId, false);

                $uploader = new Varien_File_Uploader($keyFile);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $newFileName = Mage::helper('downloads')->getDownloadsPath($fileId) . $_FILES[$keyFile]['name'];
                $uploader->save(Mage::helper('downloads')->getDownloadsPath($fileId), $_FILES[$keyFile]['name']);

                if (!file_exists($newFileName) || !filesize($newFileName)) {
                    $result['error'] = true;
                }

                $result['size'] = $_FILES[$keyFile]['size'];
                $result['type'] = substr($_FILES[$keyFile]['name'], strrpos($_FILES[$keyFile]['name'], '.') + 1);
            } catch (Exception $e) {
                if ($e->getMessage()) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        return $result;
    }

    public function productGridAction()
    {
        $key = 'internal_in_products';
        $this->getRequest()->setPost($key, $this->getRequest()->getParam($key));

        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mageworx/downloads_files_edit_tab_product')->toHtml()
        );
    }

    public function deleteAction()
    {
        $this->_init();
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = Mage::getSingleton('downloads/files');
                $model->setId($id)->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('File was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
        }
        $this->_redirect('*/*/');
    }

    public function resetDownloadsAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('files');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select File(s)'));
        } else {
            try {
                $model = Mage::getSingleton('downloads/files');
                foreach ($ids as $id) {
                    $model->load($id)
                        ->setDownloads(0)
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', count($ids)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function assignProductsAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('files');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select File(s)'));
        } else {
            try {
                $this->_initAction();
                $this->_addContent($this->getLayout()->createBlock('mageworx/downloads_files_edit'))
                    ->_addLeft($this->getLayout()->createBlock('mageworx/downloads_files_edit_tabs'));

                Mage::getSingleton('adminhtml/session')->setIds($ids);

                $this->renderLayout();
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
    }

    public function massDeleteAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('files');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select File(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('downloads/files')->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d record(s) were successfully deleted', count($ids)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('files');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select File(s)'));
        } else {
            try {
                $model = Mage::getSingleton('downloads/files');
                foreach ($ids as $id) {
                    $model->load($id)
                        ->setIsActive((int)$this->getRequest()->getParam('is_active'))
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', count($ids)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massCategoryAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('files');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select File(s)'));
        } else {
            try {
                $model = Mage::getSingleton('downloads/files');
                foreach ($ids as $id) {
                    $model->load($id)
                        ->setCategoryId((int)$this->getRequest()->getParam('category_id'))
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', count($ids)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massCustomerGroupsAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('files');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select File(s)'));
        } else {
            try {
                $model = Mage::getSingleton('downloads/files');
                $groupIds = $this->getRequest()->getParam('customer_groups');
                $groupIds = implode(',', $groupIds);
                foreach ($ids as $id) {
                    $model->load($id)
                        ->setCustomerGroups($groupIds)
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', count($ids)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function downloadAction()
    {
        $fileId = (int)$this->getRequest()->getParam('id');
        $files = Mage::getSingleton('downloads/files')->load($fileId);

        if ($files->getId()) {
            $helper = Mage::helper('downloads');
            $file = $helper->isDownloadsFile($files->getId());
            try {
                if (empty($file)) {
                    if ($files->getUrl() != '') {
                        Mage::app()->getResponse()->setRedirect($files->getUrl());
                        return;
                    } else {
                        Mage::throwException($helper->__('Sorry, there was an error getting the file'));
                    }
                }
                $helper->processDownload($file[0], $files);
                exit;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/index');
            }
        }
    }

    public function importAction()
    {
        $files = array();
        try {
            $dirPath = Mage::getBaseDir() . DS . 'media' . DS . 'downloads_import';
            if ($dir = opendir($dirPath)) {
                while (($file = readdir($dir)) !== false) {
                    if ($file == '..' || $file == '.') {
                        continue;
                    }

                    $filePath = $dirPath . DS . $file;
                    if (is_dir($filePath)) {
                        $catId = $file;
                        if ($subDir = opendir($filePath)) {
                            $dirName = $filePath;
                            while (($file = readdir($subDir)) !== false) {
                                if ($file == '..' || $file == '.') {
                                    continue;
                                }
                                $files[] = array('cat_id' => $catId, 'filepath' => $dirName . DS . $file);
                            }
                        }
                    } else {
                        $cat = Mage::getModel('downloads/categories')->getCollection()->getFirstItem();
                        if (!($catId = $cat->getId())) {
                            $catId = 0;
                        }
                        $files[] = array('cat_id' => $catId, 'filepath' => $filePath);
                    }

                }
            }
            $this->_getSession()->setFdImportFiles($files);
            $this->_getSession()->setSkippedCnt(0);
            $this->_getSession()->setSkippedIds(array());
            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    public function runImportAction()
    {
        @ini_set('max_execution_time', 1800);
        @ini_set('memory_limit', 734003200);
        $limit = Mage::helper('downloads')->getImportLimit();
        $current = intval($this->getRequest()->getParam('current', 0));
        $result = array();

        $files = $this->_getSession()->getFdImportFiles();

        $total = count($files);

        if ($current < $total) {

            $skippedNow = 0;
            for ($i = $current; $i < ($current + $limit); $i++) {
                if ($i > ($total - 1)) {
                    break;
                }

                $cat = Mage::getModel('downloads/categories')->load($files[$i]['cat_id']);
                if(!$cat || !$cat->getId()){
                    $this->_getSession()->setSkippedCnt($this->_getSession()->getSkippedCnt() + 1);
                    $skippedIds = $this->_getSession()->getSkippedIds();
                    $skippedIds[] = $files[$i]['cat_id'];
                    $this->_getSession()->setSkippedIds($skippedIds);
                    continue;
                }

                $file = pathinfo($files[$i]['filepath']);
                $data = array(
                    'name' => $file['filename'],
                    'type' => $file['extension'],
                    'filename' => $file['basename'],
                    'size' => filesize($files[$i]['filepath']),
                    'category_id' => $files[$i]['cat_id'],
                    'store_ids' => 0,
                    'is_active' => 1,
                );
                $fileModel = Mage::getModel('downloads/files');
                $fileModel->setData($data);
                $fileModel->save();

                $dest = Mage::helper('downloads')->getDownloadsPath($fileModel->getFileId());
                if (!is_dir($dest)) {
                    mkdir($dest, 0777, true);
                }
                if (!copy($files[$i]['filepath'], $dest . $fileModel->getFilename())) {
                    $this->_getSession()->addError($this->__('The file can not be uploaded'));
                }

                if (file_exists($dest . $fileModel->getFilename())) {
                    @unlink($files[$i]['filepath']);
                }
            }

            $current += $limit;
            if ($current > $total) {
                $current = $total;
            }
            $cnt = $this->_getSession()->getSkippedFiles();
            $result['text'] = $this->__('Total %1$s, processed %2$s file(s) (%3$s%%)...', $total, $current, round($current * 100 / $total, 2));
            $result['url'] = $this->getUrl('*/*/runImport/', array('current' => $current));
        }

        if ($current == $total) {
            if($total === 0){
                $result['text'] = $this->__('No files to import');
            }

            $skippedCnt = $this->_getSession()->getSkippedCnt();
            $result['skipped_ids'] = implode(',', array_unique($this->_getSession()->getSkippedIds()));
            $result['skipped_cnt'] = $skippedCnt;
            $result['total_imported'] = $total - $skippedCnt;
            $result['stop'] = true;
            $result['url'] = '';
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function categoriesJsonAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mageworx/downloads_files_edit_tab_attachments')
                ->getCategoryChildrenJson((int)$this->getRequest()->getParam('category'))
        );
    }

    protected function _setProductsFileRelation($productIds, $fileId, $id = false)
    {
        $relation = Mage::getSingleton('downloads/relation');

        if (is_array($productIds) && $productIds) {
            $relation->getResource()->deleteFileProducts($fileId);
            foreach ($productIds as $productId) {
                $relation->setData(array(
                        'file_id' => $fileId,
                        'product_id' => $productId
                    )
                );
                $relation->save();
            }
        } elseif ($id && empty($productIds)) {
            $relation->getResource()->deleteFile($id);
        }
    }

    protected function _moveFile($file, $fileDesc)
    {
        $helper = Mage::helper('downloads');

        $destDir = $helper->getDownloadsPath($file->getId());
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }
        rename($fileDesc['path'], $destDir . DS . $fileDesc['name']);
    }

    protected function _prepareProductIds($data)
    {
        $productIds = array();
        if (isset($data['post_products']) && $data['post_products']) {
            $productIds = explode(',', $data['post_products']);
        }

        return $productIds;
    }

}
