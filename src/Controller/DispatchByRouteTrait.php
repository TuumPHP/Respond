<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Helper\ReqAttr;

trait DispatchByRouteTrait
{
    use ControllerTrait;
    use ResponderHelperTrait;

    /**
     * return a hash of routes: [ pattern => handler, ],
     * where method = 'on'+handler
     * 
     * @return array
     */
    abstract protected function getRoutes();

    /**
     * @return null|ResponseInterface
     */
    protected function _execInternalMethods()
    {
        // dispatch by route
        $method         = ReqAttr::getMethod($this->request);
        $path           = ReqAttr::getPathInfo($this->request);
        if (strtoupper($method) === 'OPTIONS') {
            return $this->onOptions($path);
        }

        return $this->dispatchRoute($path, $method);
    }
    
    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param string $path
     * @return ResponseInterface
     */
    private function onOptions($path)
    {
        $routes  = $this->getRoutes();
        $options = ['OPTIONS', 'HEAD'];
        foreach ($routes as $pattern => $dispatch) {
            if ($params = Matcher::verify($pattern, $path, '*')) {
                if (isset($params['method']) && $params['method'] && $params['method'] !== '*') {
                    $options[] = strtoupper($params['method']);
                }
            }
        }
        $options = array_unique($options);
        sort($options);
        $list = implode(',', $options);

        return $this->getResponse()->withHeader('Allow', $list);
    }

    /**
     * @param string                 $path
     * @param string                 $method
     * @return ResponseInterface|null
     */
    private function dispatchRoute($path, $method)
    {
        $routes = $this->getRoutes();
        $matcher = $this->getRouteMatcher();
        foreach ($routes as $pattern => $dispatch) {
            $params = call_user_func($matcher, $pattern, $path, $method);
            if (!empty($params)) {
                $params += $this->request->getQueryParams() ?: [];
                $method = ucwords($dispatch);

                return $this->_execMethodWithArgs($method, $params);
            }
        }

        return null;
    }

    /**
     * @return callable
     */
    protected function getRouteMatcher()
    {
        return [Matcher::class, 'verify'];
    }
}
