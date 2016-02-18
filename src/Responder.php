<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\AbstractWithViewData;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Tuum\Respond\Responder\ViewData;

class Responder
{
    /**
     * @var SessionStorageInterface
     */
    private $session;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var AbstractWithViewData[]
     */
    private $responders = [];

    /**
     * @param View     $view
     * @param Redirect $redirect
     * @param Error    $error
     */
    public function __construct(
        View $view,
        Redirect $redirect,
        Error $error
    ) {
        $this->responders = [
            'view'     => $view,
            'redirect' => $redirect,
            'error'    => $error,
        ];
    }

    /**
     * @return mixed|ViewData
     */
    public function getViewData()
    {
        if ($this->session) {
            $view = $this->session->getFlash(ViewData::MY_KEY);
            if ($view) {
                return clone($view);
            }
        }

        return $this->forgeViewData();
    }

    /**
     * @return ViewData
     */
    protected function forgeViewData()
    {
        return new ViewData();
    }

    /**
     * set SessionStorage and retrieves ViewData from session's flash.
     * execute this method before using responders.
     *
     * @api
     * @param SessionStorageInterface $session
     * @return Responder
     */
    public function withSession(SessionStorageInterface $session)
    {
        $self          = clone($this);
        $self->session = $session;

        return $self;
    }

    /**
     * set response object when omitting $response when calling
     * responders, such as:
     * Respond::view($request);
     *
     * responders will return $response using this object.
     *
     * @api
     * @param ResponseInterface $response
     * @return Responder
     */
    public function withResponse(ResponseInterface $response)
    {
        $self           = clone($this);
        $self->response = $response;

        return $self;
    }

    /**
     * @param string                 $responder
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return AbstractWithViewData
     */
    private function returnWith($responder, $request, $response)
    {
        $responder = $this->responders[$responder];
        $response  = $response ?: $this->response;

        return $responder->withRequest($request, $response, $this->session);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return View
     */
    public function view(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ) {
        return $this->returnWith('view', $request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return Redirect
     */
    public function redirect(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ) {
        return $this->returnWith('redirect', $request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return Error
     */
    public function error(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ) {
        return $this->returnWith('error', $request, $response);
    }

    /**
     * @return SessionStorageInterface
     */
    public function session()
    {
        return $this->session;
    }
}