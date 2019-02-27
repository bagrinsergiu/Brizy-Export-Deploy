<?php

namespace BrizyDeploy\Utils;

use Symfony\Component\HttpFoundation\Request;

class HttpUtils
{
    static public function getBaseUrl(Request $request, $route_from, $route_to)
    {
        $baseUrl = $request->getScheme() . '://' . $request->getHost();
        if ($request->getPort() != 80 && $request->getPort() != 443) {
            $baseUrl .= ':' . $request->getPort();
        }

        $prefix = str_replace($route_from, '', $request->getBaseUrl());
        if ($prefix != '') {
            $baseUrl = $baseUrl . $prefix . $route_to;
        } else {
            $baseUrl = $baseUrl . $route_to;
        }

        return $baseUrl;
    }
}