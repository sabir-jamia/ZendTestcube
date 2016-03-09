<?php
namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use User\Model\User;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class UserTable
{

    protected $tableGateway;

    protected $resultSetPrototype;

    private $_dbConfig;

    private $_adapter;

    private $_connection;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->_adapter = $this->tableGateway->getAdapter();
        $this->_connection = $this->_adapter->getDriver()->getConnection();
        $this->resultSetPrototype = new ResultSet();
    }

    public function setDbCredentails($dbConfig)
    {
        $this->_dbConfig = $dbConfig;
    }

    public function userlist()
    {
        $resultSet = $this->tableGateway->select(array(
            'status' => '1'
        ));
        return $resultSet;
    }

    public function saveUser(User $register)
    {
        $this->_connection->beginTransaction();
        $data = array(
            'username' => $register->username,
            'email' => $register->email,
            'password' => md5($register->password),
            'registration_date' => $register->registration_date
        );
        try {
            $this->tableGateway->insert($data);
            $client_id = $this->tableGateway->lastInsertValue;
            $id = $client_id;
            $data = array(
                'client_id' => $client_id
            );
            $this->tableGateway->update($data, array(
                'id' => $id
            ));
            
            $first_name = ucfirst(strtolower($register->firstname));
            $last_name = isset($register->lastname) ? ucfirst(strtolower($register->lastname)) : NULL;
            $userProfileData = array(
                'user_id' => $id,
                'first_name' => $first_name,
                'last_name' => $last_name
            );
            $sql = new Sql($this->_adapter);
            $select = $sql->insert('user_profile')->values($userProfileData);
            $statement = $sql->prepareStatementForSqlObject($select);
            $statement->execute();
            
            $stmt = "CREATE SCHEMA IF NOT EXISTS `clientdb0" . $client_id . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;";
            $stmt .= "use `clientdb0" . $client_id . "` ;";
            $stmt .= "CREATE USER 'client0" . $client_id . "'@'localhost' IDENTIFIED BY 'client0" . $client_id . "';";
            $stmt .= "GRANT SELECT, INSERT, UPDATE, DELETE ON clientdb0" . $client_id . ".* TO 'client0" . $client_id . "'@'localhost';";
            $query = $this->_adapter->query($stmt);
            $result = $query->execute()
                ->getResource()
                ->closeCursor();
            
            $dbUser = $this->_dbConfig['username'];
            $dbPass = $this->_dbConfig['password'];
            $sqlConn = "mysql -u$dbUser -p$dbPass -hlocalhost clientdb0" . $client_id . " < " . dirname(__FILE__) . "/clientdb.sql";
            exec($sqlConn);
            
            $newdata = array(
                'testcube_id' => $id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $register->email,
                'created_on' => $register->registration_date
            );
            $this->tableGateway->insert($newdata);
            $query = $this->_adapter->query("use testcubedb;");
            $result = $query->execute()
                ->getResource()
                ->closeCursor();
            $this->_connection->commit();
            return $client_id;
        } catch (Exception $e) {
            $this->_connection->rollback();
        }
    }

    public function updatestatus($id)
    {
        $data = array(
            'status' => '1'
        );
        if ($this->getUserById($id)) {
            $this->tableGateway->update($data, array(
                'id' => $id
            ));
            return 1;
        } else {
            return 0;
        }
    }

    public function getUserById($id)
    {
        $rowset = $this->tableGateway->select(array(
            'id' => $id,
            'status' => '0'
        ));
        $row = $rowset->current();
        
        if (! $row) {
            /* throw new \Exception("Could not found row $username"); */
        }
        return $row;
    }

    public function getUserByUserName($username, $usernameType)
    {
        $rowset = $this->tableGateway->select(array(
            $usernameType => $username,
            'status' => '1'
        ));
        $row = $rowset->current();
        
        if (! $row) {
            return 'notconfirmed';
            // throw new \Exception("Could not found row $username");
        }
        return $row;
    }

    public function getPasswordByEmail($email)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $length = strlen($characters);
        $string = '';
        for ($i = 0; $i < $length - 30; $i ++) {
            $string .= $characters[rand(0, $length - 1)];
        }
        
        $sql = new Sql($this->tableGateway->getAdapter());
        
        $update = $sql->update();
        $update->table('users');
        $update->set(array(
            'password' => md5($string)
        ));
        $update->where(array(
            'email' => $email
        ));
        
        // echo $update->getSqlString();
        
        $statement = $sql->prepareStatementForSqlObject($update);
        try {
            $result = $statement->execute(); // works fine
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        }
        // die();
        return $string;
    }

    public function isEmailexist($txtVal)
    {
        $rowset = $this->tableGateway->select(array(
            'email' => $txtVal
        ));
        $row = $rowset->current();
        
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function checkUserExists($user)
    {
        $pos = strpos($user, '@');
        if ($pos === false) {
            $rowset = $this->tableGateway->select(array(
                'username' => $user
            ));
            $row = $rowset->current();
        } else {
            $rowset = $this->tableGateway->select(array(
                'email' => $user
            ));
            $row = $rowset->current();
        }
        
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function userProfile($userid)
    {
        $user_id = (int) $userid;
        
        $where = new Where();
        $where->equalTo('usersTabl.id', $user_id);
        
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select()
            ->from(array(
            'usersTabl' => 'users'
        ))
            ->columns(array(
            'clientId' => 'client_id',
            'email' => 'email',
            'username' => 'username',
            'regDate' => 'registration_date'
        ))
            ->join(array(
            'userProfileTabl' => 'user_profile'
        ), 'usersTabl.client_id = userProfileTabl.user_id', array(
            'firstName' => 'first_name',
            'lastName' => 'last_name',
            'photo' => 'photo',
            'contact' => 'contact'
        ))
            ->where($where);
        
        // echo $select->getSqlString();
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
        // \Zend\Debug\Debug::dump($result);
        return $result;
    }

    public function clientGeneralProfileUpdate($profileData)
    {
        $data = array(
            'profileFirstName' => $profileData['profileFirstName'],
            'profileLastName' => $profileData['profileLastName']
        );
        
        $sql = new Sql($this->tableGateway->getAdapter());
        
        $update = $sql->update();
        $update->table('users');
        $update->set(array(
            'first_name' => $profileData['profileFirstName'],
            'last_name' => $profileData['profileLastName']
        ));
        $update->where(array(
            'testcube_id' => $profileData['clientId']
        ));
        // echo $update->getSqlString();
        $statement = $sql->prepareStatementForSqlObject($update);
        try {
            $result = $statement->execute(); // works fine
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        }
        
        return $result;
    }

    public function superGeneralProfileUpdate($profileData)
    {
        $data = array(
            'profileFirstName' => $profileData['profileFirstName'],
            'profileLastName' => $profileData['profileLastName'],
            'profileContact' => $profileData['profileContact'],
            'profilePic' => $profileData['profilePic'],
            'random' => $profileData['random']
        );
        
        $sql = new Sql($this->tableGateway->getAdapter());
        
        $update = $sql->update();
        $update->table('user_profile');
        
        if ($data['random'] == $data['profilePic']) {
            $update->set(array(
                'first_name' => $profileData['profileFirstName'],
                'last_name' => $profileData['profileLastName'],
                'contact' => $profileData['profileContact']
            ));
        } else {
            $update->set(array(
                'first_name' => $profileData['profileFirstName'],
                'last_name' => $profileData['profileLastName'],
                'contact' => $profileData['profileContact'],
                'photo' => $profileData['profilePic']
            ));
        }
        
        $update->where(array(
            'user_id' => $profileData['clientId']
        ));
        // echo $update->getSqlString();
        
        $statement = $sql->prepareStatementForSqlObject($update);
        
        try {
            $result = $statement->execute(); // works fine
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        }
        
        return $result;
    }

    public function updateProfilePassword($passwordData)
    {
        $data = array(
            'clientId' => $passwordData['clientId'],
            'newPassword' => $passwordData['newPassword']
        );
        
        $sql = new Sql($this->tableGateway->getAdapter());
        
        $update = $sql->update();
        $update->table('users');
        $update->set(array(
            'password' => md5($passwordData['newPassword'])
        ));
        $update->where(array(
            'client_id' => $passwordData['clientId']
        ));
        // echo $update->getSqlString();
        
        $statement = $sql->prepareStatementForSqlObject($update);
        try {
            $result = $statement->execute(); // works fine
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        }
        
        return $result;
    }

    public function clientGeneralProfileSettings($profileData)
    {
        $data = array(
            'clientId' => $profileData['clientId'],
            'selectedLanguage' => $profileData['selectedLanguage'],
            'themeColor' => $profileData['themeColor']
        );
        
        $sql = new Sql($this->tableGateway->getAdapter());
        
        $update = $sql->update();
        $update->table('user_profile');
        $update->set(array(
            'language' => $profileData['selectedLanguage']
        ));
        $update->set(array(
            'theme' => $profileData['themeColor']
        ));
        $update->where(array(
            'user_id' => $profileData['clientId']
        ));
        // echo $update->getSqlString();
        
        $statement = $sql->prepareStatementForSqlObject($update);
        try {
            $result = $statement->execute(); // works fine
            $res = 1;
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
            $res = 0;
        }
        
        return $res;
    }
    
    /*
     * public function clientGeneralProfileSettings($profileData){
     * $data = array(
     * 'clientId' => $profileData['clientId'],
     * 'selectedLanguage' => $profileData['selectedLanguage'],
     * 'themeColor' => $profileData['themeColor']
     * );
     * $theme = $profileData['themeColor'];
     * $language = $profileData['selectedLanguage'];
     * $clientId = $profileData['clientId'];
     *
     *
     *
     * $adapter1 = $this->dbAdapter12;
     * $themeQuery = "UPDATE (update user_profile SET theme = $theme, language = '$language' where user_id = $clientId;) as themeselected";
     * $themeData = $adapter1->query($themeQuery, Adapter::QUERY_MODE_EXECUTE)->toArray();;
     * echo $themeData;
     * die();
     *
     *
     *
     * return $themeData;
     *
     *
     *
     *
     *
     * /*$sql = new Sql ( $this->tableGateway->getAdapter() );
     *
     * $update = $sql->update();
     * $update->table('user_profile');
     * $update->set(array('language' => $profileData['selectedLanguage']));
     * $update->set(array('theme' => $profileData['themeColor']));
     * $update->where(array('client_id' => $profileData['clientId']));
     * //echo $update->getSqlString();
     *
     * $statement = $sql->prepareStatementForSqlObject($update);
     * try {
     * $result = $statement->execute(); // works fine
     * } catch (\Exception $e) {
     * die('Error: ' . $e->getMessage());
     * }
     *
     * return $result;
     *
     * }
     */
}