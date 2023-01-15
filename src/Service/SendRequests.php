<?php

namespace App\Service;

class SendRequests
{
    public function curl($url,$method,$parameters) {
        $ch = curl_init();
        if(strtolower($method) == 'post') {
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($parameters));
        } else {
            $url = $url.http_build_query($parameters);
            curl_setopt($ch, CURLOPT_URL,$url);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
        return $server_output;
    }
}