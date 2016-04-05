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
                'created_by = usersTabl.testcube_id', 
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
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $this->resultSetPrototype->initialize($statement->execute());
        return $resultSet->toArray();
    }

   public function getCategoryById($id = 0)
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

    /**
     * @desc updat and save category
     * 
     * @param Category $category
     * @param unknown $userid
     * @throws \Exception
     */
    
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
        } else {
            $data = array(
                'id' => $category->id,
                'name' => $category->name,
                'updated_by' => $userid,
                'updated_on' => $date,
                'status' => '1'
            );
            $this->tableGateway->update($data, array(
                'id' => $category->id
            ));
        }
    }

    public function deleteCategory($id, $deletedBy)
    {
        $data = array(
            'updated_by' => $deletedBy,
            'status' => '0'
        );
        $this->tableGateway->update($data, array(
            'id' => $id
        ));
    }

    public function deleteAllCategory($ids, $deletedBy)
    {
        $idsArr = explode(",", $ids);
        $n = sizeof($idsArr);
        
        for ($i = 0; $i < $n; $i ++) {
            $this->deleteCategory($idsArr[$i], $deletedBy);
        }
    }
    
    public function fetch($id)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select()
        ->from(array(
            'categoryTabl' => 'category'
        ))
        ->join(array(
            'usersTabl' => 'users'
        ),
            'created_by = usersTabl.testcube_id',
            array(
                'createdBy' => new \Zend\Db\Sql\Expression("CONCAT(usersTabl.first_name,' ',usersTabl.last_name)"),
                'updatedBy' => new \Zend\Db\Sql\Expression("CONCAT(usersTabl.first_name,' ',usersTabl.last_name)")
            ))
            ->where(array(
                'categoryTabl.status' => '1',
                'categoryTabl.id' => $id
            ));    
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $this->resultSetPrototype->initialize($statement->execute())->toArray();
        return $resultSet[0];
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
	    $where = new Where();
	    $where = $where->equalTo('status', '1');
	    $select = $sql->select()
	               ->from('category')
	               ->columns(array(
	                   'count' => new \Zend\Db\Sql\Expression("COUNT(id)")
	               ))
	               ->where($where);
	   $statement = $sql->prepareStatementForSqlObject($select);
	   $result = $this->resultSetPrototype->initialize($statement->execute())
	   ->toArray();
	   return $result;
	}
}