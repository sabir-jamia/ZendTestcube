<?php
/*
 *@author : Divesh Pahuja
 *@date : 03-07-2014  
 *
 */
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;

class ApplicationTable
{
	protected $tableGateway;
	protected $dbAdapter12;
	
	public function __construct(TableGateway $tableGateway)  
	{
		$this->tableGateway = $tableGateway;
		$dbAdapterConfig = array(
				'driver'         =>  'Pdo',
				'dsn'            =>  'mysql:dbname=testcubedb;host=localhost',
				'username' => 'root', //'client0'.$this->clientId,
				'password' =>'tolexo' , //'client0'.$this->clientId,
		);
		
		$this->dbAdapter12 = new Adapter($dbAdapterConfig);
		
		
	}
	
	public function fetchCounts()
	{
		$dbAdapter = $this->tableGateway->getAdapter();
		$countRowQuery = "SELECT
							(select count(*) from category where status='0') as totalCategory,
							(select count(*) from questions where status='0') as totalQuestion,
							(select count(*) from test where status='0') as totalTest,
							(select count(*) from result) as totalResult,
        					(select count(*) from certificate where status='0') as totalCertificate,
							(select count(*)-1 from users where status='0') as totalUsers";
        
        $countData = $dbAdapter->query($countRowQuery, Adapter::QUERY_MODE_EXECUTE)->toArray();;
        return $countData;
	}

	public function fetchtheme($userid)
	{
		$adapter1 = $this->dbAdapter12;
		$themeQuery = "SELECT (select theme from user_profile where user_id = $userid) as themeselected";
		$themeData = $adapter1->query($themeQuery, Adapter::QUERY_MODE_EXECUTE)->toArray();
		return $themeData;
	} 
}