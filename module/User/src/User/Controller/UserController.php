<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use User\Form\LoginForm;
use User\Form\RegisterForm;
use Zend\Json\Json;
use User\Model\User;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
use Zend\Form\Element\File;
use Zend\InputFilter\FileInput;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\RenameUpload;
// use Zend\File\Transfer\Adapter\Http;
class UserController extends AbstractActionController
{

    private $_usersTable = null;

    private $_clientUsersTable = null;

    private $_authservice = null; 

    function attachScriptsAndStyleSheet()
    {
        $this->HeadPlugin()
            ->javaScript()
            ->appendFile('/user/user.js');
    }

    public function loginAction()
    {
        $this->attachScriptsAndStyleSheet();
        $request = $this->getRequest();
        $form = new LoginForm();
        $view = new ViewModel();
        
        if (! $request->isXmlHttpRequest()) {
            $view->setVariable('form', $form);
            return $view;
        } else {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setValidationGroup('username', 'password');
            $form->setData($request->getPost());
            if ($form->isValid()) {
                return new JsonModel($this->authenticate($form, $view));
            } else {
                $view->setTemplate('/user/user/login.phtml')
                    ->setTerminal(true)
                    ->setVariables(array(
                    'form' => $form
                ));
            }
        }
        
        return $view;
    }

    public function forgotPasswordAction()
    {
        $this->attachScriptsAndStyleSheet();
        $request = $this->getRequest();
        $response = $this->getResponse();
        
        if ($request->isXmlHttpRequest()) {
            if ($request->getMethod() == 'GET') {
                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                return $viewModel;
            } else {
                $emailId = $request->getPost('emailTxt');
                $validEmailvalue = $this->checkValue($emailId);
                $jsonModel = new JsonModel();
                if ($validEmailvalue == 1) { // password does not exists
                    $jsonModel->setVariable('status', 1);
                } elseif ($validEmailvalue == 2) {
                    $var = $this->getusersTable()->getPasswordByEmail($emailId);
                    $mailTemplate = $this->serviceLocator->get('Email\Model\EmailTemplate');
                    $mailTemplateData = $mailTemplate->getMailTemplate('ForgotPassword');
                    $messageBody = "Password : " . $var;
                    $emailIds[] = $emailId;
                    $mailData['mailTemplateData'] = $mailTemplateData;
                    $mailData['messageBody'] = $messageBody;
                    $mailData['emailIds'] = $emailIds;
                    $mailer = $this->serviceLocator->get('EmailService');
                    $mailer->sendMail($mailData);
                    
                    $htmlViewPart = new ViewModel();
                    $htmlViewPart->setTerminal(true)->setTemplate('/user/user/forgot-password-success.phtml');
                    $html = $this->getServiceLocator()
                        ->get('ViewRenderer')
                        ->render($htmlViewPart);
                    
                    $jsonModel = new JsonModel();
                    $jsonModel->setVariables(array(
                        'html' => $html,
                        'status' => 2
                    ));
                }
                return $jsonModel;
            }
        }
    }
    
    public function registerAction()
    {
        $request = $this->getRequest();
        
        if($request->isXmlHttpRequest()) {
            $response = $this->getResponse();
            $form = new RegisterForm();
            if($request->getMethod() == 'GET') {
                $view = new ViewModel(array(
                    'form' => $form
                ));
                $view->setTerminal(true);
                return $view;
            } else {
                $jsonModel = new JsonModel();
                $userEmail = $request->getPost('email');
                //$userId = $request->getPost('id');
                $register = new User();
                $form->setInputFilter($register->getInputFilter());
                $form->setData($request->getPost());
                if (!$form->isValid()) {
                    $register->exchangeArray($form->getData());
                    $msg = $this->getusersTable()->saveUser($register);
                    $str = $this->mntencodeAlgo($msg);
                    $url = $this->getBaseUrl() . "?id=" . $str;
                    $mailTemplate = $this->serviceLocator->get('Email\Model\EmailTemplate');
                    $mailTemplateData = $mailTemplate->getMailTemplate('clientregistration');   
                    $messageBody = $url;
                    $emailIds[] = $userEmail;
                    $mailData['mailTemplateData'] = $mailTemplateData;
                    $mailData['messageBody'] = $messageBody;
                    $mailData['emailIds'] = $emailIds;  
                    $mailer = $this->serviceLocator->get('EmailService');   
                    $mailer->sendMail($mailData);   
                    $jsonModel->setVariable('status', 1);
                } else {
                    $jsonModel->setVariable('status', 1);                
                }
                return $jsonModel;
            }
        }
    }
    
    public function getBaseUrl()
    {
        $config = $this->serviceLocator->get('config');
        return $config['applicationSettings']['appLink'];
    }

