<?php

namespace Category\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Category\Model\Category;
use Question\Model\Question;
use Category\Form\CategoryForm;
use Zend\Json\Json;
use Zend\Session\Container;

class CategoryController extends AbstractActionController {
	protected $categoryTable;
	protected $questionTable;
	public function indexAction() {
		return new ViewModel ( array (
				'categories' => $this->getCategoryTable ()->fetchAll () 
		) );
	}
	public function addAction() {
		
		

		$catform = new CategoryForm ();
		$request = $this->getRequest (); // getting current request object
		if ($request->isPost ()) {
			$category = new Category ();
			$catform->setInputFilter ( $category->getInputFilter () );
			$catform->setData ( $request->getPost () ); // setting requested data to form object
			$response = $this->getResponse ();
			
			if ($catform->isValid ()) {
				
				$userSession = new Container ( 'users' );
				$userid = $userSession->id;
				$category->exchangeArray ( $catform->getData () );
				
				$arr = $catform->getData ();
				$chkCatid = $arr['id'];
				$categoryname = $arr['name'];
				$foo = preg_replace('/\s/', '', $categoryname);
				
				if(ctype_punct($foo))
				{
					
					$response->setContent ( Json::encode ( array (
							'status' => 3 
					) ) );
					return $response;

				}

				/*$nameTesting = $arr['catNameForTesting'];

				echo $nameTesting;
				die();
*/





				if ($this->checkval ( $chkCatid, $categoryname )) {
					
					$this->getCategoryTable ()->saveCategory ( $category, $userid );
					$response->setContent ( Json::encode ( array (
							'status' => 0 
					) ) );
					return $response;
				} 

				else {
					$response->setContent ( Json::encode ( array (
							'status' => 2 
					) ) );
					return $response;
				}
				
				// Redirect to list of albums
			}
			
			$response->setContent ( Json::encode ( array (
					'status' => 1 
			) ) );
			return $response;
		}
		
		$viewmodel = new ViewModel ( array (
				'form' => $catform 
		) );
		$viewmodel->setTerminal ( true );
		return $viewmodel;
	}
	public function editAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			$id = $this->getRequest ()->getPost ( 'id', null );
			$name = $this->getRequest ()->getPost ( 'name', null );
		} 

		else {
			
			$id = $this->params ()->fromQuery ( 'catid' );
		}
		
		if (! $id) {
			return $this->redirect ()->toRoute ( 'category', array (
					'action' => 'add' 
			) );
		}
		
		// Get the Album with the specified id. An exception is thrown
		// if it cannot be found, in which case go to the index page.
		try {
			$category = $this->getCategoryTable ()->getCategory ( $id );
		} catch ( \Exception $ex ) {
			return $this->redirect ()->toRoute ( 'category', array (
					'action' => 'index' 
			) );
		}
		
		$form = new CategoryForm ();
		$form->bind ( $category );
		
		$form->get ( 'submit' )->setAttribute ( 'value', 'Edit' );
		$response = $this->getResponse ();
		
		if ($request->isPost ()) {
			
			$form->setInputFilter ( $category->getInputFilter () );
			$form->setData ( $request->getPost () );
			
			if ($form->isValid ()) {
				$userSession = new Container ( 'users' );
				$userid = $userSession->id;
				
				$chkCatid = $this->getRequest ()->getPost ( 'id', null );
				$categoryname = $this->getRequest ()->getPost ( 'name', null );
				
				if ($this->checkVal ($chkCatid, $categoryname )) {
					
					$this->getCategoryTable ()->saveCategory ( $category, $userid );
					$response->setContent ( Json::encode ( array (
							'status' => 0 
					) ) );
					return $response;
				} else {
					
					$response->setContent ( Json::encode ( array (
							'status' => 1 
					) ) );
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
		) );
		$viewmodel->setTerminal ( true );
		return $viewmodel;
	}
	public function listAction() {
		return new ViewModel ( array (
				'categories' => $this->getCategoryTable ()->fetchAll () 
		) );
	}
	public function listDetailsAction() {
		$request = $this->getRequest ();
		$catid = $this->params ()->fromQuery ( 'catid' );
		// echo $catid;
		// die;
		
		// $catid = $request->getPost('catid');
		
		$viewmodel = new ViewModel ( array (
				'categories' => $this->getCategoryTable ()->getCategory ( $catid ) 
		) );
		// var_dump($viewmodel);
		// die;
		$viewmodel->setTerminal ( true );
		return $viewmodel;
	}
	public function checkVal($chkCatid, $name) {
		$txtVal = $name;
		
		$checkVal = $this->getCategoryTable ()->checkVal ($chkCatid, $txtVal );
		if ( $checkVal) {
			$response = $this->getresponse ();
			return true;
		} else {
			$response = $this->getResponse ();
			return false;
		}
	}
	public function deleteAction() {
		$viewmodel = new ViewModel ();
		$viewmodel->setTerminal ( true );
		return $viewmodel;
	}
	public function deleteallAction() {
		$viewmodel = new ViewModel ();
		$viewmodel->setTerminal ( true );
		return $viewmodel;
	}
	public function deleteSelectedAction() {

		$request = $this->getRequest ();
		$catid = $this->params ()->fromQuery ( 'id' );
		$userSession = new Container ( 'users' );
		$userid = $userSession->id;
		$this->getCategoryTable ()->deleteallCategory ( $catid,$userid);
		$this->getQuestionTable ()->deleteAllQuestionUsingCatId ( $catid,$userid);
		$viewmodel = new ViewModel ();
		$viewmodel->setTerminal ( true );
		return $viewmodel;
	}
	public function getCategoryTable() {
		if (! $this->categoryTable) {
			
			$serviceManager = $this->getServiceLocator ();
			
			$this->categoryTable = $serviceManager->get ( 'Category\Model\CategoryTable' );
		}
		return $this->categoryTable;
	}
	
	/* deleting category from database */
	public function deleteCategoryAction() {
		$request = $this->getRequest ();

		
		if ($request->isPost ()) {
			$id = ( int ) $request->getPost ( 'id' );
			$userSession = new Container ( 'users' );
			$userid = $userSession->id;
			$this->getCategoryTable ()->deleteCategory ( $id,$userid);
			$this->getQuestionTable ()->deleteQuestionUsingCatId ( $id,$userid);
			$response = $this->getResponse ();
			$response->setContent(
                Json::encode(array(
                    'status'=> 0 )));
            return $response;


         }
    }

		public function getQuestionTable() {
				if (! $this->questionTable) {
					$serviceManager = $this->getServiceLocator ();
					$this->questionTable = $serviceManager->get ( 'Question\Model\QuestionTable' );
				}
				return $this->questionTable;
		}

}


