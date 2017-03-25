<?php

/**
 * Loguea un usuario en un foro vBulletin
 * @param string $vb_username
 * @param string $vb_password
 * @param string $vb_url
 */
function login_vbulletin($vb_username, $vb_password, $vb_url)
{

    $vb_username = trim($vb_username);
    $vb_password = trim($vb_password);
    $vb_password_md5 = md5($vb_password);

    if (strpos($vb_url, 'login.php') === false) {
        $vb_url = (substr($vb_url, -1) != '/') ? $vb_url . '/' : $vb_url;
        $vb_url = $vb_url . 'login.php?do=login';
    }

    $postfields = array(
        'vb_login_username' => $vb_username,
        'vb_login_password' => '',
        'cookieuser' => 1,
        's' => '',
        'do' => 'login',
        'vb_login_md5password' => $vb_password_md5,
        'vb_login_md5password_utf' => $vb_password_md5
    );

    $httpheaders = array();
    $httpheaders[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
    $httpheaders[] = "Accept-Language: en-us,en;q=0.5";
    $httpheaders[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";

    // do login and fetch return (with header)
    $curl = curl_init($vb_url);
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.16) Gecko/20080718 Ubuntu/8.04 (hardy) Firefox/2.0.0.16');
    curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheaders);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_REFERER, "http://www.forocoches.com");
    curl_setopt($curl, CURLOPT_COOKIEFILE, './' . $vb_username . '.txt');
    curl_setopt($curl, CURLOPT_COOKIEJAR, './' . $vb_username . '.txt');

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
        $set_result[] = setcookie($cookie['name'], $cookie['value'], (isset($cookie['expires'])) ? strtotime($cookie['expires']) : 0, $cookie['path'], $cookie['domain'], false, $cookie['HttpOnly']);
    }
}

/**
 *
 */
function guardar_datos_privados($campo)
{

    if (($campo != 'user') AND ($campo != 'clave')) {
        return true;
    } elseif (GUARDAR_ACCESO) {
        return true;
    } else {
        return false;
    }
}


/**
 * Info de los hilos introducidos en el textarea
 * @param array $hilos
 */
function guardar_hilos_en_archivo($hilos)
{

    if (file_exists(FICHERO_INFO_HILOS)) {
        unlink(FICHERO_INFO_HILOS);
    }

    $fp = fopen("hilos.txt", "w+");
    foreach ($hilos AS $hilo) {

        $linea = '';
        foreach ($hilo AS $campo => $dato) {
            if (guardar_datos_privados($campo)) {
                $linea .= trim($dato) . SEPARADOR;
            }
        }
        fwrite($fp, $linea . PHP_EOL);

    }
    chmod(FICHERO_INFO_HILOS, 0777);
    fclose($fp);
}


function obtener_hilos_desde_archivo()
{

    if ( ! file_exists(FICHERO_INFO_HILOS)) {
        return;
    }

    $fp = fopen(FICHERO_INFO_HILOS, "r");
    $hilos = '';
    while ( ! feof($fp)) {
        $hilos .= fgets($fp);
    }
    fclose($fp);

    return $hilos;

}

?>