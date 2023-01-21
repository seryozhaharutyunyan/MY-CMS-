<?php

namespace Rout\Router;

use App\Controllers\Controller;
use Singleton\Singleton;

final class Router extends BaseRouter
{
    use Singleton;

    public function start()
    {
        $rout = [];
        $getParams=[];
        $uriParams=[];
        $p = $this->params($_SERVER['REQUEST_URI']);
        if (isset($p) && ! empty($p)) {
            if (\is_array($p)) {
                $_rout = $p['path'];

                if (isset($p['getParams']) && ! empty($p['getParams'])) {
                    $getParams = $p['getParams'];
                }

                if (isset($p['uriParams']) && ! empty($p['uriParams'])) {
                    $uriParams = $p['uriParams'];
                }
            }
            if (\is_string($p)) {
                $_rout = $p;
            }
        } else {
            $phat = $_SERVER['REQUEST_URI'];
        }

        foreach ($this->routs as $r) {
            if (\is_string($_rout)) {
                if ($r['path'] === $_rout) {
                    $rout = $r;
                    continue;
                }
            }

            if (\is_array($_rout)) {
                if ($r['path'] === $_rout['path']) {
                    $rout = \array_merge($r, $_rout);
                    continue;
                }
            }
        }

        if (empty($rout)) {
            \header("Location: " . BASE_URL);
            exit();
        }

        if ($rout['method'] !== $_SERVER['REQUEST_METHOD']) {
            throw new \Exception('link not available with this node method');
            exit();
        }

        if ($rout['method'] === 'PUT') {
            $this->putMethod($rout);

        }
        if ($rout['method'] === 'POST') {
            $this->postMethod($rout, $getParams, $uriParams);

        }

        if ($rout['method'] === 'PATCH') {
            $this->patchMethod($rout, $getParams, $uriParams);

        }

        if ($rout['method'] === 'GET') {
            $this->getMethod($rout, $getParams, $uriParams);
        }

        if ($rout['method'] === 'DELETE') {
            $this->deleteMethod($rout, $getParams, $uriParams);
        }
    }


}