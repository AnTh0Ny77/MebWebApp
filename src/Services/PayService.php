<?php
namespace Src\Services;

use Src\Entities\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Src\Services\MappingServices;
require  '././vendor/autoload.php';


class PayService { 
    public function __construct(){
        $this->Config = file_get_contents('globalConfig.json');
        $this->Config = json_decode($this->Config);
        $this->AuthService = new AuthService();
        $this->Client =  new Client(['base_uri' => $this->Config->AuthService->url ,'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
    }

    public function PostTransac( $user , $amount){

        try {
             $user = $this->Client->put('/api/user/'.$user->getId().'/coin',
             ['headers' => $this->AuthService->makeHeadersUser($user) , 
             'json' => ['amount' => $amount]]);
        } catch(ClientException $exeption) {$user = $exeption->getResponse();}
    
        return$user;
      
    }
    
}