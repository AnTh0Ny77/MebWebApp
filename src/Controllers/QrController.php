<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Controllers\BaseController;
use Src\Controllers\HomeController;
use Src\Services\AuthService;
use Src\Entities\User;
use Src\Services\UserService;
use Src\Services\QrService;
use DateTimeZone;
use DateTime;

class QrController extends BaseController{

    public static function path(){
        return 'qr';
    }

    public static function index(){
        $userServices = new UserService();
        self::init();
        $alert = false;
        $qr = false ;
        $time = 48;
        $user = $userServices->autoRefresh($_SESSION['user']);

        if (!empty($_POST['game']) && !empty($_POST['date'])) {
            $parisTimezone = new DateTimeZone('Europe/Paris');
            $parisTime = new DateTime('now', $parisTimezone);
            $parisTime->setTime($parisTime->format('H'), 0, 0); // Round down to the nearest hour
            $date = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['date'], $parisTimezone);
            if ($date->format('i') >= 30) {
                $date->modify('+1 hour')->setTime($date->format('H'), 0, 0); // Round up to the next hour
            } else {
                $date->setTime($date->format('H'), 0, 0); // Round down to the nearest hour
            }
            $diff = $parisTime->diff($date);
            $hours = $diff->h + ($diff->days * 24);
            if ($diff->i >= 30) {
                $hours++; // Round up to the next hour
            }
            $time = $hours;
           
           $qrService = new QrService();
           $qr = $qrService->getQr($user , $_POST['game'] , $time);
             

        if (intval($qr->getStatusCode()) != 200){
            if (intval($qr->getStatusCode()) == 407) {
                $alert = ["message" => "Le jeu n est pas disponible pour votre compte"];
            }elseif (intval($qr->getStatusCode()) == 408) {
                $alert = ["message" => "Solde en exploreCoin insuffisant"];
            }else{
                $alert = ["message" => "impossible de generer le qrcode"];
            }
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
            $user->setBagNumber(0);
        }else{
            $user->setBagNumber($client->bagNumber);
        }

        if (empty($client->exploreCoin)) {
            $user->setExploreCoin(0);
        }else{
            $user->setExploreCoin($client->exploreCoin);
        }

        if (!empty($user->getCoverPath())) {
            $cover = $userServices->getUserCover($user);
            $cover = 'data:image/png;base64,' . base64_encode($cover);
            $cover = '<figure class="image col-6 col-md-5 panel-image"><img class="user-image mx-auto"
            src="' .$cover . '" alt="Cover Image"></figure>';
        }else {
            $cover = null;
        }
       
        $_SESSION['user'] = $user;

        return self::$twig->render(
            'qr.html.twig',
            [
                'alert' => $alert,
                'path' => self::path(),
                'user' => $_SESSION['user'], 
                'qr' => $qr, 
                'cover' => $cover
            ]
        );
    }

    function diffToParisTimezone(DateTime $date): int {

        
    }
}