    public function checkValue($name)
    {
        $txtVal = $name;
        $checkVal = $this->getusersTable()->isEmailexist($txtVal);
        
        if (! $checkVal) {
            return 1;
        } else {
            return 2;
        }
    }

    public function sendEmailsAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
    }

    public function getAuthService($usernameType = '')
    {
        if (! $this->_authservice) {
            $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
            $authAdapter = new AuthAdapter($dbAdapter, 'users', $usernameType, 'password', 'MD5(?)');
            $authService = new AuthenticationService();
            $authService->setAdapter($authAdapter);
            $this->_authservice = $authService;
        }
        return $this->_authservice;
    }

    public function getusersTable()
    {
        if (! $this->_usersTable) {
            $this->_usersTable = $this->serviceLocator->get('User\Model\UserTable');
        }
        return $this->_usersTable;
    }

    public function getClientusersTable()
    {
        if (! $this->_clientUsersTable) {
            $this->_clientUsersTable = $this->serviceLocator->get('User\Model\ClientUserTable');
        }
        return $this->_clientUsersTable;
    }

    public function referAction()
    {
        $form = new RegisterForm();
        $captcha = $form->get('captcha')->getCaptcha();
        
        $id = $captcha->generate();
        $src = $captcha->getId() . $captcha->getSuffix();
        
        $response = $this->getResponse();
        $response->setContent(JSON::encode(array(
            'id' => $id,
            'src' => $src
        )));
        return $response;
    }

    public function logoutAction()
    {
        $userSession = new Container('users');
        $usernameType = $userSession->usernameType;
        if (isset($usernameType) && ! empty($usernameType)) {
            $this->getAuthService($usernameType)->clearIdentity();
            $userSession->offsetUnset('id');
            $userSession->offsetUnset('usernameType');
            $userSession->offsetUnset('clientId');
        }
        return $this->redirect()->toRoute('home');
    }

    public function checkUserExistsAction()
    {
        $jsonModel = new JsonModel();
        $user = $this->getRequest()->getPost('user');
        $userExists = $this->getusersTable()->checkUserExists($user);
        if (! $userExists) {
            $jsonModel->setVariable('status', 0);
        } else {
            $jsonModel->setVariable('status', 1);
        }
        return $jsonModel;
    }

    public function userProfileAction()
    {
        $userSession = new Container('users');
        $userid = $userSession->clientId;
        
        return new ViewModel(array(
            'userProfile' => $this->getusersTable()->userProfile($userid),
            'userdata' => $this->getusersTable()->userlist()
        ));
    }

    /* ------upload file module----- */
    public function getFileUploadLocation()
    {
        // Fetch Configuration from Module Config
        $config = $this->getServiceLocator()->get('config');
        return $config['module_config']['upload_location'];
    }

    public function generalProfileUpdateAction()
    {
        $userSession = new Container('users');
        $clientId = $userSession->clientId;
        $sm = $this->getServiceLocator();
        $request = $this->getRequest();
        $userData = $this->getRequest()->getPost('userData', null);
        
        $response = $this->getResponse();
        $files = $request->getFiles();
        $uploadFile = $this->getFileUploadLocation();
        
        $random = (rand(10, 1000));
        
        $fileName = $_FILES['profilePic']['name'] . $random;
        $profileData = array(
            'clientId' => $clientId,
            'profileFirstName' => $request->getPost('profileFirstName'),
            'profileLastName' => $request->getPost('profileLastName'),
            'profileContact' => $request->getPost('profileContact'),
            'profilePic' => $fileName,
            'uploadLocation' => $uploadFile,
            'random' => $random
        );
        $filter = new \Zend\Filter\File\RenameUpload(array(
            "target" => $uploadFile . '/' . $fileName
        ));
        // "randomize" =>'true',
        // "target" => $uploadFile,
        
        $fileData = $filter->filter($files['profilePic']);
        
        $result = $this->getClientusersTable()->clientGeneralProfileUpdate($profileData);
        if ($result) {
            $result = $this->getusersTable()->superGeneralProfileUpdate($profileData);
            if ($result) {
                $response->setContent(JSON::encode(array(
                    'flag' => 1,
                    'profilePic' => $profileData['profilePic']
                )));
                return $response;
            } else {
                $response->setContent(JSON::encode(array(
                    'flag' => 0
                )));
                return $response;
            }
        }
        
        $view = new ViewModel();
        $view->setTemplate('/user/userProfile');
        $view->setTerminal($request->isXMLHttpRequest());
        return $view;
    }

    public function verifyOldPasswordAction()
    {
        $response = $this->getResponse();
        
        $clientId = (int) $this->params()->fromRoute('id', 0);
        
        $usernameType = 'client_id';
        $this->getAuthService($usernameType)
            ->getAdapter()
            ->setIdentity($clientId);
        
        $this->getAuthService($usernameType)
            ->getAdapter()
            ->setCredential($this->request->getPost('oldPassword'));
        
        $result = $this->getAuthService($usernameType)->authenticate();
        if ($result->isValid()) {
            $response->setContent(JSON::encode(array(
                'flag' => 1
            )));
            return $response;
        } else {
            $response->setContent(JSON::encode(array(
                'flag' => 0
            )));
            return $response;
        }
        $view = new ViewModel(array(
            'form' => $form
        ));
        $view->setTerminal(true);
        return $view;
    }

    public function updateProfilePasswordAction()
    {
        $clientId = (int) $this->params()->fromRoute('id', 0);
        
        if (! $clientId) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'login'
            ));
        }
        
        $request = $this->getRequest();
        $response = $this->getResponse();
        
        $passwordData = array(
            'clientId' => $clientId,
            'newPassword' => $request->getPost('newPassword')
        );
        
        if ($request->getPost('newPassword') != $request->getPost('confirmPassword')) {
            $response->setContent(JSON::encode(array(
                'flag' => 0
            )));
            return $response;
        } else {
            $result = $this->getusersTable()->updateProfilePassword($passwordData);
            $response->setContent(JSON::encode(array(
                'flag' => 1
            )));
            return $response;
        }
        // Redirect to updated user profile
        $this->redirect()->toRoute('user', array(
            'action' => 'userProfile'
        ));
        $view = new ViewModel();
        $view->setTemplate('/user/userProfile');
        $view->setTerminal($request->isXMLHttpRequest());
        return $view;
    }

    public function mntencodeAlgo($id)
    {
        $id = intval($id * 300);
        
        $convert = array(
            "0" => 'e',
            "1" => 'r',
            "2" => 't',
            "3" => 'o',
            "4" => 'p',
            "5" => 'l',
            "6" => 'a',
            "7" => 'm',
            "8" => 'v',
            "9" => 'y'
        );
        $str = '';
        while ($id > 1) {
            
            $num = intval($id % 10);
            $id = intval($id / 10);
            $str = $str . $convert[$num];
        }
        $str = strrev($str);
        return $str;
    }

    public function mntdecodeAlgo($string)
    {
        $convert = array(
            "e" => '0',
            "r" => '1',
            "t" => '2',
            "o" => '3',
            "p" => '4',
            "l" => '5',
            "a" => '6',
            "m" => '7',
            "v" => '8',
            "y" => '9'
        );
        $result = '';
        $string = trim($string);
        $count = strlen($string);
        
        for ($i = 0; $i < $count; $i ++) {
            $char = $string{$i};
            if (strstr('ertoplamvy', $char)) {
                $result = $result . $convert[$char];
            }
        }
        
        $id = intval($result);
        $id = intval($id / 300);
        
        return $id;
    }

    public function listClientUserAction()
    {
        return new ViewModel();
    }

    public function userSettingAction()
    {
        $response = $this->getResponse();
        $request = $this->getRequest();
        $profileData = array(
            'clientId' => $request->getPost('clientid'),
            'selectedLanguage' => $request->getPost('profileLanguage'),
            'themeColor' => $request->getPost('profileTheme')
        );
        /*
         * $selectedLanguage = $request->getPost ( 'profileLanguage' );
         * $themeColor = $request->getPost ( 'profileTheme' );
         */
        $result = $this->getusersTable()->clientGeneralProfileSettings($profileData);
        
        if ($result == 1) {
            $response->setContent(JSON::encode(array(
                'flag' => 1
            )));
            return $response;
        } else {
            $response->setContent(JSON::encode(array(
                'flag' => 0
            )));
            return $response;
        }
    }

    public function authenticate($form, $viewModel)
    {
        $request = $this->getRequest();
        $username = trim($request->getPost('username'), " ");
        $password = $this->request->getPost('password');
        $response = array();
        
        if (strpos($username, '@')) {
            $usernameType = 'email';
        } else {
            $usernameType = 'username';
        }
        
        $this->getAuthService($usernameType)
            ->getAdapter()
            ->setIdentity($username)
            ->setCredential($password);
        $result = $this->_authservice->authenticate();
        
        if ($result->isValid()) {
            $userData = $this->getusersTable()->getUserByUserName($username, $usernameType);
            if ($userData == 'notconfirmed') {
                $response['flag'] = 'notconfirmed';
            } else {
                $userSession = new Container('users');
                $userSession->clientId = $userData->getClientId();
                $userSession->usernameType = $usernameType;
                $userSession->username = $userData->getUserName();
                $userSession->email = $userData->getEmail();
                $response['flag'] = 'loginsuccess';
            }
        } else {
            $response['flag'] = 'loginFail';
        }
        
        return $response;
    }
}