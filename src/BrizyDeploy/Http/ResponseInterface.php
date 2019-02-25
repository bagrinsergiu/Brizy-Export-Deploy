<?php

namespace BrizyDeploy\Http;

interface ResponseInterface
{
    /**
     * @return mixed
     */
    public function sendHeaders();

    /**
     * @return mixed
     */
    public function sendContent();

    /**
     * @return mixed
     */
    public function send();

    /**
     * @param array $headers
     * @return mixed
     */
    public function setHeaders(array $headers);

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function addHeader($name, $value);
}