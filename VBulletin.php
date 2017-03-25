<?php


class VBulletin
{

    const URL_LOGIN = "http://www.forocoches.com/foro/login.php";

    private $username;
    private $password;
    private $passwordMd5;
    private $url;

    public function __construct($username, $password)
    {
        $this->username = trim($username);
        $this->password = trim($password);
        $this->url = self::URL_LOGIN;
    }


    public function login()
    {

        $this->passwordMd5 = md5($this->password);

        if (strpos($this->url, 'login.php') === false) {
            $this->url = (substr($this->url, -1) != '/') ? $this->url . '/' : $this->url;
            $this->url = $this->url . 'login.php?do=login';
        }

        $postfields = array(
            'vb_login_username' => $this->username,
            'vb_login_password' => '',
            'cookieuser' => 1,
            's' => '',
            'do' => 'login',
            'vb_login_md5password' => $this->passwordMd5,
            'vb_login_md5password_utf' => $this->passwordMd5
        );

        $httpheaders = array();
        $httpheaders[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $httpheaders[] = "Accept-Language: en-us,en;q=0.5";
        $httpheaders[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";

        // do login and fetch return (with header)
        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        //curl_setopt($curl, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.16) Gecko/20080718 Ubuntu/8.04 (hardy) Firefox/2.0.0.16');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheaders);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_REFERER, "http://www.forocoches.com");
        curl_setopt($curl, CURLOPT_COOKIEFILE, './' . $this->username . '.txt');
        curl_setopt($curl, CURLOPT_COOKIEJAR, './' . $this->username . '.txt');

        $curl_return = curl_exec($curl);

        curl_close($curl);
        $curl_return = explode("\n", $curl_return);

        $cookies = array();
        foreach (array_keys($curl_return) as $i) {
            if (stripos($curl_return[$i], 'set-cookie') !== false) {
                $cookies[] = substr($curl_return[$i], 12);
            }
        }
        unset($curl_return);

        // Preparar las cookies para config el usuario
        foreach ($cookies as $key => $value) {
            $cookies[$key] = explode(';', $value);
            array_walk($cookies[$key], create_function('&$temp', '$temp = trim($temp);'));
            foreach ($cookies[$key] as $inner_key => $inner_value) {
                $temp = explode('=', $inner_value);
                if (count($temp) == 2 && ($temp[0] == 'path' || $temp[0] == 'expires' || $temp[0] == 'domain')) {
                    $cookies[$key][$temp[0]] = $temp[1];
                } elseif (count($temp) == 2) {
                    $cookies[$key]['name'] = $temp[0];
                    $cookies[$key]['value'] = $temp[1];
                }

                if (count($temp) == 1 && $temp[0] == 'HttpOnly') {
                    $cookies[$key]['HttpOnly'] = true;
                } else {
                    $cookies[$key]['HttpOnly'] = false;
                }
                unset($cookies[$key][$inner_key]);
            }
        }

        $set_result = array();
        foreach ($cookies as $cookie) {
            $set_result[] = setcookie($cookie['name'], $cookie['value'], (isset($cookie['expires'])) ? strtotime($cookie['expires']) : 0, $cookie['path'], $cookie['domain'], false,
                $cookie['HttpOnly']);
        }
    }

}