<?php

namespace Rout\Router;

use App\Controllers\Controller;

abstract class BaseRouter
{
    public $routs = [];

    public function get(string $phat, Controller $controller, $name = '')
    {
        $rout = [
            'method'     => 'GET',
            'path'       => $phat,
            'controller' => $controller,
            'name'       => $name,
        ];

        $this->routs[] = $rout;
    }

    public function post($phat, Controller $controller, $name = '')
    {
        $rout = [
            'method'     => 'POST',
            'path'       => $phat,
            'controller' => $controller,
            'name'       => $name,
        ];

        $this->routs[] = $rout;
    }

    public function patch($phat, Controller $controller, $name = '')
    {
        $rout = [
            'method'     => 'PATCH',
            'path'       => $phat,
            'controller' => $controller,
            'name'       => $name,
        ];

        $this->routs[] = $rout;
    }

    public function put($phat, Controller $controller, $name = '')
    {
        $rout = [
            'method'     => 'PUT',
            'path'       => $phat,
            'controller' => $controller,
            'name'       => $name,
        ];

        $this->routs[] = $rout;
    }

    public function delete($phat, Controller $controller, $name = '')
    {
        $rout = [
            'method'     => 'DELETE',
            'path'       => $phat,
            'controller' => $controller,
            'name'       => $name,
        ];

        $this->routs[] = $rout;
    }

    protected function getParams($phat)
    {
        $arr = [];
        if (\strpos($phat, '?') !== false) {
            $arr = \explode('?', $phat);

            if ( ! empty($arr)) {
                if (\strpos($arr[1], '&')) {
                    $as = \explode('&', $arr[1]);
                } elseif (\strpos($arr[1], '=') !== false) {
                    $a = \explode('=', $arr[1]);
                }
            }

            if (isset($as) && ! empty($as)) {
                $ak = [];
                foreach ($as as $item) {
                    if (\strpos($item, '=') !== false) {
                        $ak[] = \explode('=', $item);
                    }
                }
                foreach ($ak as $item) {
                    if (\count($item) === 2) {
                        $a[] = $item[0];
                        $a[] = $item[1];
                    } else {
                        continue;
                    }
                }
            }

            $arrParams = [];
            if (isset($a) && ! empty($a)) {
                for ($i = 0; $i < \count($a); $i += 2) {
                    $arrParams[$a[$i]] = $a[$i + 1];
                }
            }

            $arr['path'] = $arr[0];
            if ( ! empty($arrParams)) {
                $arr['getParams'] = $arrParams;
            }

            unset($arr[0], $arr[1]);
        }

        if (empty($arr)) {
            return $phat;
        }

        return $arr;
    }

    protected function params($uri)
    {
        $rout        = $this->getParams($uri);
        $routsParams = [];
        $routs       = [];

        foreach ($this->routs as $r) {
            if (\preg_match('/\/.*?\/*?\{.*?\}\/*?.*?/', $r['path'])) {
                $routsParams[] = $r;
            } else {
                $routs[] = $r;
            }
        }
        if ( ! empty($routs)) {
            foreach ($routs as $r) {
                if (\is_array($rout)) {
                    if ($r['path'] === $rout['path']) {
                        return $rout;
                    }
                } else {
                    if ($r['path'] === $rout) {
                        return $rout;
                    }
                }
            }
        }

        if ( ! empty($routsParams) && isset($rout) && ! empty($rout)) {
            $r = $this->uriParams($rout, $routsParams);
            if (\is_array($rout)) {
                $rout = \array_merge($rout, $r);
            } else {
                $rout = $r;
            }
        }

        return $rout;
    }

    protected function uriParams($rout, $routs)
    {
        $p = '';
        if (\is_array($rout)) {
            $p = \trim($rout['path'], '/');
        } elseif ($rout === '/') {
            return $rout;
        } else {
            $p = \trim($rout, '/');
        }

        $routArr    = \explode('/', $p);
        $param      = [];
        $returnRout = [];
        foreach ($routs as $r) {
            $arr = \explode('/', \trim($r['path'], '/'));

            if (\count($arr) === \count($routArr)) {
                for ($i = 0; $i < \count($arr); $i++) {
                    if (\preg_match('/^\{.*?\}$/', $arr[$i])) {
                        if (\is_numeric($routArr[$i])) {
                            $param[\trim($arr[$i], '{}')] = $routArr[$i];
                            if ($i === count($arr) - 1) {
                                $returnRout = [
                                    'path'      => $r['path'],
                                    'uriParams' => $param,
                                ];
                            }
                        }
                    } elseif ($arr[$i] !== $routArr[$i]) {
                        continue 1;
                    } else {
                        if ($i === count($arr) - 1) {
                            $returnRout = [
                                'path'      => $r['path'],
                                'uriParams' => $param,
                            ];
                        }
                    }
                }
            }
        }

        return $returnRout;
    }

    protected function putMethod($rout, $getParams = [], $uriParams = [])
    {
        \header('Content-Type: application/x-www-form-urlencoded');
        parse_str(file_get_contents('php://input'), $data);

        $this->startPMethod($rout, $data, $getParams, $uriParams);
    }

    protected function postMethod($rout, $getParams = [], $uriParams = [])
    {
        $data = $_POST;
        $this->startPMethod($rout, $data, $getParams, $uriParams);
    }

    protected function patchMethod($rout, $getParams = [], $uriParams = [])
    {
        \header('Content-Type: application/x-www-form-urlencoded');
        parse_str(file_get_contents('php://input'), $data);

        $this->startPMethod($rout, $data, $getParams, $uriParams);
    }

    protected function getMethod($rout, $getParams = [], $uriParams = [])
    {
        $this->startGDMethod($rout, $getParams, $uriParams);
    }

    protected function deleteMethod($rout, $getParams = [], $uriParams = [])
    {
        $this->startGDMethod($rout, $getParams, $uriParams);
    }

    protected function startPMethod($rout, $data = [], $getParams = [], $uriParams = [])
    {
        if ( ! empty($getParams) && ! empty($uriParams)) {
            $rout['controller']($data, $uriParams, $getParams);
        } elseif ( ! empty($getParams) && empty($uriParams)) {
            $rout['controller']($data, $getParams);
        } elseif (empty($getParams) && ! empty($uriParams)) {
            $rout['controller']($data, $uriParams);
        } else {
            $rout['controller']($data);
        }
    }

    protected function startGDMethod($rout, $getParams = [], $uriParams = [])
    {
        if ( ! empty($getParams) && ! empty($uriParams)) {
            $rout['controller']($uriParams, $getParams);
        } elseif ( ! empty($getParams) && empty($uriParams)) {
            $rout['controller']($getParams);
        } elseif (empty($getParams) && ! empty($uriParams)) {
            $rout['controller']($uriParams);
        } else {
            $rout['controller']();
        }
    }
}