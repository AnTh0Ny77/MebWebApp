<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Controllers\BaseController;
use Src\Services\AuthService;
use Src\Services\PayService;
use Src\Entities\User;
use Src\Services\UserService;

class StatController extends BaseController
{

   
    public static function path(){
        return 'stat';
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

        $client = $userServices->getAdminData($user);

        $cover_rank = $userServices->getRankCover($user);

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

        if (empty($client->bagNumber)) {
            $user->setBagNumber(14);
        }else{
            $user->setBagNumber($client->bagNumber);
        }

        if (empty($client->exploreCoin)) {
            $user->setExploreCoin(0);
        }else{
            $user->setExploreCoin($client->exploreCoin);
        }
        
       
        $_SESSION['user'] = $user;

        return self::$twig->render(
            'stat.html.twig',
            [
                'alert' => $alert,
                'cover_rank' => $cover_rank ,
                'rank_games' => $rank_games,
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
