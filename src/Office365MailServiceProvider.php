<?php

namespace Office365Mail;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Office365Mail\Transport\Office365MailTransport;

class Office365MailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/office365mail.php' => config_path('office365mail.php')
        ], 'office365mail');
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->singleton('mail.manager', function ($app) {
            $manager = new Office365MailTransport($app);
            $this->registerCustomTransports($manager);    // Registers sengrid and other custom drivers.
            return $manager;
        });

        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
        });
        /*
        $this->app->afterResolving('mail.manager', function (MailManager $manager) {
            $this->extendMailManager($manager);
        });
        */
    }

    public function extendMailManager(MailManager $manager)
    {
        $manager->extend('office365mail', function () {
            return new Office365MailTransport();
        });
    }
}
