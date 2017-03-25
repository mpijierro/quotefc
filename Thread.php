<?php

class Thread
{
    const FICHERO_INFO_HILOS = 'hilos.txt';
    const SEPARADOR = ':::';
    const NUM_PAGINAS = 10;
    const MAX_LIMIT = 15;
    const MAX_HILOS = 10;
    const POSITION_THREAD = 0;
    const POSITION_USER_SEARCHED = 1;
    const POSITION_START = 2;
    const POSITION_END = 3;
    const USER_LOGIN = 4;
    const USER_PASSWORD = 5;
    const PAGE_INITIAL = 1;

    private $threads = [];

    public function getThreads()
    {
        return $this->threads;
    }

    public function configSearch()
    {
        if (isset($_POST['hilos'])) {
            $this->parserThreads();

        } else {
            $this->defaultConfig();
        }

    }

    private function parserThreads()
    {
        $lineNumber = 1;
        $contentTextarea = array_slice(explode("\n", trim($_POST['hilos'])), 0, 50);

        foreach ($contentTextarea AS $line) {

            $busqueda = array();
            $busqueda_correcta = true;
            $lineArray = explode(self::SEPARADOR, $line);

            if ( ! empty($lineArray[self::POSITION_THREAD])) {
                $busqueda['hilo'] = $this->sanitize($lineArray[self::POSITION_THREAD]);
            } else {
                $busqueda_correcta = false;
            }

            if (( ! empty($lineArray[1])) AND ($busqueda_correcta)) {
                $busqueda['usuario'] = $this->sanitize($lineArray[self::POSITION_USER_SEARCHED]);
            } else {
                $busqueda_correcta = false;
            }

            if ((isset($lineArray[self::POSITION_START])) AND (is_numeric($lineArray[self::POSITION_START])) AND ($busqueda_correcta)) {
                $busqueda['inicio'] = $lineArray[self::POSITION_START];
            } else {
                $busqueda['inicio'] = self::PAGE_INITIAL;
            }

            if ((isset($lineArray[self::POSITION_END])) AND (is_numeric($lineArray[self::POSITION_END])) AND ($busqueda_correcta)) {
                $busqueda['final'] = $lineArray[self::POSITION_END];
            } else {
                $busqueda['final'] = self::NUM_PAGINAS;
            }

            if (( ! empty($lineArray[self::USER_LOGIN])) AND ( ! empty($lineArray[self::USER_PASSWORD])) AND ($busqueda_correcta)) {
                $busqueda['user'] = $this->sanitize($lineArray[self::USER_LOGIN]);
                $busqueda['clave'] = $this->sanitize($lineArray[self::USER_PASSWORD]);
            } else {
                $busqueda['user'] = '';
                $busqueda['clave'] = '';
            }

            if ($busqueda_correcta) {
                $this->threads[] = $busqueda;
            } else {
                $this->threads[] = "Error leyendo información de la línea " . $lineNumber;
            }

            $lineNumber++;
        }

        $this->saveThreadInFile($this->threads);
    }

    private function defaultConfig()
    {
        $this->threads[] = array(
            'hilo' => $this->sanitize($_POST['hilo']),
            'usuario' => $this->sanitize($_POST['usuario']),
            'inicio' => $this->sanitize($_POST['inicio']),
            'final' => $this->sanitize($_POST['final']),
            'user' => $this->sanitize($_POST['user']),
            'clave' => $this->sanitize($_POST['clave'])
        );
    }

    private function sanitize($value)
    {
        return trim($value);
    }

    public function savePrivateData($campo)
    {

        if (($campo != 'user') AND ($campo != 'clave')) {
            return true;
        } elseif (GUARDAR_ACCESO) {
            return true;
        }

        return false;
    }

    /**
     * Info de los hilos introducidos en el textarea
     *
     */
    public function saveThreadInFile($hilos)
    {

        if (file_exists(self::FICHERO_INFO_HILOS)) {
            unlink(self::FICHERO_INFO_HILOS);
        }

        $fp = fopen(self::FICHERO_INFO_HILOS, "w+");

        foreach ($hilos AS $hilo) {

            $linea = '';
            foreach ($hilo AS $campo => $dato) {
                if ($this->savePrivateData($campo)) {
                    $linea .= trim($dato) . SEPARADOR;
                }
            }
            fwrite($fp, $linea . PHP_EOL);

        }
        chmod(FICHERO_INFO_HILOS, 0777);
        fclose($fp);
    }


    public function retrieveThreadsFromFile()
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


}