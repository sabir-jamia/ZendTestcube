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

    public function sendMail($mailTemplateName, $from, $to, $var, $bcc = array(), $attachment = false)
    {
        $messageBody = $var;
        $message = new Message();
        $mailTemplate = $this->serviceLocator->get('Email\Model\EmailTemplate');
        $mailTemplateData = $mailTemplate->getMailTemplate($mailTemplateName);
        $mailTemplateData = $mailTemplateData[0];
        $message->setEncoding('utf-8')
            ->addFrom($mailTemplateData['sender'], $from)
            ->setSubject($mailTemplateData['subject'])
            ->addReplyTo($mailTemplateData['replyto'])
            ->addTo($to);
        if (! $attachment) {
            $message->setBody($mailTemplateData['message'] . PHP_EOL . $messageBody);
        } else {
            $message->setBody($messageBody);
        }
        
        foreach ($bcc as $emailId) {
            $message->addBcc($emailId);
        }
        
        if (! $this->transport) {
            $this->setSmtpOption();
        }
        $this->transport->send($message);
        return 1;
    }
}