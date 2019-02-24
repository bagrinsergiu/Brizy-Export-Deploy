<?php

namespace BrizyDeploy\Http;

interface ResponseInterface
{
    public function sendHeaders();

    public function sendContent();

    public function send();

    public function setHeaders(array $headers);

    public function addHeader($name, $value);
}