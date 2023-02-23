<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Controllers\BaseController;
use Src\Services\AuthService;
use Src\Services\PayService;
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
        $cover_rank = false ;
        $rank_games = false;
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
                    $pay = new PayService();
                    $pay->PostTransac($user , $_SESSION['payment']['amount']);
                    $alert = [ 
                        "message" => "Bravo ! ".intval($_SESSION['payment']['amount']/100)." exploreCoins ont étés ajoutés a votre compte"
                    ];
                }
                $_SESSION['payment'] = "";
        }
        

        $client = $userServices->getAdminData($user);
        $cover_rank = $userServices->getRankCover($user);

        if (!empty($user->getCoverPath())) {
            $cover = $userServices->getUserCover($user);
            $cover = 'data:image/png;base64,' . base64_encode($cover);
            $cover = '<figure class="image col-6 col-md-5 panel-image"><img class="user-image mx-auto"
            src="' .$cover . '" alt="Cover Image"></figure>';
        }else {
            $cover = null;
        }
        

        if (!empty($client)) {
            $user->setClientInfiniteQr($client->clientInfiniteQr);
    
            $user->setClientGames($client->clientGames);
    
            $user->setTransac($client->transacs);

            foreach ($user->getTransac() as $value) {
                $time = strtotime($value->createdAt);
                $value->createdAt = date("d-m-Y", $time);
            }
            $user->setStats($client->stat);
            $stats = $userServices->handleStats($user);
            $rank_games = $stats['scan_total'];
            $user->setStats(json_encode($stats));
        }       

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
            
        $_SESSION['user'] = $user;
        $date = date("d-m-Y");
        return self::$twig->render(
            'home.html.twig',
            [
                'alert' => $alert,
                'cover_rank' => $cover_rank ,
                'date' => $date,
                'rank_games' => $rank_games,
                'path' => self::path(),
                'user' => $_SESSION['user'] , 
                'cover' => $cover
            ]
        );
    }

    public static function error404()
    {
        self::init();
    }
}
