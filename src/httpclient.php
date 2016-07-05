<?php

class HTTPClient {

    private $host;

    public function __construct($host) {
        $this->host = $host;
    }

    public function RunCommand($resource='/',$method='GET',$body='',$headers=[]) {
        $url = $this->host . $resource;
        $curl_response = $this->curl_command($url,$method,$body,$headers);
        return $curl_response;
    }

    private function curl_command($url='', $method='',$body='',$headers=[]) {
        $method = strtoupper($method);
        $ch = curl_init();
 
        if ($method != 'GET') {       
            if (substr($body,0,1) == '{') {
                $content_type = 'application/json';
            } else {
                $content_type = 'application/x-www-form-urlencoded';
            }
            $headers['Content-Type'] = $content_type;
        }

        if (!empty($headers)) {
            $allheaders = [];
            foreach ($headers as $key=>$value) {
                $allheaders[$key] = $key . ': ' . $value;
            }
            $headers = $allheaders;
        }


        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch,CURLOPT_USERAGENT,'curl'); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $result = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $resbody = substr($result, $header_size);
        $header = explode("\r\n",$header);
        $newheader['Response-Code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        foreach ($header as $ehead) {
            $ehead = explode(': ',$ehead);
            if (sizeof($ehead) == 2) {
                $key = $ehead[0];
                $value = $ehead[1];
                $newheader[$key] = $value;
            }
        }
        $response['header'] = $newheader;
        $response['body'] = $resbody;
        if (json_decode($resbody,true)) {
            $response['body'] = json_decode($resbody,true);
        }
        return $response;
    }
}
