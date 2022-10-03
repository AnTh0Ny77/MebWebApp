<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Controllers\BaseController;
use Src\Controllers\HomeController;
use Src\Services\AuthService;
use Src\Entities\User;
use Src\Services\UserService;
use Src\Services\QrService;

class QrController extends BaseController{

    public static function path(){
        return 'qr';
    }

    public static function index()
    {
        $userServices = new UserService();
        self::init();
        $alert = false;
        $qr = false ;

        $user = $userServices->autoRefresh($_SESSION['user']);

        if (!empty($_POST['game']) && !empty($_POST['time'])) {
           $qrService = new QrService();
           $qr = $qrService->getQr($user , $_POST['game']);
             

        if (intval($qr->getStatusCode()) != 200){
            $alert = ["message" => "impossible de generer le qrcode"];
            $qr = false ;
        }else { $qr = base64_encode($qr->getBody()->read(16384)); }
              
        }
        
        if (!$user instanceof User){
            $_SESSION['alert'] = $user;
            return IndexController::logout();
        }

        $client = $userServices->getAdminData($user);

        if (empty($client->clientGames)) {
            $_SESSION['alert'] = ['message' => 'Aucun jeux disponible , contactez myExplorebag'];
            return HomeController::index();
        }
       
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
            'qr.html.twig',
            [
                'alert' => $alert,
                'path' => self::path(),
                'user' => $_SESSION['user'], 
                'qr' => $qr
            ]
        );
    }

}