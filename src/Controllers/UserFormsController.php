<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Controllers\BaseController;
use Src\Controllers\HomeController;
use Src\Services\AuthService;
use Src\Entities\User;
use Src\Services\UserService;
use Src\Services\QrService;

class UserFormsController extends BaseController{

    public static function path(){
        return 'userforms';
    }

    public static function index(){
        $userServices = new UserService();
        self::init();
        $alert = false;
        $qr = false ;
        $cover =

        $user = $userServices->autoRefresh($_SESSION['user']);

        if (!$user instanceof User){
            $_SESSION['alert'] = $user;
            return IndexController::logout();
        }

        if ($user->getId() != 914) {
            return IndexController::logout();
        }

        $client = $userServices->getAdminData($user);

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
        
       
        
        if (!empty($_POST['username']) and !empty($_POST['email']) and !empty($_POST['location']) and !empty($_POST['password'])) {
            $body = [
                'username' => $_POST['username'] , 
                'email' => $_POST['email'] ,
                'location' => $_POST['location'],
                'password' => $_POST['password'] ,
                'phone' => $_POST['phone'] , 
                'type' => $_POST['type'] , 
                'bag' => $_POST['bag'],
                'exc' => $_POST['exc']
            ];
            $post = $userServices->postClient($user , $body);
            
            $email = $post->message;
            
            if (!empty($_FILES)){
                $fileName = $_FILES['cover']['name'];
                $tempPath = $_FILES['cover']['tmp_name'];
                $fileSize = $_FILES['cover']['size'];
                if ($fileSize > 111) {
                    move_uploaded_file($tempPath, __DIR__ .'/' .$fileName);
                    $file = $userServices->postFile($user, fopen(__DIR__ . '/' .$fileName , 'r') ,$email);
                    unlink(__DIR__ .'/' .$fileName);
                }
            }
            header('location user');
        }

        return self::$twig->render(
            'userforms.html.twig',
            [
                'alert' => $alert,
                'path' => self::path(),
                'user' => $_SESSION['user'], 
                'cover' => $cover
            ]
        );
    }

}