<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Controllers\BaseController;
use Src\Services\AuthService;
use Src\Services\PayService;
use Src\Entities\User;
use Src\Services\UserService;

class PasswordChangeController extends BaseController
{

    public static function path(){
        return 'pass';
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

        $client = $userServices->getAdminData($user);
        $user->setClientInfiniteQr($client->clientInfiniteQr);
        $user->setClientGames($client->clientGames);
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

        if (!empty($user->getCoverPath())) {
            $cover = $userServices->getUserCover($user);
            $cover = 'data:image/png;base64,' . base64_encode($cover);
            $cover = '<figure class="image col-6 col-md-5 panel-image"><img class="user-image mx-auto"
            src="' .$cover . '" alt="Cover Image"></figure>';
        }else {
            $cover = null;
        }

        //traitement du changement de mot de passe  :
        if (!empty($_POST['actual_password']) and !empty($_POST['password'])) {
            $body = [
                "password" => $_POST['password'] , 
                "actual_password" => $_POST['actual_password']
            ];
            $responsePassword = $userServices->updatePassword($user , $body);
            
            if (!empty($responsePassword->error)){
                $alert = ["message" => $responsePassword->error];
            }else {
                $alert = ["message" => "Mot de passe mis à jour avec succès"];
            }
        }
        
       
        $_SESSION['user'] = $user;

        return self::$twig->render(
            'password.html.twig',
            [
                'alert' => $alert,
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
