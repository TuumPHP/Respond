<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Form\Data\Data;
use Tuum\Form\Data\Errors;
use Tuum\Form\Data\Escape;
use Tuum\Form\Data\Inputs;
use Tuum\Form\Data\Message;
use Tuum\Form\DataView;
use Tuum\Form\Dates;
use Tuum\Form\Forms;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\ViewData;

/**
 * Class ViewHelper
 *
 * @package Tuum\Respond\Service
 *
 * @property Forms   $forms
 * @property Dates   $dates
 * @property Data    $data
 * @property Message $message
 * @property Inputs  $inputs
 * @property Errors  $errors
 * @property Escape  $escape
 */
class ViewHelper
{
    /**
     * @var DataView
     */
    private $dataView;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var ViewData
     */
    private $viewData;

    /**
     * ViewHelper constructor.
     *
     * @param DataView $dataView
     */
    public function __construct($dataView)
    {
        $this->dataView = $dataView;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param Responder              $responder
     * @param ViewDataInterface      $viewData
     * @return ViewHelper
     */
    public static function forge($request, $response, $responder, $viewData)
    {
        $self = new self(new DataView());
        $self->start($request, $response, $responder);
        $self->setViewData($viewData);

        return $self;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param Responder              $responder
     * @return $this
     */
    public function start($request, $response, $responder)
    {
        $this->request   = $request;
        $this->response  = $response;
        $this->responder = $responder;

        return $this;
    }

    /**
     * @param ViewDataInterface $viewData
     * @return $this
     */
    public function setViewData($viewData)
    {
        $view = $this->dataView;
        $get  = function ($method) use ($viewData) {
            return ($viewData instanceof ViewDataInterface) ? $viewData->$method() : [];
        };
        $view->setData($get('getData'));
        $view->setErrors($get('getInputErrors'));
        $view->setInputs($get('getInput'));
        $view->setMessage($get('getMessages'));
        $this->viewData = $viewData;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->dataView->$name)) {
            return $this->dataView->$name;
        }
        throw new \InvalidArgumentException;
    }

    /**
     * @return Forms
     */
    public function forms()
    {
        return $this->dataView->forms;
    }

    /**
     * @return Inputs
     */
    public function inputs()
    {
        return $this->dataView->inputs;
    }

    /**
     * @return Data
     */
    public function data()
    {
        return $this->dataView->data;
    }

    /**
     * @return Errors
     */
    public function errors()
    {
        return $this->dataView->errors;
    }

    /**
     * @return Message
     */
    public function message()
    {
        return $this->dataView->message;
    }

    /**
     * @return Dates
     */
    public function dates()
    {
        return $this->dataView->dates;
    }

    /**
     * @return ServerRequestInterface
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * @return UriInterface
     */
    public function uri()
    {
        return $this->request->getUri();
    }

    /**
     * @param null|string $key
     * @param null|string $default
     * @return array|mixed
     */
    public function attributes($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->request->getAttributes();
        }

        return $this->request->getAttribute($key, $default);
    }

    /**
     * @param string|PresenterInterface $presenter
     * @param null|mixed|ViewData       $data
     * @return string
     */
    public function call($presenter, array $data = [])
    {
        if (!$this->responder) {
            return '';
        }
        $response = $this->prepareResponseStream($this->response);
        $response = $this->responder->view($this->request, $response)->call($presenter, $data);

        return $this->returnResponseBody($response);
    }

    /**
     * @param string              $viewFile
     * @param array $data
     * @return string
     */
    public function render($viewFile, $data = [])
    {
        if (!$this->responder) {
            return '';
        }
        $response = $this->prepareResponseStream($this->response);
        $response = $this->responder->view($this->request, $response)->render($viewFile, $data);

        return $this->returnResponseBody($response);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function prepareResponseStream($response)
    {
        $response->getBody()->rewind();

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @return string
     */
    private function returnResponseBody($response)
    {
        if (!$response->getBody()->isSeekable()) {
            throw new \InvalidArgumentException('not seekable response body. ');
        }
        $position = $response->getBody()->tell();
        $response->getBody()->rewind();
        $contents = $response->getBody()->read($position);
        $response->getBody()->rewind();

        return $contents;
    }
}