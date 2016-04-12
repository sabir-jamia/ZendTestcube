<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use User\Form\LoginForm;
use User\Form\RegisterForm;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
use Zend\Form\Element\File;
use Zend\InputFilter\FileInput;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\RenameUpload;
use Zend\Session\AbstractContainer;

class UserController extends AbstractActionController
{

    private $_authservice = null;
    
    protected $sm;
    
    public function __construct(ServiceLocatorInterface $sm) 
    {
    	$this->sm = $sm; 	
    }

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
        
        if (! $request->isXmlHttpRequest()) {
            $id = $this->params()->fromQuery('id');
            $userTable = $this->sm->get('User\Model\UserTable');
            $confirmStatus  = $userTable->updatestatus($this->mntdecodeAlgo($id));
            return new ViewModel(array(
                'form' => $form,
                'status' => $confirmStatus
            ));
        } else {
            $user = $this->sm->get('User\Model\User');
            $form->setInputFilter($user->getInputFilter());
            $form->setValidationGroup('username', 'password');
            $form->setData($request->getPost());
            if ($form->isValid()) {
                return new JsonModel($this->authenticate($form));
            } else {
            	$view = new ViewModel();
                $view->setTemplate('/user/user/login.phtml')
                    ->setTerminal(true)
                    ->setVariables(array(
                        'form' => $form
                    ));
            }
        }
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
                    $mailTemplate = 'ForgotPassword';
                    $userTable = $this->sm->get('User\Model\UserTable');
                    $var = "Password : " . $userTable->getPasswordByEmail($emailId);
                    $mailer = $this->sm->get('EmailService');
                    $mailer->sendMail($mailTemplate, "Admin Testcube", $emailId, $var);
                    $jsonModel = new JsonModel();
                    $jsonModel->setVariables(array(
                        'message' => 'Your password has been sent to email',
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
        
        if ($request->isXmlHttpRequest()) {
            $response = $this->getResponse();
            $form = new RegisterForm();
            if ($request->getMethod() == 'GET') {
                $view = new ViewModel(array(
                    'form' => $form
                ));
                $view->setTerminal(true);
                return $view;
            } else {
                $jsonModel = new JsonModel();
                $userEmail = $request->getPost('email');
                $register = $this->sm->get('User\Model\User');
                $form->setInputFilter($register->getInputFilter());
                $postData = $request->getPost();
                $form->setData($postData);
                if (! $form->isValid()) {
                    $register->exchangeArray($form->getData());
                    $restService = $this->sm->get('RestClient');
                    $userDataArr =  get_object_vars($postData);
                    unset($userDataArr['confirm-password'], $userDataArr['captcha']);
                    $result = $restService->callRestApi("register", $userDataArr); 
                    if($result->status = "success") {
                        $str = $this->mntencodeAlgo($result->data->clientId);
                        $url = $this->getBaseUrl() . "?id=" . $str; 
                        $mailTemplate = 'clientregistration';
                        $mailer = $this->sm->get('EmailService');
                        $mailer->sendMail($mailTemplate, "Admin Testcube", $userEmail, $url);
                        $jsonModel->setVariables(array(
                            'status' => 1,
                            'message' => 'Account created! Please confirm account from link sent on your Email'
                        ));
                    } else {
                        $jsonModel->setVariables(array(
                            'status' => 0,
                            'message' => 'Account not created! Some error occurred'
                        ));
                    }
                } else {
                    $jsonModel->setVariable('status', 1);
                }
                return $jsonModel;
            }
        }
    }

    public function checkCaptchaAction()
    {
        $captchaHiddenId = $this->params()->fromPost('captcha-hidden');
        $captcha = $this->params()->fromPost('captcha');
        $captchaInput = $captcha['input'];
        $captchaSession = new Container('Zend_Form_Captcha_' . trim($captchaHiddenId));
        $captchaSession->setExpirationHops(100);
        $captchaIterator = $captchaSession->getIterator();
        $captchaWord = $captchaIterator['word'];
        if ($captchaWord == $captchaInput) {
            echo "true";
        } else {
            echo "false";
        }
        return $this->getResponse();
    }

    public function getBaseUrl()
    {
        $config = $this->sm->get('config');
        return $config['applicationSettings']['appLink'];
    }

    public function checkValue($name)
    {
        $txtVal = $name;
        $userTable = $this->sm->get('User\Model\UserTable');
        $checkVal = $userTable->isEmailexist($txtVal);
        
        if (! $checkVal) {
            return 1;
        } else {
            return 2;
        }
    }

    public function refreshAction()
    {
        $form = new RegisterForm();
        $captcha = $form->get('captcha')->getCaptcha();
        $id = $captcha->generate();
        $src = $captcha->getId() . $captcha->getSuffix();
        return new JsonModel(array(
            'id' => $id,
            'src' => $src
        ));
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
        $user = $this->params()->fromPost('user');
        $userTable = $this->sm->get('User\Model\UserTable');
        $userExists = $userTable->checkUserExists($user);
        if (! $userExists) {
            echo "true";
        } else {
            echo "false";
        }
        return $this->getResponse();
    }

    public function userProfileAction()
    {
        $userSession = new Container('users');
        $userid = $userSession->clientId;
        $userTable = $this->sm->get('User\Model\UserTable');
        
        return new ViewModel(array(
            'userProfile' => $userTable->userProfile($userid),
            'userdata' => $userTable->userlist()
        ));
    }

    public function getAuthService($usernameType = '')
    {
        if (! $this->_authservice) {
            $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
            $authAdapter = new AuthAdapter($dbAdapter, 'users', $usernameType, 'password', 'MD5(?)');
            $authService = new AuthenticationService();
            $authService->setAdapter($authAdapter);
            $this->_authservice = $authService;
        }
        return $this->_authservice;
    }

    public function generalProfileUpdateAction()
    {
        $userSession = new Container('users');
        $clientId = $userSession->clientId;
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
        $clientUserTable = $this->sm->get('User\Model\ClientUserTable');
        $result = $clientuserTable->clientGeneralProfileUpdate($profileData);
        if ($result) {
            $userTable = $this->sm->get('User\Model\UserTable');
            $result = $userTable->superGeneralProfileUpdate($profileData);
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
            $userTable = $this->sm->get('User\Model\UserTable');
            $result = $userTable->updateProfilePassword($passwordData);
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
        $userTable = $this->sm->get('User\Model\UserTable');
        $result = $userTable->clientGeneralProfileSettings($profileData);
        
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

    /* ------upload file module----- */
    public function getFileUploadLocation()
    {
        // Fetch Configuration from Module Config
        $config = $this->sm->get('config');
        return $config['module_config']['upload_location'];
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

    public function authenticate($form)
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
        
        $restService = $this->sm->get('RestClient');
        $result = $restService->callRestApi("authenticate", array(
            "username" => $username,
            "userType" => $usernameType
        ));
        if ($result->status == "success" && ! empty($result->data) && ! empty($result->data->id) && $result->data->password == $password) {
            if ($result->data->status == 1) {
                $userSession = new Container('users');
                    $userSession->clientId = $result->data->clientId;
                    $userSession->usernameType = $usernameType;
                    $userSession->username = $result->data->username;
                    $userSession->email = $result->data->email;
                    $response['flag'] = 'loginsuccess';
                } else {
                    $response['flag'] = 'notconfirmed';
                }
        } else {
            $response['flag'] = 'loginFail';
        }
        return $response;
    }
}