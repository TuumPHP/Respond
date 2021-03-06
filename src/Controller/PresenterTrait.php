<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;

trait PresenterTrait
{
    use ResponderHelperTrait;

    /**
     * renders $view and returns a new $response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $data
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $data = [])
    {
        return $this->_dispatch('dispatch', $request, $response, $data);
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return ResponseInterface
     */
    public function __call($name, $arguments)
    {
        return $this->_dispatch($name, $arguments[0], $arguments[1], $arguments[2]);
    }

    /**
     * @param string                 $name
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $data
     * @return ResponseInterface
     */
    public function _dispatch($name, ServerRequestInterface $request, ResponseInterface $response, array $data)
    {
        $this->request = $request;
        $this->response = $response;
        if (!$this->responder) {
            $this->responder = Respond::getResponder();
        }

        return $this->$name($data);
    }
}
