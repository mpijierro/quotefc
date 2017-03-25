<?php


class Page
{

    private $quoted = false;
    private $url = '';
    private $numPage = 0;
    private $postFound = true;


    public function getUrl()
    {
        return $this->url;
    }

    public function getNumPage()
    {
        return $this->numPage;
    }


    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setNumPage($numPage)
    {
        $this->numPage = $numPage;
    }


    public function quoted()
    {
        $this->quoted = true;
    }

    public function notQuoted()
    {
        $this->quoted = false;
    }

    public function isQuoted()
    {
        return $this->quoted;
    }

    public function postsNotFound($url)
    {
        $this->postFound = false;
        $this->url = $url;
    }

    public function isPostFound()
    {
        return $this->postFound;
    }

}