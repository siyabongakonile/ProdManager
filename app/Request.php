<?php
declare(strict_types = 1);

namespace App;

class Request{
    protected string $method;
    protected string $uri;
    protected $uriArgsParts;
    protected array $params;

    public function __construct($method, $uri, $params = []){
        $this->method = $method;
        $this->uri = $uri;
        $this->params = $params;
        $this->setUriArgsParts();
    }

    /** 
     * Get the request method.
     * 
     * @return string the request method
    */
    public function getMethod(): string{
        return $this->method;
    }

    /**
     * Get the request URI.
     * 
     * @return string URI
     */
    public function getUri(): string{
        return $this->uri;
    }

    protected function setUriArgsParts(){
        $uriArgsParts = \array_slice(\explode("/", $this->uri), 1);
        $uriArgsParts[0] = '/' . $uriArgsParts[0];
        $this->uriArgsParts = $uriArgsParts;
    }

    public function getFirstUriPart(): string{
        return $this->uriArgsParts[0];
    }

    public function getSecondUriPart(): ?string{
        return $this->uriArgsParts[1] ?? null;
    }

    public function getPageNumberFromUri(): ?string{
        return $this->uriArgsParts[2] ?? null;
    }

    public function getParams(){
        return $this->params;
    }

    /**
     * Get the value of the $_COOKIE constant.
     * 
     * @return array The value of $_COOKIE
     */
    public function getCookie(string $cookieName): ?array{
        return $_COOKIE[$cookieName] ?? null;
    }

    /**
     * Get the value of the $_SESSION constant.
     * 
     * @return array The value of $_SESSION
     */
    public function getSession(): ?array{
        return $_SESSION ?? null;
    }

    /**
     * Get the value of the $_GET constant.
     * 
     * @return array The value of $_GET
     */
    public function getGET(): array{
        return $_GET;
    }

    /**
     * Get the value of the $_POST constant.
     * 
     * @return array The value of $_POST
     */
    public function getPOST(): array{
        return $_POST;
    }
}