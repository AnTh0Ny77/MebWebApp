<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Controllers\BaseController;
use Src\Controllers\HomeController;
use Src\Services\AuthService;
use Src\Entities\User;
use Src\Services\UserService;
use Src\Services\QrService;
use Src\Services\LocationsService;
use DateTimeZone;
use DateTime;

class Qr2Controller extends BaseController{

    public static function path(){
        return 'qr2';
    }

    public static function index(){
        $userServices = new UserService();
        $locationService = New LocationsService();
        self::init();
        $alert = false;
        $qr = false ;
        $tempGame = false ;
        $tempDate= false ;
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



        ////location maj //////////////
        if (!empty($_GET['default'])) {
             $defaultL = [
                'default' => $_GET['default']
             ] ;
             $defaultLocation = $locationService->defaultLocation( $user , $defaultL );
            $tempGame = false ;
            if (!empty($_GET['date'])) {
                $tempDate= $_GET['date'] ;
            }
            if (!empty($_GET['game'])) {
                $tempGame= $_GET['game'] ;
            }
           
        }

        //////supression////////////////////
        if (!empty($_GET['delete'])) {
             $bodyDelete = [
                'delete' => $_GET['delete']
             ] ;
            $deleteLocation = $locationService->postLocation( $user , $bodyDelete );
        }

         ///clientLocationPost///////
        if (!empty($_POST['textColumn']) && !empty($_POST['JsonColumn'])) {
            if (isset($_POST['isActive']) && $_POST['isActive'] == 'on') {
               $isActive = 1;
            } else {
               $isActive = 0;
            }

            list($lat, $lng) = explode(",", $_POST['JsonColumn']);

            // Créer un tableau associatif
            $jsonArray = array(
                "lat" => floatval($lat),
                "lng" => floatval($lng)
            );

            $jsonString = json_encode($jsonArray);

          

            $location = [
                'textColumn' => $_POST['textColumn'] , 
                'postal' => $_POST['postal'] , 
                'booleanColumn' => $isActive , 
                'jsonColumn' =>  [$jsonString]
            ];
               
            $postLocation = $locationService->postLocation( $user , $location );
            
           
            if ($postLocation->message != 'ok'){
                $alert = ["message" => "un problème est survenu pendant la création code erreur: " . $postLocation ];   
            }     
        }

        //clientlocationEdit///////////////////////////////////////////
        if (!empty($_POST['textColumnM']) && !empty($_POST['locationId'])) {
            if (isset($_POST['isActiveM']) && $_POST['isActiveM'] == 'on') {
               $isActive = 1;
            } else {
               $isActive = 0;
            }
            list($lat, $lng) = explode(",", $_POST['JsonColumnM']);

            // Créer un tableau associatif
            $jsonArray = array(
                "lat" => floatval($lat),
                "lng" => floatval($lng)
            );

            $jsonString = json_encode($jsonArray);
            $location = [
                'id' => $_POST['locationId'] ,
                'textColumn' => $_POST['textColumnM'] , 
                'postal' => $_POST['postalM'] , 
                'booleanColumn' => $isActive , 
                'jsonColumn' =>  [$jsonString]
            ];
            $postLocation = $locationService->postLocation( $user , $location );
            
            if ($postLocation->message != 'ok'){
                $alert = ["message" => "un problème est survenu pendant la création code erreur: " . $postLocation ];   
            }     
        }



        $listLocationRequest = $locationService->getLocations( $user );
        $listLocation = [];
        foreach ($listLocationRequest as $key => $location) {


            $listLocation[$key]['id'] = $location->id;
            $listLocation[$key]['textColumn'] = $location->textColumn;
            $listLocation[$key]['jsonColumn'] = $location->jsonColumn;
            if (is_array($location->jsonColumn)) {
                $temp = json_decode($location->jsonColumn[0]);
                $listLocation[$key]['jsonColumn'] = $temp;
                $listLocation[$key]['lat'] = $temp->lat;
                $listLocation[$key]['lng'] = $temp->lng;
             
            }else{
                 $listLocation[$key]['lat'] = $location->jsonColumn->lat;
                 $listLocation[$key]['lng'] = $location->jsonColumn->lng;
             }

            $listLocation[$key]['postal'] = $location->postal;
            $listLocation[$key]['booleanColumn'] = $location->booleanColumn;
            $listLocation[$key]['value'] = json_encode($location);
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
            'qr2.html.twig',
            [
                'alert' => $alert,
                'path' => self::path(),
                'user' => $_SESSION['user'], 
                'qr' => $qr, 
                'cover' => $cover , 
                'list_location' =>  $listLocation  , 
                'tempGame' => $tempGame , 
                'tempDate' => $tempDate
            ]
        );
    }

    function diffToParisTimezone(DateTime $date): int {

        
    }
}