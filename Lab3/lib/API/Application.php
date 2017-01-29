<?php
namespace API;

use Slim\Slim;
use Swift_Message;

class Application extends Slim
{
    private static $mailer;


//----------------------------------------Mailer------------------------------------------------------------------

    /**mail initialisation
     * @param $mailer
     */
    public static function setMailer($mailer){

        self::$mailer = $mailer;

    }

    /**Main function
     * @param $to
     * @param $subject
     * @param $body
     * @return mixed
     */
    public static function sendMail($to, $subject, $body)
    {
        $from = 'info@officialalert.md';

        $transport = \Swift_SmtpTransport::newInstance("smtp.ebs.md", 25, "tls")
            ->setUsername($from)
            ->setPassword("budjs2EX");

        $mMailer = \Swift_Mailer::newInstance($transport);

        self::setMailer($mMailer);

        $message = Swift_Message::newInstance();

        $message->setSubject($subject)
            ->setFrom($from, 'Official Alert')
            ->setReplyTo($from, 'Official Alert')
            ->setTo($to)
            ->setBody($body)
            ->setContentType("text/html");

        // Send the message
        return self::$mailer->send($message);

    }
//-----------------------------------------------------------------------------------------------------------------

}
