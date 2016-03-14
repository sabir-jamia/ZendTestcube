<?php
/*
 * @author : Ashwani Singh
 * @date : 30-09-2013
 *
 */
namespace Category\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class CategoryTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->resultSetPrototype = new ResultSet();
    }

    public function fetchAll($limit = 0, $offset = 0)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select()
            ->from(array(
                'categoryTabl' => 'category'
            ))
            ->join(array(
                'usersTabl' => 'users'
            ),
                'created_by = usersTabl.id', 
                array(
                    'createdBy' => new \Zend\Db\Sql\Expression("CONCAT(usersTabl.first_name,' ',usersTabl.last_name)"),
                    'updatedBy' => new \Zend\Db\Sql\Expression("CONCAT(usersTabl.first_name,' ',usersTabl.last_name)")
            ))
            ->where(array(
                'categoryTabl.status' => '1'
            ))
            ->order('categoryTabl.created_on DESC');
        
        if(!empty($limit)) {
            $select->limit((int)$limit);
        }
        if(!empty($offset)) {
            $select->offset((int)$offset);
        }
        
        //print_r($sql->getSqlStringForSqlObject($select));die;
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $this->resultSetPrototype->initialize($statement->execute());
        return $resultSet->toArray();
    }

    public function getCategoryId($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array(
            'id' => $id
        ));
        $row = $rowset->current();
        
        if (! $row) {
            throw new \Exception("Could not found row $id");
        }
        return $row;
    }

    public function saveCategory(Category $category, $userid)
    {
        $date = date('Y-m-d H:i:s');
        if (empty($category->id)) {
            $data = array(
                'id' => $category->id,
                'name' => $category->name,
                'created_by' => $userid,
                'created_on' => $date,
                'status' => '1'
            );
            $this->tableGateway->insert($data);
        } elseif (!empty($this->getCategoryId($id))) {
            $data = array(
                'id' => $category->id,
                'name' => $category->name,
                'updated_by' => $userid,
                'updated_on' => $date,
                'status' => '1'
            );
            $this->tableGateway->update($data, array(
                'id' => $id
            ));
        } else {
            throw new \Exception('Category id doesnt exists');
        }
    }

    public function deleteCategory($id, $deletedBy)
    {
        $one = 1;
        $data = array(
            'updated_by' => $deletedBy,
            'status' => $one
        );
        $this->tableGateway->update($data, array(
            'id' => $id
        ));
    }

    public function deleteallCategory($id, $deletedBy)
    {
        $one = 1;
        $data = array(
            'updated_by' => $deletedBy,
            'status' => $one
        );
        
        $id = explode(",", $id);
        // print_r($id);
        
        $n = sizeof($id);
        // print_r($id);
        for ($i = 0; $i < $n; $i ++)
            $this->tableGateway->update($data, array(
                'id' => $id
            ));
    }

    public function isCategoryExists($categoryId, $categoryName)
    {
        $where = new Where();
        if (!empty($categoryId)) {
            $where->equalTo('name', $categoryName)
                ->equalTo('status', '1')
                ->notEqualTo('id', $categoryId);
        } else {
            $where->equalTo('name', $categoryName)
                ->equalTo('status', '1');     
        }
        
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select()
            ->from(array(
                'categoryTable' => 'category'
            ))->columns(array(
                'id' => 'id',
                'name' => 'name'
            ))
            ->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
        
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
	}
	
	public function countRow() 
	{
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $select = $sql->select()
	               ->from('category')
	               ->columns(array(
	                   'count' => new \Zend\Db\Sql\Expression("COUNT(id)")
	               ));
	   $statement = $sql->prepareStatementForSqlObject($select);
	   $result = $this->resultSetPrototype->initialize($statement->execute())
	   ->toArray();
	   return $result;
	}
}