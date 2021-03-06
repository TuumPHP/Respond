<?php
namespace tests\Responder;

use tests\Tools\TesterTrait;
use tests\Tools\NoRender;
use Tuum\Respond\Builder;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class ViewTest extends \PHPUnit_Framework_TestCase
{
    use TesterTrait;

    /**
     * @var Responder
     */
    public $responder;

    /**
     * @var SessionStorageInterface
     */
    private $session;

    /**
     * @var View
     */
    private $view;

    /**
     * @var NoRender
     */
    private $renderer;

    function setup()
    {
        $_SESSION      = [];
        $this->session = SessionStorage::forge('tuum-app');
        $this->setPhpTestFunc($this->session);
        $this->renderer = new NoRender();
        $this->responder = Responder::forge(
            Builder::forge('app-test')
            ->setRenderer($this->renderer)
        )->setResponse(new Response());
        $this->view      = $this->responder->view(ReqBuilder::createFromPath('test'));
    }

    function tearDown()
    {
        unset($_SESSION);
    }

    function test0()
    {
        $this->assertEquals('Tuum\Respond\Responder\View', get_class($this->view));
    }

    /**
     * @test
     */
    function asContents_uses_default_content_file()
    {
        $this->view->content_view = 'default/contents';
        $this->view->asContents('test-content');
        $this->assertEquals('default/contents', $this->renderer->template);
    }

    /**
     * @test
     */
    function asFileContents_returns_fp_as_stream()
    {
        $fp = fopen('php://memory', 'wb+');
        fwrite($fp, 'test/resource');

        $response = $this->view->asFileContents($fp, 'mime/test');
        $this->assertSame('test/resource', $response->getBody()->__toString());
        $this->assertEquals('mime/test', $response->getHeader('Content-Type')[0]);
    }

    /**
     * @test
     */
    function executes_callable()
    {
        $string = $this->view->call(function () {
            return 'tested: closure';
        });
        $this->assertEquals('tested: closure', $string);
    }

    /**
     * @test
     */
    function executes_ViewerInterface_object()
    {
        $stub = $this->getMockBuilder(PresenterInterface::class)->getMock();
        $stub->method('__invoke')->willReturn('tested: mock');
        $string = $this->view->call($stub);
        $this->assertEquals('tested: mock', $string);
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    function execute_string_without_resolver_throws_exception()
    {
        $this->view->call('bad-input');
    }

}