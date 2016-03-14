<?php
namespace Category\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Question\Model\Question;
use Category\Form\CategoryForm;
use Category\Model\Category;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;

class CategoryController extends AbstractActionController
{
    
    protected $questionTable;

    public function indexAction()
    {
	    $categoryTable = $this->serviceLocator->get('CategoryFactory');
	    $countArr = $categoryTable->countRow();
	    $defaultSelect = 1;
	    $selectedPage = 1;
	    $rowCount = $countArr[0]['count'];
        return new ViewModel(array(
            'categories' => $categoryTable->fetchAll($defaultSelect),
            'rowCount' => $rowCount,
            'defaultSelect' => $defaultSelect,
            'selectedPage' => $selectedPage
        ));
    }

    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $categoryForm = new CategoryForm();
            if($request->getMethod() == 'GET') {
                $viewmodel = new ViewModel(array(
                    'form' => $categoryForm
                ));
                $viewmodel->setTerminal(true);
                return $viewmodel;
            } else {
                $category = new Category();
                $categoryForm->setInputFilter($category->getInputFilter());
                $categoryForm->setData($request->getPost());
                $jsonModel = new JsonModel();
           
                if ($categoryForm->isValid()) {
                    $userSession = new Container('users');
                    $userId = $userSession->clientId;
                    $category->exchangeArray($categoryForm->getData());
                    $arrData = $categoryForm->getData();
                    $categoryId = $arrData['id'];
                    $categoryName = preg_replace('/\s/', '', $arrData['name']); 
                    
                    if (ctype_punct($categoryName)) {
                        $jsonModel->setVariable('status', 3);
                    } elseif (!$this->isCategoryExists($categoryId, $categoryName)) {
                        $categoryTable = $this->serviceLocator->get('CategoryFactory');
                        $categoryTable->saveCategory($category, $userId);
                        $jsonModel->setVariable('status', 0);
                    } else {
                        $jsonModel->setVariable('status', 2);
                    }
                }
                return $jsonModel;
            }
        }
    }

    public function editAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $id = $this->getRequest()->getPost('id', null);
            $name = $this->getRequest()->getPost('name', null);
        } else {    
            $id = $this->params()->fromQuery('catid');
        }
        
        if (! $id) {
            return $this->redirect()->toRoute('category', array(
                'action' => 'add'
            ));
        }
        // Get the Album with the specified id. An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $category = $this->getCategoryTable()->getCategory($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('category', array(
                'action' => 'index'
            ));
        }
        
        $form = new CategoryForm();
        $form->bind($category);
        $form->get('submit')->setAttribute('value', 'Edit');
        $response = $this->getResponse();
        
        if ($request->isPost()) {   
            $form->setInputFilter($category->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $userSession = new Container('users');
                $userid = $userSession->id;
                $chkCatid = $this->getRequest()->getPost('id', null);
                $categoryname = $this->getRequest()->getPost('name', null);
                
                if ($this->isCategoryExists($chkCatid, $categoryname)) {     
                    $this->getCategoryTable()->saveCategory($category, $userid);
                    $response->setContent(Json::encode(array(
                        'status' => 0
                    )));
                    return $response;
                } else {   
                    $response->setContent(Json::encode(array(
                        'status' => 1
                    )));
                    return $response;
                }
                // $this->getCategoryTable()->saveCategory($category,$userid);
                // Redirect to list of category
            } else {
                return 0;
            }
        }
        $viewmodel = new ViewModel ();
		$viewmodel->setVariables ( array (
		    'id' => $id,
			'form' => $form 
		));
		$viewmodel->setTerminal ( true );
		return $viewmodel;
	}
	
	public function listAction() 
	{
	    $categoryTable = $this->serviceLocator->get('CategoryFactory');
        return new ViewModel(array(
            'categories' => $categoryTable->fetchAll()
        ));
    }
    
	public function listDetailsAction() 
	{
		$request = $this->getRequest ();
		$categoryTable = $this->serviceLocator->get('CategoryFactory');
        $catid = $this->params ()->fromQuery ( 'catid' );
		$viewmodel = new ViewModel (array(
		    'categories' => $categoryTable->getCategory($catid) 
		));
		$viewmodel->setTerminal ( true );
		return $viewmodel;
	}
	
	public function isCategoryExists($categoryId, $categoryName) 
	{
        $categoryTable = $this->serviceLocator->get('CategoryFactory');
        return $categoryTable->isCategoryExists($categoryId, $categoryName);
    }
    
	public function deleteAction() 
	{
        $viewmodel = new ViewModel();
        $viewmodel->setTerminal(true);
        return $viewmodel;
    }
    
	public function deleteallAction()
	{
        $viewmodel = new ViewModel();
        $viewmodel->setTerminal(true);
        return $viewmodel;
    }
    
	public function deleteSelectedAction() 
	{
        $request = $this->getRequest();
        $catid = $this->params()->fromQuery('id');
        $userSession = new Container('users');
        $userid = $userSession->id;
        $this->getCategoryTable()->deleteallCategory($catid, $userid);
        $this->getQuestionTable()->deleteAllQuestionUsingCatId($catid, $userid);
        $viewmodel = new ViewModel();
        $viewmodel->setTerminal(true);
        return $viewmodel;
    }
	
	/*deleting category from database */
	public function deleteCategoryAction()
	{
		$request = $this->getRequest ();
		if ($request->isPost ()) {
            $id = (int) $request->getPost('id');
            $userSession = new Container('users');
            $userid = $userSession->id;
            $categoryTable = $this->serviceLocator->get('Category\Model\CategoryTable');
            $categoryTable->deleteCategory($id, $userid);
            $this->getQuestionTable()->deleteQuestionUsingCatId($id, $userid);
            $response = $this->getResponse();
            $response->setContent(Json::encode(array(
                'status' => 0
            )));
            return $response;
        }
    }
    
    public function fetchAction()
    {
        $limit = $this->params()->fromQuery('limit', 0);
        $offset = $this->params()->fromQuery('offset', 0);
        $categoryTable = $this->serviceLocator->get('CategoryFactory');
        return new JsonModel($categoryTable->fetchAll($limit, $offset));
    }

	public function getQuestionTable() 
	{
	    if (! $this->questionTable) {
            $serviceManager = $this->getServiceLocator();
            $this->questionTable = $serviceManager->get('Question\Model\QuestionTable');
		}
		return $this->questionTable;
	}
}