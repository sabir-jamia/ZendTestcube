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
    
    public function fetchAction()
    {
        $limit = $this->params()->fromQuery('limit', 0);
        $offset = $this->params()->fromQuery('offset', 0);
        $categoryTable = $this->serviceLocator->get('CategoryFactory');
        return new JsonModel($categoryTable->fetchAll($limit, $offset));
    }
    
    public function addCategoryAction()
    {
        $request = $this->getRequest();
        $jsonModel = new JsonModel();
        
        if ($request->isXmlHttpRequest()) {
            $categoryForm = new CategoryForm();
            $categoryTable = $this->serviceLocator->get('CategoryFactory');
            
            if($request->getMethod() == 'GET') {
                $id = (int) $this->params()->fromQuery('id', 0);
                $viewmodel = new ViewModel();
                $headerLabel = "Add new category";
                
                if(!empty($id)) {//Edit category form
                    $categoryName = $categoryTable->getCategoryById($id)->name;
                    $categoryForm->get('id')->setValue($id);
                    $categoryForm->get('name')->setValue($categoryName);
                    $categoryForm->get('submit-category')->setLabel("Edit");
                    $headerLabel = "Edit category";
                }
                $viewmodel->setTerminal(true)
                ->setTemplate('category/add-category.phtml')
                ->setVariables(array(
                    'form' => $categoryForm,
                    'headerLabel' => $headerLabel
                ));
                $htmlOutput = $this->getServiceLocator()
                ->get('viewrenderer')
                ->render($viewmodel);
                $jsonModel->setVariables(array(
                    'html' => $htmlOutput
                ));
            } else {
                $category = new Category();
                $categoryForm->setInputFilter($category->getInputFilter());
                $categoryForm->setData($request->getPost());
                
                if ($categoryForm->isValid()) {
                    $userSession = new Container('users');
                    $userId = $userSession->clientId;
                    $category->exchangeArray($categoryForm->getData());
                    $arrData = $categoryForm->getData();
                    $categoryId = $arrData['id'];
                    $categoryName = preg_replace('/\s/', '', $arrData['name']); 
                
                    if (ctype_punct($categoryName)) {
                        $jsonModel->setVariable('status', 2);
                    } else {
                        $categoryTable->saveCategory($category, $userId);
                        $jsonModel->setVariable('status', 1);
                    }
                }
            }
            return $jsonModel;
        }
    }

    public function deleteCategoryAction()
    {
        $request = $this->getRequest();
        $jsonModel = new JsonModel();
        
        if ($request->isXmlHttpRequest()) {
            
            if ($request->isGet()) {
                $id = (int) $this->params()->fromQuery('id', 0);
                $htmlViewPart = new ViewModel();
                $htmlViewPart->setTerminal(true)
                ->setTemplate('category/delete-category.phtml')
                ->setVariables(array(
                    'id' => $id,
                    'message' => 'All the questions of this Category will also be deleted'
                ));
                $htmlOutput = $this->getServiceLocator()
                    ->get('viewrenderer')
                    ->render($htmlViewPart);
                $jsonModel->setVariables(array(
                    'html' => $htmlOutput
                ));
            } elseif ($request->isPost()) {
                $id = (int) $this->params()->fromPost('id', 0);
                $userSession = new Container('users');
                $userid = $userSession->clientId;
                    // $categoryTable = $this->serviceLocator->get('CategoryFactory');
                    // $categoryTable->deleteCategory($id, $userid);
                    // $this->getQuestionTable()->deleteQuestionUsingCatId($id, $userid);
                $jsonModel->setVariable('status', 1);
            }
            return $jsonModel;
        }
    }
    
    public function deleteSelectedAction()
    {
        $request = $this->getRequest();
        $jsonModel = new JsonModel();
        
        if ($request->isGet()) {
            $ids = $this->params()->fromQuery('ids', 0);
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true)
            ->setTemplate('category/delete-category.phtml')
            ->setVariables(array(
                'id' => $ids,
                'message' => 'All the questions of these categories will also be deleted'
            ));
            $htmlOutput = $this->getServiceLocator()
            ->get('viewrenderer')
            ->render($viewModel);
            $jsonModel->setVariables(array(
                'html' => $htmlOutput
            ));
        } elseif($request->isPost()) {
            $categoryIds = $this->params()->fromPost('ids');
            $userSession = new Container('users');
            $userId = $userSession->clientId;
            $categoryTable = $this->serviceLocator->get('CategoryFactory');
            $categoryTable->deleteAllCategory($categoryIds, $userId);
            //$this->getQuestionTable()->deleteAllQuestionUsingCatId($catid, $userid);
            $jsonModel->setVariable('status', 1);
        }
        return $jsonModel;
    }

    public function viewCategoryAction()
    {
        $id = $this->params()->fromRoute('id');
        $categoryTable = $this->serviceLocator->get('CategoryFactory');
        $rowCategory = $categoryTable->fetch($id);
        $viewModel = new ViewModel();
        $viewModel->setVariable('rowCategory', $rowCategory);
        return $viewModel;
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

	public function getQuestionTable() 
	{
	    if (! $this->questionTable) {
            $serviceManager = $this->getServiceLocator();
            $this->questionTable = $serviceManager->get('Question\Model\QuestionTable');
		}
		return $this->questionTable;
	}
}