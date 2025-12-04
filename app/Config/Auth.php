<?php

namespace Config;

class Auth extends \Myth\Auth\Config\Auth
{
    public $views = [
        'login' => 'Auth/login',
    ];

    public $allowRegistration = true;

    public $requireActivation = 'App\Auth\Activators\WahaActivator';

    public $userActivators = [
        'App\Auth\Activators\WahaActivator' => [
            'apiURL' => null,
            'apiToken' => null,
        ],
        'Myth\Auth\Authentication\Activators\EmailActivator' => [
            'fromEmail' => null,
            'fromName' => null,
        ],
    ];
}
