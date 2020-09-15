<?php


class Thread
{
    const PAGE_INITIAL = 1;
    const NUM_PAGINAS = 10;

    const POSITION_THREAD = 0;
    const POSITION_USER_SEARCHED = 1;
    const POSITION_FIRST_PAGE = 2;
    const POSITION_LAST_PAGE = 3;
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

        $this->assignValueFromRequest();

        if (empty($this->firstPage)) {
            $this->firstPage = self::PAGE_INITIAL;
        }

        if (empty($this->lastPage)) {
            $this->lastPage = self::NUM_PAGINAS;
        }

        $this->checkThreadIsValid();

    }

    private function assignValueFromRequest()
    {
        $this->url = $this->sanitize($_POST['thread']);
        $this->userSearched = $this->sanitize($_POST['user_searched']);
        $this->firstPage = $this->sanitize($_POST['first_page']);
        $this->lastPage = $this->sanitize($_POST['last_page']);
        $this->user = $this->sanitize($_POST['user']);
        $this->password = $this->sanitize($_POST['password']);

    }

    public function configFromArray($lineArray = [])
    {

        $this->lineArray = $lineArray;

        $this->checkEmptyLine();

        $this->configUrl();

        $this->configUserSearched();

        $this->configFirstPage();

        $this->configLastPage();

        $this->configLogin();

        $this->checkThreadIsValid();

    }

    private function checkEmptyLine()
    {
        if (empty($this->lineArray[0])) {
            throw new Exception('Line content not found');
        }
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

        $existFirstPageConfig = (isset($this->lineArray[self::POSITION_FIRST_PAGE]) AND (is_numeric($this->lineArray[self::POSITION_FIRST_PAGE])));

        if ($existFirstPageConfig) {
            $this->firstPage = $this->lineArray[self::POSITION_FIRST_PAGE];
        }

    }

    public function configLastPage()
    {

        $this->lastPage = self::NUM_PAGINAS;

        $existLastPageConfig = (isset($this->lineArray[self::POSITION_LAST_PAGE]) AND (is_numeric($this->lineArray[self::POSITION_LAST_PAGE])));

        if ($existLastPageConfig) {
            $this->lastPage = $this->lineArray[self::POSITION_LAST_PAGE];
        }

    }

    public function configLogin()
    {

        $existUser = ! empty($this->lineArray[self::USER_LOGIN]);
        $existPassword = ! empty($this->lineArray[self::USER_PASSWORD]);

        if ($existUser AND $existPassword) {
            $this->user = $this->sanitize($this->lineArray[self::USER_LOGIN]);
            $this->password = $this->sanitize($this->lineArray[self::USER_PASSWORD]);
        }

    }

    private function sanitize($string)
    {
        return trim($string);
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

        $pattern = "/^(https:\/\/www.forocoches.com)/i";

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

    public function retrieveAsLineString($addPrivateData = false)
    {
        $string = $this->getUrl() . Searcher::SEPARADOR . $this->getUser() . Searcher::SEPARADOR . $this->getFirstPage() . Searcher::SEPARADOR . $this->getLastPage() . Searcher::SEPARADOR;

        if ($addPrivateData) {
            $string .= $this->getUser() . Searcher::SEPARADOR . $this->getPassword();
        }

        return $string;
    }

}


