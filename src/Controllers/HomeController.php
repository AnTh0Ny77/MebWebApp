<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Controllers\BaseController;
use Src\Services\AuthService;
use Src\Entities\User;
use Src\Services\UserService;

class HomeController extends BaseController
{

    public static function index()
    {
        $userServices = new UserService();
        self::init();
        $alert = false;

        $user = $userServices->autoRefresh($_SESSION['user']);
       
        if (!$user instanceof User){
            $_SESSION['alert'] = $user;
            return IndexController::logout();
        }
            
        $client = $userServices->getAdminData($user);
       
        $user->setClientInfiniteQr($client->clientInfiniteQr);
        $user->setClientGames($client->clientGames);
        if (empty($client->bagNumber)) {
            $user->setBagNumber(14);
        }else{
            $user->setBagNumber($client->bagNumber);
        }

        if (empty($client->exploreCoin)) {
            $user->setExploreCoin(450);
        }else{
            $user->setExploreCoin($client->exploreCoin);
        }
        
       
        $_SESSION['user'] = $user;

        return self::$twig->render(
            'home.html.twig',
            [
                'alert' => $alert,
                'user' => $_SESSION['user']
            ]
        );
    }

    public static function error404()
    {
        self::init();
    }
}
