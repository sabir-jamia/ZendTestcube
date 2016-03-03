<?php
/*
*@author : Ashwani Singh
*@date : 30-09-2013
*
*/

namespace Category\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class CategoryTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
		$this->resultSetPrototype = new ResultSet ();
	}
	public function fetchAll() {
		/* $resultSet = $this->tableGateway->select(array('status'=>'0')); */
		$status = 0;
		$sql = new Sql ( $this->tableGateway->getAdapter () );
		$select = $sql->select ()
					  ->from ( array ('categoryTabl' => 'category' ) )
					  ->join ( array ('usersTabl' => 'users' ), 'created_by = usersTabl.id', 
					  		   array ('createdBy' => new \Zend\Db\Sql\Expression 
					  		   		 ("CONCAT(usersTabl.first_name,' ',usersTabl.last_name)" ),
									  'updatedBy' => new \Zend\Db\Sql\Expression 
					  		   		 ("CONCAT(usersTabl.first_name,' ',usersTabl.last_name)" ) ) )
					 ->where ( array ('categoryTabl.status' => $status ) )
					 ->order ( 'categoryTabl.created_on DESC' );
		$statement = $sql->prepareStatementForSqlObject ( $select );
		$resultSet = $this->resultSetPrototype->initialize ( $statement->execute () );
		
		return $resultSet;
	}
	public function getCategory($id) {
		$id = ( int ) $id;
		$rowset = $this->tableGateway->select ( array (
				'id' => $id 
		) );
		$row = $rowset->current ();
		
		if (! $row) {
			throw new \Exception ( "Could not found row $id" );
		}
		return $row;
	}
	public function saveCategory(Category $category, $userid) {
		$date = date ( 'Y-m-d H:i:s' );
		$zero = 0;
		$id = $category->id;
		
		$id = ( int ) $category->id;
		if ($id == 0) {
			$data = array (
					'id' => $category->id,
					'name' => $category->name,
					'created_by' => $userid,
					'created_on' => $date,
					'status' => $zero 
			);
			$this->tableGateway->insert ( $data );
		} else {
			if ($this->getCategory ( $id )) {
				$data = array (
						'id' => $category->id,
						'name' => $category->name,
						'updated_by' => $userid,
						'updated_on' => $date,
						'status' => $zero 
				);
				$this->tableGateway->update ( $data, array (
						'id' => $id 
				) );
			} else {
				throw new \Exception ( 'Category id doesnt exists' );
			}
		}
	}
	public function deleteCategory($id,$deletedBy) {
		
		$one = 1;
		$data = array (
				'updated_by' => $deletedBy,
				'status' => $one 
		);
		$this->tableGateway->update ( $data, array (
				'id' => $id 
		) );
	}
	public function deleteallCategory($id,$deletedBy) {
		$one = 1;
		$data = array (
				'updated_by' => $deletedBy,
				'status' => $one 
		);
		
		$id = explode ( ",", $id );
		// print_r($id);
		
		$n = sizeof ( $id );
		// print_r($id);
		for($i = 0; $i < $n; $i ++)
			$this->tableGateway->update ( $data, array (
					'id' => $id 
			) );
	}
	
	public function checkVal($chkId, $txtVal) {
		$zero = 0;
		$where = new Where ();
		// $where(array('name'=>$txtVal,'status'=>$zero));
		if ($chkId) {
			$where->equalTo ( 'name', $txtVal )->equalTo ( 'status', $zero )->notEqualTo ( 'id', $chkId );
		} else {
			$where->equalTo ( 'name', $txtVal )->equalTo ( 'status', $zero );
		}
		
		$sql = new Sql ( $this->tableGateway->getAdapter () );
		$select = $sql->select ()
					  ->from ( array ('categoryTabl' => 'category' ) )
					  ->columns ( array ('id' => 'id','name' => 'name' ) )
					  ->where ( $where );
		$statement = $sql->prepareStatementForSqlObject ( $select );
		$result = $this->resultSetPrototype->initialize ( $statement->execute () )->toArray ();
		
		if (empty ( $result )) {
			return true;
		} else {
			return false;
		}
		/*
		 * $zero = 0; if($chkId){ $rowset = $this->tableGateway->select(array('id'=>$chkId,'name'=>$txtVal,'status'=>$zero)); } else{ $rowset = $this->tableGateway->select(array('id'=>$chkId,'name'=>$txtVal,'status'=>$zero)); } $row = $rowset->current(); if($row) { return true; } else { return false; }
		 */
	}
}

