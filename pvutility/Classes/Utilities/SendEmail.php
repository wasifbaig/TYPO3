<?php

namespace PV\Utility\Utilities;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * Description of SendEmail
 *
 * @author PV1
 */
class SendEmail {
   
    /**
    * @param array $recipient recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
    * @param array $sender sender of the email in the format array('sender@domain.tld' => 'Sender Name')
    * @param string $subject subject of the email
    * @param string $templateName template name (UpperCamelCase)
    * @param array $variables variables to be passed to the Fluid view
    * @return string
    */
    
   public function getTemplateEmail($templateName, array $variables = array()) {
       
       
            $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
            $configurationManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Configuration\ConfigurationManager');
            
            /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
            $emailView = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

            $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
            $templateRootPath = GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath']. 'assets/Email/');
            
            // in case you get an error like: The Fluid template files "" could not be loaded.
            $layoutRootPath = GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['layoutRootPath'].'assets/Email/');
            $partialRootPath = GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['partialRootPath'].'assets/Email/Partials/');
            $emailView->setLayoutRootPaths(array($layoutRootPath));
            $emailView->setPartialRootPaths(array($partialRootPath));
            
            
            //DebuggerUtility::var_dump($variables);
            
            $templatePathAndFilename = $templateRootPath . $templateName . '.html';
            $emailView->setTemplatePathAndFilename($templatePathAndFilename);
            $emailView->assignMultiple($variables);
            $emailBody = $emailView->render();
            
            return $emailBody;
            
            
 
    }
    
    public function send(array $recipient, array $sender, $subject, $emailBody)
    {
        
        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
             // if you have an additional html Template //
            // $templatePathAndFilename = $templateRootPath . 'Email/' . $templateName . 'Html.html';
            // $emailView->setTemplatePathAndFilename($templatePathAndFilename);
            // $emailHtmlBody = $emailView->render();

            // if you want to use german or other UTF-8 chars in subject enable next line 
            // $subject =  '=?utf-8?B?'. base64_encode( subject  ) .'?=' ;

             /** @var $message \TYPO3\CMS\Core\Mail\MailMessage */
            $message = $objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
            $message->setTo($recipient)
                      ->setFrom($sender)
                      ->setSubject($subject);

            // Possible attachments here
            //foreach ($attachments as $attachment) {
            //	$message->attach(\Swift_Attachment::fromPath($attachment));
            //}

            // Plain text example
            //$message->setBody($emailBody, 'text/plain');

            // HTML Email
            $message->addPart($emailBody, 'text/html');
            
            $message->send();
            return $message->isSent();
    }        
    
    
}
