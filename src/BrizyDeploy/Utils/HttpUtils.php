<?php

namespace BrizyDeploy\Utils;

use Symfony\Component\HttpFoundation\Request;

class HttpUtils
{
    static public function getBaseUrl(Request $request, $route_from, $route_to)
    {
        $prefix = str_replace($route_from, '', $request->getBaseUrl());
        if ($prefix != '') {
            $baseUrl = $request->getScheme() . '://' . $request->getHost() . $prefix . $route_to;
        } else {
            $baseUrl = $request->getScheme() . '://' . $request->getHost() . $route_to;
        }

        if ($request->getPort() != 80 && $request->getPort() != 443) {
            $baseUrl .= ':' . $request->getPort();
        }

        return $baseUrl;
    }
}