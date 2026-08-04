<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SmtpService
{
    /**
     * Set mailer settings in config at runtime.
     */
    public function configureDynamicSmtp(): void
    {
        $mailer = strtolower(Setting::get('mail_mailer', 'smtp'));
        $host = Setting::get('mail_host', 'smtp.mailtrap.io');
        $port = Setting::get('mail_port', '2525');
        $username = Setting::get('mail_username');
        $password = Setting::get('mail_password');
        $encryption = Setting::get('mail_encryption', 'tls');
        $fromAddress = Setting::get('mail_from_address', 'care@rohidafarm.com');
        $fromName = Setting::get('mail_from_name', 'RohidaFarm');

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', $mailer);
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', $port);
        Config::set('mail.mailers.smtp.username', $username);
        Config::set('mail.mailers.smtp.password', $password);
        Config::set('mail.mailers.smtp.encryption', $encryption);
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName);

        // Purge the mailer cache to force recreation with new config
        Mail::purge('smtp');
        Mail::purge();
    }

    /**
     * Send a diagnostic email to test configurations.
     */
    public function sendTestEmail(string $recipientEmail): bool
    {
        $this->configureDynamicSmtp();

        try {
            Mail::to($recipientEmail)->send(new \App\Mail\TestTemplateMail());
            return true;
        } catch (\Exception $e) {
            logger()->error('Dynamic SMTP test execution failure: ' . $e->getMessage());
            return false;
        }
    }
}
