<?php

class Update
{

    /**
     * If curl should verify the host certificate.
     *
     * @var bool
     */
    private $sslVerifyHost = true;

    /**
     * @return bool
     */
    public function getSslVerifyHost()
    {
        return $this->sslVerifyHost;
    }

    /**
     * @param $sslVerifyHost
     * @return $this
     */
    public function setSslVerifyHost($sslVerifyHost)
    {
        $this->sslVerifyHost = $sslVerifyHost;

        return $this;
    }

    public function execute()
    {
        //@todo
        // 1. create reserve copy
        // 2. download zip
        // 3. replace simulate
        // 4. replace or rollback


    }

    /**
     * Download file via curl.
     *
     * @param string $url URL to file
     * @return string|false
     */
    protected function _downloadCurl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if ($this->sslVerifyHost) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->sslVerifyHost);
        $update = curl_exec($curl);
        $error = false;
        if (curl_error($curl)) {
            $error = true;
        }
        curl_close($curl);
        if ($error === true) {
            return false;
        }
        return $update;
    }
}