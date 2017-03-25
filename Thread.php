<?php


class Thread
{
    const PAGE_INITIAL = 1;
    const NUM_PAGINAS = 10;

    const POSITION_THREAD = 0;
    const POSITION_USER_SEARCHED = 1;
    const POSITION_START = 2;
    const POSITION_END = 3;
    const USER_LOGIN = 4;
    const USER_PASSWORD = 5;

    private $url = '';
    private $idThreadType = '';
    private $numberThread = '';
    private $idThread = '';
    private $userSearched = '';
    private $firstPage = '';
    private $lastPage = '';
    private $user = '';
    private $password = '';

    private $lineArray = [];


    public function getUrl()
    {
        return $this->url;
    }

    public function getIdThread()
    {
        return $this->idThread;
    }

    public function getUserSearched()
    {
        return $this->userSearched;
    }

    public function getFirstPage()
    {
        return $this->firstPage;
    }

    public function getLastPage()
    {
        return $this->lastPage;
    }


    public function getUser()
    {
        return $this->user;
    }


    public function getPassword()
    {
        return $this->password;
    }

    public function retrieveBuildIdThread()
    {
        return $this->idThreadType . '=' . $this->numberThread;
    }

    public function configFromRequest()
    {

        $this->url = $_POST['hilo'];
        $this->userSearched = $_POST['usuario'];
        $this->firstPage = $_POST['inicio'];
        $this->lastPage = $_POST['final'];
        $this->user = $_POST['user'];
        $this->password = $_POST['clave'];

        $this->checkThreadIsValid();
    }

    public function configFromArray($lineArray = [])
    {

        $this->lineArray = $lineArray;

        $this->configUrl();

        $this->configUserSearched();

        $this->configFirstPage();

        $this->configLastPage();

        $this->configLogin();

        $this->checkThreadIsValid();

    }


    private function configUrl()
    {
        $this->url = $this->sanitize($this->lineArray[self::POSITION_THREAD]);
    }

    private function configUserSearched()
    {
        $this->userSearched = $this->sanitize($this->lineArray[self::POSITION_USER_SEARCHED]);
    }

    public function configFirstPage()
    {

        $this->firstPage = self::PAGE_INITIAL;

        $existStartConfig = (isset($this->lineArray[self::POSITION_START]) AND (is_numeric($this->lineArray[self::POSITION_START])));

        if ($existStartConfig) {
            $this->firstPage = $this->lineArray[self::POSITION_START];
        }

    }

    public function configLastPage()
    {

        $this->lastPage = self::NUM_PAGINAS;

        $existEndConfig = (isset($this->lineArray[self::POSITION_END]) AND (is_numeric($this->lineArray[self::POSITION_END])));

        if ($existEndConfig) {
            $this->lastPage = $this->lineArray[self::POSITION_END];
        }

    }

    public function configLogin()
    {

        $existUser = ! empty($lineArray[self::USER_LOGIN]);
        $existPassword = ! empty($lineArray[self::USER_PASSWORD]);

        if ($existUser AND $existPassword) {
            $this->user = $this->sanitize($lineArray[self::USER_LOGIN]);
            $this->password = $this->sanitize($lineArray[self::USER_PASSWORD]);
        }

    }


    private function checkThreadIsValid()
    {

        $this->checkValidUrl();

        $this->checkUserSearched();

    }

    public function checkValidUrl()
    {
        $this->checkUrl();

        $this->checkForocochesUrl();

        $this->checkIdThread();
    }

    private function checkUrl()
    {

        if (empty($this->url)) {
            throw new Exception('Url not found');
        }
    }

    public function checkForocochesUrl()
    {

        $pattern = "/^(http:\/\/www.forocoches.com)/i";

        if ( ! preg_match($pattern, $this->url, $salida)) {
            throw new Exception('Forocoches url not found');
        }

    }

    private function checkIdThread()
    {

        $pattern = "/(t|p)=([0-9]+)/";

        if ( ! preg_match($pattern, $this->url, $configIdThread)) {
            throw new Exception('Thread id not found in url');
        }

        $this->idThreadType = $configIdThread[1];
        $this->numberThread = $configIdThread[2];

        $this->idThread = $configIdThread;

    }


    private function checkUserSearched()
    {

        if (empty($this->userSearched)) {
            throw new Exception('User searched not found');
        }
    }

    public function hasLoginConfiguration()
    {
        return ( ! empty($this->user) and ! empty($this->password));
    }

    public function hasUser()
    {
        return ! empty($this->hasUser);
    }

}


