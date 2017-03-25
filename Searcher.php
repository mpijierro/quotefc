<?php

class Searcher
{
    const FICHERO_INFO_HILOS = 'hilos.txt';
    const SEPARADOR = ':::';

    const MAX_LIMIT = 15;
    const MAX_HILOS = 10;

    private $threads = [];
    private $errors = [];

    public function getThreads()
    {
        return $this->threads;
    }

    public function configSearch()
    {
        if (isset($_POST['hilos'])) {
            $this->parserThreads(trim($_POST['hilos']));
        } else {
            $this->defaultConfig();
        }

    }

    private function parserThreads($threadArray = '')
    {
        $lineNumber = 1;
        $contentTextarea = array_slice(explode("\n", $threadArray, 0, 50));

        foreach ($contentTextarea AS $line) {

            try {

                $lineArray = explode(self::SEPARADOR, $line);

                $thread = new Thread();
                $thread->configFromArray($lineArray);
                $this->addThread($thread);

            } catch (Exception $e) {
                $this->errors [] = $e->getMessage() . ' - Line: ' . $lineNumber;
            }

            $lineNumber++;
        }

        $this->saveThreadInFile($this->threads);
    }

    private function defaultConfig()
    {

        $thread = new Thread();
        $thread->configFromRequest();

        $this->addThread($thread);
    }


    private function addThread(Thread $thread)
    {
        $this->threads[] = $thread;
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