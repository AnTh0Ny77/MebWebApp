<?php
namespace Src\Services;

use Src\Entities\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Src\Services\MappingServices;
require  '././vendor/autoload.php';


class QrService {

public function __construct(){
    $this->Config = file_get_contents('globalConfig.json');
    $this->Config = json_decode($this->Config);
    $this->AuthService = new AuthService();
    $this->Client =  new Client(['base_uri' => $this->Config->AuthService->url ,'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
}

public function getQr( $user , $game__id , $time){

    try {
        $user = $this->Client->post('/api/qr/create', ['headers' => $this->AuthService->makeHeadersUser($user) , 'json' => ['game' => $game__id , 'time' => intval($time) ]]);
    } catch(ClientException $exeption) {$user = $exeption->getResponse();}

    return $user;
  
}


}