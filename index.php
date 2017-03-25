<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es">
<head>
    <title>Citanding FC 1.0</title>
    <meta name="Description" content="Buscador de citas a tus posts en Forocoches"/>
    <meta name="robots" content="noarchive,noindex,nofollow"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" href="estilos.css" type="text/css" media="screen" charset="utf-8"/>
</head>
<body>
<?php
error_reporting(0);

include('constantes.php');
include('funciones.php');

if ($_POST) {

    include('gestion_hilos.php');

    $error = false;

    foreach ($hilos AS $busqueda) {

        ?>
        <div class="resultado">

        <div class="resultado_datos">

            <br/>HILO: <strong><?php echo $busqueda['hilo'] ?> </strong>
            <br/>USUARIO: <strong><?php echo $busqueda['usuario'] ?></strong>
            <br/>PÁGINA INICIO: <strong><?php echo $busqueda['inicio'] ?></strong>
            <br/>PÁGINA FINAL: <strong><?php echo $busqueda['final'] ?></strong>
        </div>

        <?php

        // ******************************************
        // 1) Comprobar que la url es de Forocoches
        // ******************************************
        $url_origen = $busqueda['hilo'];
        $patron = "/^(http:\/\/www.forocoches.com)/i";

        if (( ! preg_match($patron, $url_origen, $salida)) OR (empty($busqueda['hilo']))) {
            $error = true;
            ?>
            <span class="resultados no_citado alone">
			    <img src="gordo.jpg"/> ¡¡ NO has indicado una página de Forocoches !!<br/>
			</span>
            <?php
        }

        if ( ! $error) {

            // ******************************************
            // 2) Obtener el id del hilo
            // ******************************************
            $url_origen = $busqueda['hilo'];
            $patron = "/(t|p)=([0-9]+)/";

            if ( ! preg_match($patron, $url_origen, $hilo)) {
                $error = true;
                ?>
                <span class="resultados no_citado alone">
				    <img src="gordo.jpg"/>¡¡ NO se ha encontrado un indicador de hilo correcto !!<br/>
				</span>
                <?php
            }
        }

        if ( ! $error) {

            // ******************************************
            // 2.2) Obtener el user a buscar
            // ******************************************
            if (empty($busqueda['usuario'])) {
                $error = true;
                ?>
                <span class="resultados no_citado alone">
				    <img src="gordo.jpg"/> ¡¡ NO has indicado un usuario para buscar !!
				</span>
                <?php
            }
        }

        if ( ! $error) {

            $id_hilo = $hilo[1] . "=" . $hilo[2];

            /** ********************************************************************** */
            /** *************************INICIO PROCESO******************************* */
            /** ********************************************************************** */

            $url_origen = "http://www.forocoches.com/foro/showthread.php?" . $id_hilo;

            // ***********************************************
            //3) Obtener números de página de inicio y fin
            // ***********************************************
            $inicio = $busqueda['inicio'];
            $fin = $busqueda['final'];

            if (( ! empty($inicio)) AND (is_numeric($inicio)) AND ($inicio) AND ($inicio < 54)) {
                $pagina = $inicio;
            } else {
                $pagina = 1;
            }

            if ((empty($fin)) OR ( ! is_numeric($fin)) OR ( ! $fin) OR ($fin > 54) OR ($pagina > $fin) OR (($fin - $pagina) > NUM_PAGINAS)) {
                $fin = $pagina + NUM_PAGINAS;
            }

            $final = false;
            $ultimo_tamanio = 0;
            ?>
            <?php
            // ************************************************************
            //4) Obtener usuario y contraseñaa. Crear conexión con el foro
            // ************************************************************
            if (( ! empty($busqueda['user'])) AND ( ! empty($busqueda['clave']))) {
                login_vbulletin($busqueda['user'], $busqueda['clave'], URL_LOGIN);

                if (( ! empty($busqueda['user'])) AND (file_exists($busqueda['user'] . '.txt'))) {
                    chmod($busqueda['user'] . ".txt", 0777);
                }
            }

            $nombre_cookie = './' . $busqueda['user'] . '.txt';
            $num_citaciones = 0;
            $max_limit = 0;

            while ( ! $final) {

                ob_start();

                $url_busqueda = $url_origen . "&page=" . $pagina;

                //obtener contenido de la página
                $s = curl_init($url_busqueda);
                curl_setopt($s, CURLOPT_HEADER, true);
                curl_setopt($s, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($s, CURLOPT_COOKIEFILE, $nombre_cookie);
                curl_setopt($s, CURLOPT_COOKIEJAR, $nombre_cookie);
                curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
                $contenido = curl_exec($s);

                if ($ultimo_tamanio == strlen($contenido)) {
                    $final = true;
                } else {

                    $ultimo_tamanio = strlen($contenido);

                    // Comprobar que  la página tiene posts
                    $patron = "/<div id=\"posts\">/";

                    if ( ! preg_match($patron, $contenido, $salida)) {
                        ?>
                        <span class="resultados no_citado">
					    ¡¡ No se han encontrado post en la página indicada !! <img src="gordo.jpg"/>
					</span>
                        <?php
                        $final = true;
                    } else {

                        // 1) Extraemos los posts de la página
                        $patron = "#<table id=\"post([0-9]+)\"#i";
                        $division = preg_split($patron, $contenido);

                        $citado = false;

                        foreach ($division AS $indice => $contenido) {

                            // 2) Obtenemos el id del post
                            $patron = "#<div id=\"post_message_([0-9]+)\">#i";
                            $patron = '#post([0-9]+)#i';
                            if (preg_match_all($patron, $contenido, $salida_posts)) {

                                $id_post = $salida_posts[1][0];

                                // 3) Buscamos el usuario en alguna cita
                                $patron = "/<b>" . $busqueda['usuario'] . "<\/b>/i";
                                if (preg_match_all($patron, $contenido, $salida_posts)) {

                                    $citado = true;
                                    $num_citaciones++;
                                    $url_post = $url_busqueda . "#post" . $id_post;
                                    ?>
                                    <span class="resultados citado">
							    <img src="roto2.png"/>&nbsp;&nbsp;
                                        <?php echo $busqueda['usuario'] ?> ha sido CITADO en la página <strong><?php echo $pagina ?></strong>
							    <a href="<?php echo $url_post ?>" target="_blank" style="color:green;font-weight:bold;" title="Ver el post">VER POST</a>
							</span>
                                    <?php
                                }
                            }
                        }

                        if ( ! $citado) {
                            ?>
                            <span class="resultados no_citado">
						<?php echo $busqueda['usuario'] ?> NO ha sido citado en la página <strong><?php echo $pagina ?></strong>
						</span>
                            <?php
                        }
                    }

                    $pagina++;
                    $max_limit++;

                    if (($pagina > $fin) OR ($max_limit > MAX_LIMIT)) {
                        $final = true;
                    }
                }
                ob_end_flush();
                flush();
            }

            if (($max_limit) AND ( ! $num_citaciones)) {
                ?>
                <span class="resultados no_citado alone">
				    NO has sido citado en ningún post ... <img src="alone.png"/>
				</span>
                <?php
            }


            if (( ! empty($busqueda['user'])) AND (file_exists($busqueda['user'] . '.txt'))) {
                unlink($busqueda['user'] . '.txt');
            }
            ?>

            </div>
            <?php
        }
    }
}

$contenido_textarea = obtener_hilos_desde_archivo();

?>

<form name="form_fc" action="" method="post" class="form">

    <div class="titulo">
        <span class="script">Citanding FC v.0.1</span>
        ¿Te crees que has escrito un post mítico?...¿quieres saber si te han citado en alguna parte de un hilo?...Tus opiniones son tomadas
        en cuenta o por el contrario eres considerado un troll más y no pintas nada en los hilos. Para descubrir todo esto, aquí tienes una herramienta que puede servirte.<br>
        Y sobre todo, porque citar es nuestra costumbre y además de respetarla, hay que mantenerla. Además, puede fomentar el diálo, el debate e incluso el trolleo en Forocoches.com.
        ¡¡Disfruten lo citado y lo programado...o lo faileado!!.
    </div>

    <div class="campo largo">
        <span>HILO</span>
        <input type="text" name="hilo" class="largo" value=""/>
        <img src='ayuda.png' title="Copia aquí la dirección completa del hilo de forocoches en el cual quieras buscar al usuario citado"
             alt="Copia aquí la direcciín completa del hilo de forocoches en el cual quieras buscar al usuario citado"
        />
    </div>
    <div class="campo">
        <span>USUARIO BúSQUEDA</span>
        <input type="text" class="medio" name="usuario" value=""/>
        <img src='ayuda.png' title="Indica el usuario que quieras buscar para ver si ha sido citado"
             alt="Indica el usuario que quieras buscar para ver si ha sido citado"
        />
    </div>
    <div class="campo corto">
        <span>PÁGINA INICIO</span>
        <input type="text" class="corto" name="inicio" maxlength="2"/>
        <img src='ayuda.png' title="Indica la página de inicio para iniciar la búsqueda"
             alt="Indica la página de inicio para iniciar la búsqueda"
        />
    </div>
    <div class="campo corto">
        <span>PÁGINA FINAL</span>
        <input type="text" class="corto" name="final" maxlength="2"/>
        <img src='ayuda.png' title="Indica la página de final. En cualquier caso, la búsqueda funcionará en 10 páginas como máximo."
             alt="Indica la página de final. En cualquier caso, la búsqueda funcionará en 10 páginas como máximo."
        />
    </div>

    <div class="usuario_registrado">
        <div class="div_aviso">
            <p>El <strong>uso de estos campos privados es opcional</strong> para los hilos públicos, pero necesarios para poder rastrear citas en hilos +18,+HD,+PRV...</p>
        </div>
        <div class="campo medio">
            <span class="privado">USUARIO FC</span>
            <input type="text" class="medio" name="user"/>
            <img src='ayuda.png' title="Indica tu usuario de forocoches" alt="Indica tu usuario de forocoches"
            />
        </div>
        <div class="campo medio">
            <span class="privado">CLAVE FC</span>
            <input type="password" class="medio" name="clave"/>
            <img src='ayuda.png' title="Indica tu clave forocochera" alt="Indica tu clave forocochera"
            />
        </div>
    </div>
    <div class="campo boton">
        <input type="submit" value="Buscar"/>
    </div>
</form>
</br>

<form name="form_fc" action="" method="post" class="form">
    <p><strong>RASTREO DE MÚLTIPLES HILOS</strong></p>

    <div class="usuario_registrado rastreo_multiple">
        <div class="div_aviso">
            <p>Es posible rastrear múltiples hilos para así no tener que ir buscando uno por uno. Generará un archivo llamado por defecto 'hilos.txt' para ir almacenando los hilos a revisar.
                Para ello, es necesario rellenar cada fila del siguiente textareas siguiente el formmato sigueinte:</p>
            <p><strong>URL del hilo<?php echo SEPARADOR ?>usuario<?php echo SEPARADOR ?>inicio<?php echo SEPARADOR ?>final<?php echo SEPARADOR ?>usuario_acceso<?php echo SEPARADOR ?>
                    clave_acceso</strong>
                <br/><br/>
                Ejemplo:<br/>
                http://www.forocoches.com/foro/showthread.php?t=3191579<?php echo SEPARADOR ?>hilario<?php echo SEPARADOR ?>1<?php echo SEPARADOR ?>10<?php echo SEPARADOR ?>
                hilario<?php echo SEPARADOR ?>athenea
            </p>

            <p>Los <strong>datos privados no se guardan por defecto</strong> en ningún fichero. Si quieres que queden almacenados deberás cambiar en el fichero <i>constantes.php</i> la constante <i>GUARDAR_ACCESO</i>
                con valor TRUE</p>
        </div>

        <textarea name="hilos" rows="10" cols="100" style="font-size:10px;"><?php echo $contenido_textarea ?></textarea>
        <br/>
        <div class="campo boton">
            <input type="submit" value="Añadir y buscar"/>
        </div>
    </div>
</form>


<form name="aviso" action="" method="post" class="form">
    <p><strong>INFO</strong></p>

    <div class="usuario_registrado aviso_cop">
        <div class="div_aviso">
            <p>La finalidad y el objetivo de implementar este script ha sido meramente académica.</p>
            <p>La responsabilidad final sobre el uso de este script y sus consecuencias recaen únicamente en aquella persona que lo ejecute.</p>
            <p>Este código tiene licencia <a href="https://es.wikipedia.org/wiki/GNU_General_Public_License" target="_blank">GNU</a> por lo que
                garantiza a los usuarios finales (personas, organizaciones, compañías) la libertad de usar, estudiar, compartir (copiar) y modificar el software. Su propósito es declarar que el
                software cubierto por esta licencia es software libre y protegerlo de intentos de apropiación que restrinjan esas libertades a los usuarios.
                (según la Wikipedia :) ).
            </p>
            <br/>
            <p><strong>por <a href="http://www.forocoches.com/foro/member.php?u=541527" target="_blank">antares</a></strong></p>
        </div>
    </div>
</form>

</body>