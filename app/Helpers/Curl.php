<?php
namespace App\Helpers;

// use 

class Curl {

    public static function get($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "?" . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result === false) {
            $error = curl_error($ch);
            $response = json_encode(compact("error"));
        }
        else {
            $json = json_decode(curl_exec($ch));
            $mensaje = $json->respuesta;
            $response = json_encode(compact("mensaje"));
        }
        curl_close($ch);
        return json_decode($response);
    }

    public static function post($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        return $response;
    }

    public static function put($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        return $response;
    }
}