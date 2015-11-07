<?php
namespace tests\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Service\TuumViewer;
use Tuum\Respond\Service\ViewData;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class ViewStreamFuncTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TuumViewer
     */
    public $view;

    /**
     * @var ServerRequestInterface
     */
    public $req;

    /**
     * @var ResponseInterface
     */
    public $res;

    /**
     * @var ViewData
     */
    public $viewData;

    function setup()
    {
        $this->view = TuumViewer::forge(__DIR__.'/views');
        $this->req   = RequestHelper::createFromPath('test');
        $this->res   = new Response();
        $this->viewData  = new ViewData();
    }

    /**
     * @test
     */
    function get_contents()
    {
        $this->viewData->setViewFile('simple-text');
        $res = $this->view->withView($this->req, $this->res, $this->viewData);
        $this->assertEquals('this is a simple text.', $res->getBody()->__toString());
    }

}