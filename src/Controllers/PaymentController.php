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

        
        \Stripe\Stripe::setApiKey('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');
       
        
        try {
        
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => intval($_POST['amount']),
                'currency' => 'eur'
            ]);
        
            echo json_encode(['status' => 'success', 'paymentIntent' => $paymentIntent]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            echo json_encode(['error' => 'Erreur Stripe : ' . $e->getMessage()]);
            die();
        } catch (Exception $e) {
            echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
            die();
        }

       

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

        if (!empty($user->getCoverPath())) {
            $cover = $userServices->getUserCover($user);
            $cover = 'data:image/png;base64,' . base64_encode($cover);
            $cover = '<figure class="image col-6 col-md-5 panel-image"><img class="user-image mx-auto"
            src="' .$cover . '" alt="Cover Image"></figure>';
        }else {
            $cover = null;
        }
        

        return self::$twig->render(
            'payment.html.twig',
            [
                'alert' => $alert,
                'amount' => $_POST['amount'] ,
                'path' => self::path(),
                'user' => $_SESSION['user'], 
                'output' => $output , 
                'cover' => $cover
            ]
        );
    }

    public static  function amount(){
        $userServices = new UserService();
        self::init();
        $alert = false;
        $user = $userServices->autoRefresh($_SESSION['user']);

        if (!empty($user->getCoverPath())) {
            $cover = $userServices->getUserCover($user);
            $cover = 'data:image/png;base64,' . base64_encode($cover);
            $cover = '<figure class="image col-6 col-md-5 panel-image"><img class="user-image mx-auto"
            src="' .$cover . '" alt="Cover Image"></figure>';
        }else {
            $cover = null;
        }

        return self::$twig->render(
            'amount.html.twig',
            [
                'alert' => $alert,
                'user' => $_SESSION['user'] , 
                'cover' => $cover
            ]
        );
    }

}