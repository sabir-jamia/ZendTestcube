<?php 
namespace Email\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;


class EmailTemplate 
{
	protected $adapter;
	public $resultSetPrototype;

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet ();
	}

	public function getMailTemplate($forEntity)
	{
		$mailforentity = strtolower($forEntity);
		$sql = new Sql ( $this->adapter );
		$where = new Where();
		$where->equalTo('forentity',$mailforentity);
		$select = $sql->select ()
					  ->from ( array ('emailtemplate' => 'email_template' ) )
					  ->where($where);
		
		$statement = $sql->prepareStatementForSqlObject ( $select );
		$result = $this->resultSetPrototype->initialize ( $statement->execute () )->toArray();
		return $result;
	}
}