<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }

    private function configureMailer()
    {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host       = env('MAIL_HOST', 'smtp.gmail.com');
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = env('MAIL_USERNAME');
            $this->mailer->Password   = env('MAIL_PASSWORD');
            $this->mailer->SMTPSecure = env('MAIL_ENCRYPTION', PHPMailer::ENCRYPTION_STARTTLS);
            $this->mailer->Port       = env('MAIL_PORT', 587);

            // Recipients
            $this->mailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        } catch (Exception $e) {
            throw new \Exception("Mail configuration failed: " . $e->getMessage());
        }
    }

    public function sendOTP($email, $name, $otp, $purpose = 'verification')
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $name);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'OTP Verification - ' . ucfirst(str_replace('_', ' ', $purpose));
            
            $this->mailer->Body = $this->getOTPTemplate($name, $otp, $purpose);
            $this->mailer->AltBody = "Your OTP code is: {$otp}. This code will expire in 10 minutes.";

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            throw new \Exception("Message could not be sent. Mailer Error: " . $e->getMessage());
        }
    }

    private function getOTPTemplate($name, $otp, $purpose)
    {
        $purposeText = ucfirst(str_replace('_', ' ', $purpose));
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .otp-box { background: white; padding: 20px; text-align: center; margin: 20px 0; border: 2px dashed #007bff; }
                .otp-code { font-size: 32px; font-weight: bold; color: #007bff; letter-spacing: 5px; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>OTP Verification</h1>
                </div>
                <div class='content'>
                    <h2>Hello {$name},</h2>
                    <p>You have requested to verify your identity for <strong>{$purposeText}</strong>.</p>
                    <p>Please use the following OTP code to complete the verification:</p>
                    
                    <div class='otp-box'>
                        <div class='otp-code'>{$otp}</div>
                    </div>
                    
                    <p><strong>Important:</strong></p>
                    <ul>
                        <li>This OTP is valid for 10 minutes only</li>
                        <li>Do not share this code with anyone</li>
                        <li>If you didn't request this, please ignore this email</li>
                    </ul>
                </div>
                <div class='footer'>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}