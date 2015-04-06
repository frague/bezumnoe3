<?php
// version 03.12.2004
    class LiveinternetSeTracker {

        var $path   =   '/cgi-bin/robot.cgi';
        var $server     =   'host45.rax.ru';
        var $se     =   Array(

            'google'        =>  'Google',
            'yandex'        =>  'Yandex',
            'scooter'       =>  'AltaVista',
            'stack'         =>  'Rambler',
            'aport'         =>  'Aport',
            'lycos'         =>  'Lycos',
            'fast'          =>  'Fast Search',
            'rambler'       =>  'Rambler',
            );

        function liveinternetSeTracker($site_id) {
                        if (!isset($site_id)) exit ;
                        $this->siteid=$site_id;
            if(preg_match('/(google)|(yandex)|(scooter)|(stack)|(aport)|(lycos)|(fast)|(rambler)/msi',$_SERVER['HTTP_USER_AGENT'],$out))
            {
                $liveinternet_se        = $this->se[strtolower($out[0])];
                $url                    = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $liveinternet_post_data =   Array(
                'url'           =>  $url,
                'useragent'     =>  $liveinternet_se,
                'site'          =>  $this->siteid,
                );

                $this->PostToHost($this->server,$this->path,$this->URLEncodeArray($liveinternet_post_data)); 


            } 
            

        }
        function URLEncodeArray($QueryVars) { 
            unset($QueryBits); 
            while (list($var, $value) = each($QueryVars)) { 
                $QueryBits[] = urlencode($var).'='.urlencode($value); 
            } 
            return( implode('&', $QueryBits) ); 
        } 

        function PostToHost($host, $path, $data_to_send, $port=80, $proto="1.0") { 
            $rval           = -1; 
            $data_len       = strlen($data_to_send); 
            $fp             = fsockopen($host, $port); 
    
            if ($fp) { 
                fputs($fp, "POST $path HTTP/$proto\r\n"); 
                fputs($fp, "Host: $host\r\n"); 
                fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
                fputs($fp, "Content-length: ".$data_len."\r\n"); 
                fputs($fp, "Connection: close\r\n\r\n"); 
                fputs($fp, $data_to_send); 
                while(!feof($fp)) { $rval .= fgets($fp, 128); } 
                fclose($fp); 
            } 
        return($rval); 
        } 
     }
    new LiveinternetSeTracker("bezumnoe.ru");
?>