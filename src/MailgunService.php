<?php

namespace gameap\mail;

use \Myth\Mail\MailServiceInterface;
use Mailgun\Mailgun;

class MailgunService implements MailServiceInterface {

    protected $domain = '';
    protected $apiKey = '';

    private $message = [];

    public function __construct($config = [])
    {
        if (is_array($config) && empty($config)) {
            foreach ($config as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Does the actual delivery of a message. In this case, though, we simply
     * write the html and text files out to the log folder/emails.
     *
     * The filename format is: yyyymmddhhiiss_email.{format}
     *
     * @param bool  $clear_after    If TRUE, will reset the class after sending.
     *
     * @return mixed
     */
    public function send($clear_after=true)
    {
        $mg = Mailgun::create($this->apiKey);
        $mg->messages()->send($this->domain, $this->message);
    }

    /**
     * Adds an attachment to the current email that is being built.
     *
     * @param string    $filename
     * @param string    $disposition    like 'inline'. Default is 'attachment'
     * @param string    $newname        If you'd like to rename the file for delivery
     * @param string    $mime           Custom defined mime type.
     */
    public function attach($filename, $disposition=null, $newname=null, $mime=null)
    {
        $key = $disposition ? 'inline' : 'attachment';

        $this->message[$key]['filePath'] = $filename;

        if ($newname) {
            $this->message[$key]['filename'] = $newname;
        } else {
            $this->message[$key]['filename'] = basename($filename);
        }
    }

    /**
     * Sets a header value for the email. Not every service will provide this.
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function setHeader($field, $value)
    {
        $this->message['h:' . $field] = $value;
    }

    /**
     * Sets the email address to send the email to.
     *
     * @param $email
     * @return mixed
     */
    public function to($email)
    {
        $this->message['to'] = $email;
    }

    /**
     * Sets who the email is coming from.
     *
     * @param $email
     * @param null $name
     * @return mixed
     */
    public function from($email, $name=null)
    {
        if (!empty($name)) {
            $this->message['from'] = $email;
        } else {
            $this->message['from'] = "{$name} <{$email}>";
        }
    }

    /**
     * Sets a single additional email address to 'cc'.
     *
     * @param $email
     * @return mixed
     */
    public function cc($email)
    {
        $this->message['cc'] = $email;
    }

    /**
     * Sets a single email address to 'bcc' to.
     *
     * @param $email
     * @return mixed
     */
    public function bcc($email)
    {
        $this->message['bcc'] = $email;
    }

    /**
     * Sets the reply to address.
     *
     * @param $email
     * @return mixed
     */
    public function reply_to($email, $name = null)
    {
        $this->setHeader('Reply-To', $email);
    }

    /**
     * Sets the subject line of the email.
     *
     * @param $subject
     * @return mixed
     */
    public function subject($subject)
    {
        $this->message['subject'] = $subject;
    }

    /**
     * Sets the HTML portion of the email address. Optional.
     *
     * @param $message
     * @return mixed
     */
    public function html_message($message)
    {
        $this->message['text'] = $message;
    }

    /**
     * Sets the text portion of the email address. Optional.
     *
     * @param $message
     * @return mixed
     */
    public function text_message($message)
    {
        $this->message['text'] = $message;
    }

    /**
     * Sets the format to send the email in. Either 'html' or 'text'.
     *
     * @param $format
     * @return mixed
     */
    public function format($format)
    {

    }

    /**
     * Resets the state to blank, ready for a new email. Useful when
     * sending emails in a loop and you need to make sure that the
     * email is reset.
     *
     * @param bool $clear_attachments
     * @return mixed
     */
    public function reset($clear_attachments=true)
    {
        $this->message = [];
    }
}