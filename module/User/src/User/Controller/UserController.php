<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Form\LoginForm;
use User\Form\RegisterForm;
use Zend\Json\Json;
use User\Model\User;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
use Zend\Form\Element\File;
use Zend\InputFilter\FileInput;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\RenameUpload;
// use Zend\File\Transfer\Adapter\Http;
class UserController extends AbstractActionController
{

    private $usersTable = null;

    private $clientUsersTable = null;

    private $authservice = null;

    public function loginAction()
    {
        $statusmsg = $this->params()->fromQuery('statusmsg');
        // echo $statusmsg;
        // die();
        $id = $this->params()->fromQuery('id');
        $request = $this->getRequest();
        $form = new LoginForm();
        
        if ($statusmsg == 1) {
            $view = new ViewModel(array(
                'form' => $form,
                'status1' => 4
            ));
            $view->setTerminal($request->isXMLHttpRequest());
            return $view;
        }
        
        if ($id) {
            $id = $this->params()->fromQuery('id');
            $id = $this->mntdecodeAlgo($id);
            $confirmStatus = $this->getusersTable()->updatestatus($id);
            
            $view = new ViewModel(array(
                'form' => $form,
                'status' => $confirmStatus
            ));
            $view->setTerminal($request->isXMLHttpRequest());
            return $view;
        }
        
        $view = new ViewModel(array(
            'form' => $form
        ));
        $view->setTerminal($request->isXMLHttpRequest());
        return $view;
    }

    public function forgotpasswordAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $forgotPass = $request->getPost('emailTxt');
        
        $validEmailvalue = $this->checkVal($forgotPass);
        if ($validEmailvalue == 1) {
            $response->setContent(Json::encode(array(
                'status' => 1
            )));
            return $response;
        } 

