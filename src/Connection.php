<?php

namespace CodePhix\Asaas;

use stdClass;

class Connection {
    public $http;
    public $api_key;
    public $api_status;
    public $base_url;
    public $headers;
    public $user_agent;

    public function __construct($token, $status, $userAgent = false) {

        if($status == 'producao'){
            $this->api_status = false;
        }elseif($status == 'homologacao'){
            $this->api_status = 1;
        }else{
            die('Tipo de homologação invalida');
        }
        $this->api_key = $token;
        $this->user_agent = !$userAgent ? 'aplicacao-web' : $userAgent;
        $this->base_url = $this->api_status ? "https://sandbox.asaas.com/api/v3" : "https://api.asaas.com/v3";
        return $this;
    }


    public function get($url, $option = false, $custom = false )
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url . $url.$option);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        if(!empty($custom)){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $custom);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: ".$this->api_key,
            "User-Agent: " .$this->user_agent
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);

        if(empty($response)){
            $response = new stdClass();
            $response->error = [];
            $response->error[0] = new stdClass();
            $response->error[0]->description = 'Tivemos um problema ao processar a requisição.';
        }

        return $response;
    }

    public function post($url, $params)
    {
        $params = json_encode($params);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->base_url . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: ".$this->api_key,
            "User-Agent: " .$this->user_agent
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);

        if(empty($response)){
            $response = new stdClass();
            $response->error = [];
            $response->error[0] = new stdClass();
            $response->error[0]->description = 'Tivemos um problema ao processar a requisição.';
        }
        
        return $response;

    }
    
}
