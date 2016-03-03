<?php
namespace Email\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;

class EmailService implements ServiceLocatorAwareInterface
{

    protected $serviceLocator;

    protected $transport;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator            
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setSmtpOption()
    {
        // Setup SMTP transport using LOGIN authentication
        $transport = new SmtpTransport();
        
        $config = $this->serviceLocator->get('config');
        
        // \Zend\Debug\Debug::dump();
        $options = new SmtpOptions($config['mail_setting']);
        
        $transport->setOptions($options);
        $this->transport = $transport;
    }

    public function sendMail($mailData, $attachment = false)
    {
        $mailTemplateData = $mailData['mailTemplateData'];
        $messageBody = $mailData['messageBody'];
        $emailIds = $mailData['emailIds'];
        
        $message = new Message();
        
        foreach ($mailTemplateData as $mailOptions) {
            $message->setEncoding('utf-8')
                ->addFrom($mailOptions['sender'], "TESTCube.com")
                ->setSubject($mailOptions['subject'])
                ->addReplyTo($mailOptions['replyto']);
            if (! $attachment) {
                $message->setBody($mailOptions['message'] . PHP_EOL . $messageBody);
            } else {
                $message->setBody($messageBody);
            }
        }
        
        foreach ($emailIds as $emailId) {
            $message->addBcc($emailId);
        }
        
        if (! $this->transport) {
            $this->setSmtpOption();
        }
        $this->transport->send($message);
        return 1;
    }
}