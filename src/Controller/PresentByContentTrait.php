<?php
namespace Tuum\Respond\Controller;

use Negotiation\Accept;
use Negotiation\Negotiator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait PresentByContentTrait
{
    use ResponderHelperTrait;

    /**
     * list available methods and its accept mime type as;
     * [
     *    'mime-type' => 'method-name',
     *    'application/json' => 'json',
     *    'application/xml'  => 'xml',
     * ]
     *
     * @Override
     * @var string[]
     */
    protected $methodsList = [
    ];

    /**
     * @var string[]
     */
    private $defaultMethodList = [
        'text/html; charset=UTF-8' => 'html',
        'application/json' => 'json',
        'application/xml'  => 'xml',
    ]; 

    /**
     * prepares a response returns a new $response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $data
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $data = [])
    {
        return $this->_dispatch($request, $response, $data);
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return ResponseInterface
     */
    public function __call($name, $arguments)
    {
        $this->setRequest($arguments[0]);
        $this->setResponse($arguments[1]);

        return call_user_func_array([$this, $name], $arguments);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $data
     * @return ResponseInterface
     */
    protected function _dispatch(ServerRequestInterface $request, ResponseInterface $response, array $data)
    {
        $this->setRequest($request);
        $this->setResponse($response);
        
        $negotiator = new Negotiator();
        $accepts = $request->getServerParams()['HTTP_ACCEPT'];
        $methods = $this->_findMethodList();
        
        /** @var Accept $bestMime */
        $bestMime = $negotiator->getBest($accepts, array_keys($methods));
        $mimeType = $bestMime->getValue();
        $execute  = $methods[$mimeType];
        
        /** @var ResponseInterface $response */
        $response = $this->$execute($data);
        return $response->withHeader('Content-Type', $mimeType);
    }

    /**
     * @return string[]
     */
    private function _findMethodList()
    {
        if (!empty($this->methodsList)) {
            return $this->methodsList;
        }
        $ref  = new \ReflectionClass($this);
        $list = [];
        foreach($this->defaultMethodList as $mime => $method) {
            if ($ref->hasMethod($method)) {
                $list[$mime] = $method;
            }
        }
        return $list;
    }
}