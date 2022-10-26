<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Entities\User;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Src\Services\QrService;
use Src\Services\AuthService;
use Src\Services\UserService;
use Src\Controllers\BaseController;
use Src\Controllers\HomeController;

class PaymentController extends BaseController{

    public static function path(){
        return 'exc';
    }

    public static function index()
    {
        $userServices = new UserService();
        self::init();
        $alert = false;
        $qr = false ;

        $user = $userServices->autoRefresh($_SESSION['user']);

        if (empty($_POST['amount'])) {
            header('location: amount');
            die();
        }

        \Stripe\Stripe::setApiKey('sk_test_51LpWzeK8zI5Hqeq0fijdaVrEOliXGx9nnjRL2c05x6Uzw76gyQmoZPBHIjFxFtcLcpegNLDzjXuZMHIfn3YWULPg00GYj1kgrG');
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => intval($_POST['amount']),
            'currency' => 'eur'
        ]);

        if (empty($paymentIntent->client_secret)) {
            header('location: amount');
            die();
        }

        $_SESSION['payment'] = [
            "amount" => intval($_POST['amount']), 
            "secret" => $paymentIntent->client_secret
        ];
    
        $output = [
            'clientSecret' => $paymentIntent->client_secret,
        ];
        

        return self::$twig->render(
            'payment.html.twig',
            [
                'alert' => $alert,
                'amount' => $_POST['amount'] ,
                'path' => self::path(),
                'user' => $_SESSION['user'], 
                'output' => $output
            ]
        );
    }

    public static  function amount(){
        $userServices = new UserService();
        self::init();
        $alert = false;
        $user = $userServices->autoRefresh($_SESSION['user']);

        return self::$twig->render(
            'amount.html.twig',
            [
                'alert' => $alert,
                'user' => $_SESSION['user']
            ]
        );
    }

}