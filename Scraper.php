<?php


class Scraper
{
    const LAST_PAGE_THREAD = 54;

    private $thread;
    private $originUrl = '';
    private $firstPage = '';
    private $lastPage = 1;
    private $page = 1;
    private $pages = [];
    private $cookieName = '';

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }


    public function getPages()
    {
        return $this->pages;
    }

    private function configOriginUrl()
    {
        $this->originUrl = "http://www.forocoches.com/foro/showthread.php?" . $this->thread->retrieveBuildIdThread();
    }

    private function configInitialAndEndPages()
    {

        $this->firstPage = $this->thread->getFirstPage();
        $this->lastPage = $this->thread->getLastPage();

        if ($this->firstPage < self::LAST_PAGE_THREAD) {
            $this->page = $this->firstPage;
        }

        if ((empty($this->lastPage)) OR
            ( ! is_numeric($this->lastPage)) OR
            ( ! $this->lastPage) OR
            ($this->lastPage > self::LAST_PAGE_THREAD) OR
            ($this->page > $this->lastPage) OR
            (($this->lastPage - $this->page) > NUM_PAGINAS)
        ) {
            $this->lastPage = $this->page + NUM_PAGINAS;
        }

    }

    private function tryLogin()
    {
        $vBulletin = new VBulletin($this->thread->getUser(), $this->thread->getPassword());
        $vBulletin->login();
    }

    private function grantPermissionToFile()
    {
        if (file_exists($this->thread->hasUser() . '.txt')) {
            chmod($this->thread->getUser() . ".txt", 0777);
        }
    }

    private function configCookie()
    {
        return './' . $this->thread->getUser() . '.txt';
    }

    private function retrievePage()
    {

        $url_busqueda = $this->originUrl . "&page=" . $this->page;

        $s = curl_init($url_busqueda);
        curl_setopt($s, CURLOPT_HEADER, true);
        curl_setopt($s, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($s, CURLOPT_COOKIEFILE, $this->cookieName);
        curl_setopt($s, CURLOPT_COOKIEJAR, $this->cookieName);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);

        return curl_exec($s);

    }

    public function scrapThread()
    {

        $this->configOriginUrl();

        // ***********************************************
        //3) Obtener números de página de inicio y fin
        // ***********************************************
        $inicio = $this->thread->getFirstPage();
        $fin = $this->thread->getLastPage();

        if (( ! empty($inicio)) AND (is_numeric($inicio)) AND ($inicio) AND ($inicio < 54)) {
            $this->page = $inicio;
        } else {
            $this->page = 1;
        }

        if ((empty($fin)) OR ( ! is_numeric($fin)) OR ( ! $fin) OR ($fin > 54) OR ($this->page > $fin) OR (($fin - $this->page) > NUM_PAGINAS)) {
            $fin = $this->page + NUM_PAGINAS;
        }

        $final = false;
        $lastSize = 0;

        $this->configCookie();

        if ($this->thread->hasLoginConfiguration()) {
            $this->tryLogin();
        }

        if ($this->thread->hasUser()) {
            $this->grantPermissionToFile();
        }

        $quoteNum = 0;
        $max_limit = 0;

        while ( ! $final) {

            ob_start();

            $content = $this->retrievePage();


            if ($lastSize == strlen($content)) {
                $final = true;
            } else {

                $page = new Page();


                $lastSize = strlen($content);

                // Comprobar que  la página tiene posts
                $patron = "/<div id=\"posts\">/";

                if ( ! preg_match($patron, $content, $salida)) {
                    $page->postsNotFound($this->originUrl);
                    $final = true;

                } else {

                    // 1) Extraemos los posts de la página
                    $patron = "#<table id=\"post([0-9]+)\"#i";
                    $division = preg_split($patron, $content);

                    foreach ($division AS $indice => $content) {

                        // 2) Obtenemos el id del post
                        $patron = '#post([0-9]+)#i';
                        if (preg_match_all($patron, $content, $salida_posts)) {

                            $id_post = $salida_posts[1][0];

                            // 3) Buscamos el usuario en alguna cita
                            $patron = "/<b>" . $this->thread->getUserSearched() . "<\/b>/i";
                            if (preg_match_all($patron, $content, $salida_posts)) {
                                $page->quoted();
                                $page->setUrl($this->originUrl . "#post" . $id_post);
                                $quoteNum++;

                            }
                        }
                    }

                    if ( ! $page->isQuoted()) {
                        $page->setNumPage($this->page);
                    }
                }

                $this->pages[] = $page;
                $this->page++;
                $max_limit++;

                if (($this->page > $fin) OR ($max_limit > MAX_LIMIT)) {
                    $final = true;
                }
            }
            ob_end_flush();
            flush();
        }


    }

}