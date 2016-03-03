<?php
return array(
    'mail_setting' => array(
        'host' => 'smtp.gmail.com',
        'connection_class' => 'login',
        'connection_config' => array(
            'ssl' => 'tls',
            'username' => 'dev.sabir.jmi11@gmail.com',
            'password' => 'sabir@oss@3113'
        ),
        'port' => 587
    )
);


/****** How to use ****/
/*

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;




class UserController extends AbstractActionController
{


public function inviteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $email = $request->getPost('email', NULL);
            $sm = $this->getServiceLocator();
            $mailService = $sm->get('EmailService');
            $message = new Message();
            $msg = '<h1> hello </h1>';

            $html = new MimePart($msg);
            $html->type = "text/html";
             
            $body = new MimeMessage();
            $body->addPart($html);
 
            $message->addTo($email)
                    ->setFrom('sunil kumar')
                    ->setSubject('Test send mail using ZF2')
                    ->setBody($body);

           $mailService->sendMail($message);
           return $this->redirect()->toRoute('user');

        }
    }

}*/