<?php
/*
 * @author : Sabir
 * @desc : Recent Details for Dashboard module
 * @created on : 18-07-2014
 * ---------------------------------------------
 * @modified on :
 *
 *
 *
 */
namespace Dashboard\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class RecentDetails
{

    protected $table = 'assigned_questions';

    protected $adapter;

    public $resultSetPrototype;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
    }

    public function fetchRecentTestDetails()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('test')
            ->columns(array(
            'name' => 'name',
            'id' => 'id'
        ))
            ->where('status = 0')
            ->order("id DESC")
            ->limit(3);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
        return $result;
    }

    public function fetchRecentLinkDetails()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('link_assign_dates')
            ->columns(array(
            'linkId' => 'link_id',
            'linkCode' => 'link_code',
            'showUntil' => 'showuntill'
        ))
            ->order("id DESC")
            ->limit(3);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
        
        return $result;
    }

    public function fetchRecentResultDetails()
    {
        $where = new Where();
        $sql = new Sql($this->adapter);
        
        $select = $sql->select()
            ->from(array(
            't' => 'test'
        ))
            ->columns(array(
            'testname' => 'name',
            'testid' => 'id'
        ))
            ->join(array(
            'l' => 'link'
        ), 't.id = l.test_id', array(
            'linkName' => 'name'
        ))
            ->join(array(
            'lad' => 'link_assign_dates'
        ), 'lad.link_id = l.id', array(
            'linkCode' => 'link_code'
        ))
            ->join(array(
            'r' => 'result'
        ), ' lad.id = r.link_assign_dates_id')
            ->join(array(
            's' => 'student'
        ), 's.id = r.student_id', array(
            'stuFname' => 'fname',
            'stuLname' => 'lname',
            'stuEmail' => 'email'
        ))
            ->where(array(
            'r.status' => '0'
        ))
            ->order("r.date_finished DESC")
            ->limit(3);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
        
        return $result;
    }

    public function fetchCounts()
    {
        $countRowQuery = "SELECT
							(select count(*) from category where status='0') as totalCategory,
							(select count(*) from questions where status='0') as totalQuestion,
							(select count(*) from test where status='0') as totalTest,
        					(select count(*) from certificate where status='0') as totalCertificate,
							(select count(*)-1 from users where status='0') as totalUsers";
        
        $countData = $this->adapter->query($countRowQuery, Adapter::QUERY_MODE_EXECUTE)->toArray();
        ;
        return $countData;
    }
}