        else 
            if ($validEmailvalue == 2) {
                $viewmodel = new ViewModel(array(
                    'password' => $this->getusersTable()->getPasswordByEmail($forgotPass)
                ));
                $var = $this->getusersTable()->getPasswordByEmail($forgotPass);
                
                $serviceManager = $this->getServiceLocator();
                $mailTemplate = $serviceManager->get('Email\Model\EmailTemplate');
                $mailTemplateData = $mailTemplate->getMailTemplate('ForgotPassword');
                
                $messageBody = "Password : " . $var;
                $emailIds[] = $forgotPass;
                
                $mailData['mailTemplateData'] = $mailTemplateData;
                $mailData['messageBody'] = $messageBody;
                $mailData['emailIds'] = $emailIds;
                
                $serviceManager = $this->getServiceLocator();
                $mailer = $serviceManager->get('EmailService');
                
                $mailer->sendMail($mailData);
                
                $viewmodel->setTerminal(true);
                $response->setContent(Json::encode(array(
                    'status' => 2
                )));
                
                return $response;
            }
    }

    public function getBaseUrl()
    {
        $sm = $this->getServiceLocator();
        $config = $sm->get('config');
        return $config['applicationSettings']['appLink'];
    }

    public function checkVal($name)
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
        if (! $this->authservice) {
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'users', $usernameType, 'password', 'MD5(?)');
            $authService = new AuthenticationService();
            $authService->setAdapter($dbTableAuthAdapter);
            $this->authservice = $authService;
        }
        return $this->authservice;
    }

    public function processAction()
    {
        $view = new ViewModel();
        $request = $this->getRequest();
        $response = $this->getresponse();
        $form = new LoginForm();
        $user = new User();
        
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $str = trim($request->getPost('username'), " ");
            $pos = strpos($str, '@');
            
            if ($pos !== false) {
                $usernameType = 'email';
                $form->setValidationGroup('username', 'password');
            } else {
                $usernameType = 'username';
                $form->setValidationGroup('username', 'password');
            }
            
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $s = trim($this->request->getPost('username'), ' ');
                $this->getAuthService($usernameType)
                    ->getAdapter()
                    ->setIdentity($s);
                $this->getAuthService($usernameType)
                    ->getAdapter()
                    ->setCredential($this->request->getPost('password'));
                $result = $this->getAuthService($usernameType)->authenticate();
                
                if ($result->isValid()) {
                    $userData = $this->getusersTable()->getUserByUserName($s, $usernameType);    
                    if ($userData == 'notconfirmed') {
                        $response->setContent(JSON::encode(array(
                            'flag' => 'notconfirmed'
                        )));
                    } else { 
                        $userSession = new Container('users');
                        $userSession->clientId = $userData->getClientId();
                        $userSession->usernameType = $usernameType;
                        $userSession->username = $userData->getUserName();
                        $userSession->email = $userData->getEmail();
                        
                        $response->setContent(JSON::encode(array(
                            'flag' => 'loginsuccess'
                        )));
                    }
                    return $response;
                } else {
                    $response->setContent(JSON::encode(array(
                        'flag' => 'loginFail'
                    )));
                    return $response;
                }
            } else {
                $view->setVariables(array(
                    'form' => $form
                ));
                $view->setTemplate('/user/user/login.phtml');
                $view->setTerminal(true);
                return $view;
            }
        }
    }

    public function registerAction()
    {
        $form = new RegisterForm();   
        $form->get('submit')->setValue('Register');
        $request = $this->getRequest();
        $response = $this->getResponse();
        
        if ($request->isPost()) {
            $userEmail = $request->getPost('email');
            $userId = $request->getPost('id');
            
            $register = new User();
            $form->setInputFilter($register->getInputFilter());
            $form->setData($request->getPost()); // setting requested data to form object
            
            if ($form->isValid()) { 
                $register->exchangeArray($form->getData());
                $msg = $this->getusersTable()->saveUser($register);
                $str = $this->mntencodeAlgo($msg);
                $url = $this->getBaseUrl() . "?id=" . $str;
                
                $serviceManager = $this->getServiceLocator();
                $mailTemplate = $serviceManager->get('Email\Model\EmailTemplate');
                $mailTemplateData = $mailTemplate->getMailTemplate('clientregistration');
                
                $messageBody = $url;
                $emailIds[] = $userEmail;
                
                $mailData['mailTemplateData'] = $mailTemplateData;
                $mailData['messageBody'] = $messageBody;
                $mailData['emailIds'] = $emailIds;
                
                var_dump($mailData);die;
                
                $serviceManager = $this->getServiceLocator();
                $mailer = $serviceManager->get('EmailService');
                
                $mailer->sendMail($mailData);
                
                $response = $this->getResponse();
                $response->setContent(JSON::encode(array(
                    'succ' => 1
                )));
            } else {
                
                $response = $this->getResponse();
                $response->setContent(JSON::encode(array(
                    'succ' => 0
                )));
            }
            return $response;
        }
        
        $view = new ViewModel(array(
            'form' => $form
        ));
        $view->setTerminal(true);
        return $view;
    }

    public function getusersTable()
    {
        if (! $this->usersTable) {
            $serviceManager = $this->getServiceLocator();
            
            $this->usersTable = $serviceManager->get('User\Model\UserTable');
        }
        return $this->usersTable;
    }

    public function getClientusersTable()
    {
        if (! $this->clientUsersTable) {
            $serviceManager = $this->getServiceLocator();
            
            $this->clientUsersTable = $serviceManager->get('User\Model\ClientUserTable');
        }
        
        return $this->clientUsersTable;
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

    public function checkValAction()
    {
        $txtVal = $this->getRequest()->getPost('txtVal');
        $checkVal = $this->getusersTable()->checkVal($txtVal);
        if (! $checkVal) {
            $response = $this->getresponse();
            $response->setContent(JSON::encode(array(
                'val' => 0
            )));
        } else {
            $response = $this->getResponse();
            $response->setContent(JSON::encode(array(
                'val' => 1
            )));
        }
        return $response;
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
        )
        // "randomize" =>'true',
        // "target" => $uploadFile,
        );
        $fileData = $filter->filter($files['profilePic']);
        
        $result = $this->getClientusersTable()->clientGeneralProfileUpdate($profileData);
        if ($result) {
            $result = $this->getusersTable()->superGeneralProfileUpdate($profileData);
            if ($result) {
                $response->setContent(JSON::encode(array(
                    'flag' => 1,
                    'profilePic' => $profileData['profilePic']
                )
                ));
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
}