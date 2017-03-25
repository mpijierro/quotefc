<?php


class Scraper
{
    const LAST_PAGE_THREAD = 54;

    private $thread;
    private $originUrl = '';
    private $firstPage = '';
    private $lastPage = 1;
    private $currentPage = 1;
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
            $this->currentPage = $this->firstPage;
        }

        if ((empty($this->lastPage)) OR
            ( ! is_numeric($this->lastPage)) OR
            ( ! $this->lastPage) OR
            ($this->lastPage > self::LAST_PAGE_THREAD) OR
            ($this->currentPage > $this->lastPage) OR
            (($this->lastPage - $this->currentPage) > Thread::NUM_PAGINAS)
        ) {
            $this->lastPage = $this->currentPage + Thread::NUM_PAGINAS;
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

    private function configCookieName()
    {
        $this->cookieName = './' . $this->thread->getUser() . '.txt';
    }

    private function retrievePage()
    {

        $url_busqueda = $this->originUrl . "&page=" . $this->currentPage;

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

        $this->configCookieName();

        // ***********************************************
        //3) Obtener números de página de inicio y fin
        // ***********************************************
        $firstPage = $this->thread->getFirstPage();
        $lastPage = $this->thread->getLastPage();

        if (( ! empty($firstPage)) AND (is_numeric($firstPage)) AND ($firstPage) AND ($firstPage < 54)) {
            $this->currentPage = $firstPage;
        } else {
            $this->currentPage = 1;
        }

        if ((empty($lastPage)) OR
            ( ! is_numeric($lastPage)) OR
            ( ! $lastPage) OR ($lastPage > self::LAST_PAGE_THREAD) OR
            ($this->currentPage > $lastPage) OR
            (($lastPage - $this->currentPage) > Thread::NUM_PAGINAS)
        ) {
            $lastPage = $this->currentPage + Thread::NUM_PAGINAS - 1;
        }

        $isFinal = false;
        $lastSize = 0;

        if ($this->thread->hasLoginConfiguration()) {
            $this->tryLogin();
        }

        if ($this->thread->hasUser()) {
            $this->grantPermissionToFile();
        }

        $limitCounter = 0;

        while ( ! $isFinal) {

            ob_start();

            $pageContent = $this->retrievePage();

            if ($lastSize == strlen($pageContent)) {
                $isFinal = true;
            } else {

                $page = new Page();

                $lastSize = strlen($pageContent);

                // Comprobar que la página tiene posts
                $pattern = "/<div id=\"posts\">/";

                if ( ! preg_match($pattern, $pageContent, $salida)) {
                    $page->postsNotFound($this->originUrl);
                    $isFinal = true;

                } else {

                    // 1) Extraemos los posts de la página
                    $pattern = "#<table id=\"post([0-9]+)\"#i";
                    $posts = preg_split($pattern, $pageContent);

                    foreach ($posts AS $index => $postContent) {

                        // 2) Buscamos al usuario citado en el post
                        $pattern = "/<b>" . $this->thread->getUserSearched() . "<\/b>/i";

                        if (preg_match_all($pattern, $postContent, $output)) {

                            $pattern = '#postcount([0-9]+)#i';
                            preg_match_all($pattern, $postContent, $outputPost);
                            $id_post = $outputPost[1][0];

                            $page->quoted();
                            $page->setUrl($this->originUrl . "&page=" . $this->currentPage . "#post" . $id_post);

                        }
                    }
                }

                $page->setNumPage($this->currentPage);
                $this->pages[] = $page;
                $this->currentPage++;
                $limitCounter++;

                if (($this->currentPage > $lastPage) OR ($limitCounter > Searcher::MAX_LIMIT)) {
                    $isFinal = true;
                }

            }
            ob_end_flush();
            flush();
        }

    }


}