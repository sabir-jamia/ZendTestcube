<?php
namespace Dashboard\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;

class DashUpdates
{

    protected $adapter;

    protected $resultSetPrototype;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
    }
    
    // used to the get updates Data
    public function getdashUpdates()
    {
        $limit = 10;
        
        $sql = new Sql($this->adapter);
        $select = $sql->select()
            ->from(array(
            'activityTabl' => 'activity'
        ))
            ->columns(array(
            'id' => 'id',
            'activity_performed' => 'activity_performed',
            'entity_created' => 'entity_created',
            'created_what' => 'created_what',
            'created_by' => 'created_by',
            'created_when' => 'created_when'
        ))
            ->join(array(
            'usersTabl' => 'users'
        ), 'created_by = usersTabl.id', array(
            'created_by' => 'first_name'
        ))
            ->order('created_when DESC')
            ->limit($limit);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
        $i = 0;
        foreach ($result as $data) {
            $result[$i]['created_when'] = $this->time_elapsed_string($data['created_when'], true);
            $i ++;
        }
        return $result;
    }
    
    // used to the get updates Data
    public function getAlldashUpdates()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select()
            ->from(array(
            'activityTabl' => 'activity'
        ))
            ->columns(array(
            'id' => 'id',
            'activity_performed' => 'activity_performed',
            'entity_created' => 'entity_created',
            'created_what' => 'created_what',
            'created_by' => 'created_by',
            'created_when' => 'created_when'
        ))
            ->join(array(
            'usersTabl' => 'users'
        ), 'created_by = usersTabl.id', array(
            'created_by' => new \Zend\Db\Sql\Expression("CONCAT(usersTabl.first_name,' ',usersTabl.last_name)")
        ))
            ->order('created_when DESC');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute());
        return $result;
    }

    public function getClientUserByEmail($useremail)
    {
        $sql = new Sql($this->adapter);
        $select = $select = $sql->select()
            ->from(array(
                'users' => 'users'
            ))
            ->where(array(
                'email' => $useremail
           ));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute());
        $row = $result->current();
        
        if (! $row) {
            throw new \Exception("Could not found row $username");
        }
        return $row;
    }
    
    public function time_elapsed_string($datetime, $full = false)
    {
        $date = ($datetime instanceof \DateTime ? $datetime : new \DateTime($datetime));
        $diff = date_create('now', $date->getTimezone())->diff($date);
        
        if ($diff->days === 0) {
            $s = ($diff->days * 43200) + ($diff->h * 3600) + ($diff->i * 60) + $diff->s;
            
            if ($s <= 4)
                return 'now';
            if ($s <= 12)
                return 'few seconds ago';
            if ($s <= 59)
                return $s . ' second' . ($diff->s > 1 ? 's' : null) . ' ago';
            if ($s <= 75)
                return 'a minute ago';
            if ($s > 1740 && $s < 1860)
                return 'half hour ago';
            if ($s <= 3600)
                return ($diff->i > 1 ? $diff->i . ' minutes' : 'an hour') . ' ago';
            if ($s > 3600)
                return ($diff->h > 1 ? $diff->h . ' hours' : 'an hour') . ($diff->i > 1 ? ' ' . $diff->i . ' minutes' : null) . ' ago';
        }
        
        if ($diff->days === 1 && $diff->h < 1) {
            $h = ($diff->days * 24) + ($diff->h);
            return $h . ' hour' . ($h > 1 ? 's' : null) . ($diff->i > 1 ? ' ' . $diff->i . ' minutes' : null) . ' ago';
        }
        
        if ($diff->days <= 25)
            return $diff->days . ' day' . ($diff->days > 1 ? 's' : null) . ($diff->h > 1 ? ' ' . $diff->h . ' hours' : ' and hour') . ' ago';
        if ($diff->days <= 45)
            return 'a month ago';
        if ($diff->days <= 345)
            return ($diff->m > 1 ? $diff->m . ' months' : 'an month') . ($diff->d > 1 ? ' ' . $diff->d . ' days' : null) . ' ago';
        if ($diff->days <= 548)
            return 'a year ago';
        if ($diff->days > 548)
            return $diff->y . ' years ago';
        if ($diff->days > 365)
			return $diff->y . ' years ago';
	}

	
}