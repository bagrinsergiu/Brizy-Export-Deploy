<?php

namespace BrizyDeploy\Http;

class RedirectResponse extends Response
{
    /**
     * @var string
     */
    protected $targetUrl;

    /**
     * RedirectResponse constructor.
     * @param $url
     * @param int $status
     * @param array $headers
     */
    public function __construct($url, $status = 302, $headers = array())
    {
        parent::__construct('', $status, $headers);

        $this->setTargetUrl($url);

        if ($status != 302 && $status != 301) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
        }
    }

    /**
     * Returns the target URL.
     *
     * @return string target URL
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * Sets the redirect target of this response.
     *
     * @param string $url The URL to redirect to
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setTargetUrl($url)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }

        $this->targetUrl = $url;

        $this->content = sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url=%1$s" />

        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));

        $this->addHeader('Location', $this->targetUrl);

        return $this;
    }
}
