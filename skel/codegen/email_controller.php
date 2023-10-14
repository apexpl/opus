<?php
declare(strict_types = 1);

namespace ~namespace~;

use Apex\App\Base\Web\Utils\FormBuilder;
use Apex\App\Interfaces\EmailNotificationControllerInterface;
use Apex\App\Interfaces\UserInterface;
use Apex\Mercury\Email\{EmailContact, EmailContactCollection};

/**
 * ~alias.title~ E-Mail Notification Controller
 */
class ~class_name~ implements EmailNotificationControllerInterface
{

    /**
     * The title of the e-mail notification controller.
     */
    public string $title = 'New E-Mail Notification Controller';


    /**
     * Get conditional form fields.
     *
     * These form fields are displayed when the administrator is creating a new 
     * e-mail notification with this controller, and allow the 
     * administrator to define the conditionals of the e-mail.
     */
    public function getConditionFormFields(FormBuilder $builder):array
    {

        // Generate form fields
        $fields = [
            'is_active' => $builder->boolean()
        ];

        // Return
        return $fields;
    }

    /**
     * Get available senders
     *
     * When the administrator is creating an e-mail notification, they can select 
     * who appears as the sender.  If any senders are available aside from 'user' and 'admin', 
     * return them here in an associative array.  Otherwise, return null.
     */
    public function getAvailableSenders():?array
    {
        return null;
    }

    /**
     * Get available recipients
     *
     * When the administrator is creating an e-mail notification, they can select 
     * who is the recipient.  If any recipients are available aside from 'user' and 'admin', 
     * return them here in an associative array.  Otherwise, return null.
     */
    public function getAvailableRecipients():?array
    {
        return null;
    }

    /**
     * Get merge fields
     *
     * Define the additional merge fields available within this notification controller 
     * that may be used to personalize e-mail messages aside 
     * from standard user profile information.
     */
    public function getMergeFields():array
    {

        $fields = [];
        $fields['Some Category'] = [
            'somecat-id' => 'ID#',
            'somecat-amount' => 'Amount',
            'somecat-name' => 'Name'
        ];

        // Return
        return $fields;
    }

    /**
     * Get merge vars
     *
     * Obtain the personalized information to send an individual e-mail.
     * This should generate the necessary array of personalized information 
     * for all fields defined within the getMergeFields() method of this class.
     */
    public function getMergeVars(string $uuid, array $data = []):array
    {

        // Set personalized info according to merge fields
        $vars = [
            'somecat-id' => 881,
            'somecat-amount' => 34.95,
            'somecat-name' => 'Electronics'
        ];

        // Return
        return $vars;
    }

    /**
     * Get sender
     *
     * If additional senders are available other than 'admin' and 'user', 
     * This should return the sender name and e-mail address as a 
     * EmailContact object.  Otherwise, return null.
     */
    public function getSender(string $sender, ?UserInterface $user, array $data = []):?EmailContact
    {
        return null;
    }

    /**
     * Get recipient
     *
     * If additional recipients are available other than 'admin' and 'user', 
     * This should return the recipient name and e-mail address as a 
     * EmailContact object.  Otherwise, return null.
     */
    public function getRecipients(string $recipient, ?UserInterface $user, array $data = []):?EmailContactCollection
    {
        return null;
    }

    /**
     * Get reply-to
     *
     * If necessary, you can return the Reply-TO e-mail address of the message here.  
     * Otherwise, return null.
     */
    public function getReplyTo(array $data = []):?string
    {
        return null;
    }

    /**
     * Get cc
     *
     * If necessary, you can return the Cc e-mail address of the message here.  
     * Otherwise, return null.
     */
    public function getCc(array $data = []):?array
    {
        return null;
    }

    /**
     * Get bcc
     *
     * If necessary, you can return the bcc e-mail address of the message here.  
     * Otherwise, return null.
     */
    public function getBcc(array $data = []):?array
    {
        return null;
    }
}




