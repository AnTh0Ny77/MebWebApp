<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Controllers\BaseController;
use Src\Services\AuthService;
use Src\Entities\User;
use Src\Services\UserService;

class HomeController extends BaseController
{

   
    public static function path(){
        return 'home';
    }

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

        if (isset($_SESSION['alert'])) {
            $alert = $_SESSION['alert'];
            $_SESSION['alert'] = "";
        }

        //traitement du payment stripe :
        if (!empty($_GET['valid']) and isset($_SESSION['payment'])) {
                if (!empty($_SESSION['payment']['amount']) and  !empty($_SESSION['payment']['secret'])) {
                    //requete de payement
                    $alert = [ 
                        "message" => "Bravo ! ".intval($_SESSION['payment']['amount']/100)." exploreCoins ont étés ajoutés a votre compte"
                    ];
                }
                $_SESSION['payment'] = "";
        }
        
        $client = $userServices->getAdminData($user);

        $user->setClientInfiniteQr($client->clientInfiniteQr);

        $user->setClientGames($client->clientGames);
        
        $user->setStats($client->stat);

        $stats = $userServices->handleStats($user);

        $user->setStats($stats);

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
                'path' => self::path(),
                'user' => $_SESSION['user']
            ]
        );
    }

    public static function error404()
    {
        self::init();
    }
}
