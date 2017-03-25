<?php
$hilos = array();
if (isset($_POST['hilos'])) {

    $num_linea = 1;
    $contenido_textarea = array_slice(explode("\n", trim($_POST['hilos'])), 0, 50);

    foreach ($contenido_textarea AS $linea_bruta) {

        $busqueda = array();
        $busqueda_correcta = true;
        $array_linea = explode(SEPARADOR, $linea_bruta);

        if ( ! empty($array_linea[0])) {
            $busqueda['hilo'] = trim($array_linea[0]);
        } else {
            $busqueda_correcta = false;
        }

        if (( ! empty($array_linea[1])) AND ($busqueda_correcta)) {
            $busqueda['usuario'] = trim($array_linea[1]);
        } else {
            $busqueda_correcta = false;
        }

        if ((isset($array_linea[2])) AND (is_numeric($array_linea[2])) AND ($busqueda_correcta)) {
            $busqueda['inicio'] = $array_linea[2];
        } else {
            $busqueda['inicio'] = 1;
        }

        if ((isset($array_linea[3])) AND (is_numeric($array_linea[3])) AND ($busqueda_correcta)) {
            $busqueda['final'] = $array_linea[3];
        } else {
            $busqueda['final'] = NUM_PAGINAS;
        }

        if (( ! empty($array_linea[4])) AND ( ! empty($array_linea[5])) AND ($busqueda_correcta)) {
            $busqueda['user'] = trim($array_linea[4]);
            $busqueda['clave'] = trim($array_linea[5]);
        } else {
            $busqueda['user'] = '';
            $busqueda['clave'] = '';
        }

        if ($busqueda_correcta) {
            $hilos[] = $busqueda;
        } else {
            $hilos[] = "Error leyendo información de la línea " . $num_linea;
        }

        $num_linea++;
    }

    guardar_hilos_en_archivo($hilos);


} else {
    $hilos[] = array(
        'hilo' => trim($_POST['hilo']),
        'usuario' => trim($_POST['usuario']),
        'inicio' => trim($_POST['inicio']),
        'final' => trim($_POST['final']),
        'user' => trim($_POST['user']),
        'clave' => trim($_POST['clave'])
    );
}
?>