<?php
namespace tests\Service;

use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\ViewData;
use Tuum\Respond\Service\ViewerInterface;
use Tuum\Respond\Service\ViewerTrait;

class ViewForError implements ViewerInterface
{
    use ViewerTrait;

    /**
     * renders $view_file with $data.
     *
     * @param string   $view_file
     * @param ViewData $data
     * @return ViewerInterface
     */
    public function withView($view_file, $data = null)
    {
        $this->view_file = $view_file;
        $this->view_data = $data;
        return $this;
    }

    /**
     * @return string
     */
    protected function render()
    {
    }
    
    public function getViewFile()
    {
        return $this->view_file;
    }
    
    public function getViewData()
    {
        return $this->view_data;
    }
};

class ErrorViewException extends \Exception
{
}

class ErrorViewTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ViewForError
     */
    private $view;

    function setup()
    {
        $this->view  = new ViewForError();
    }

    /**
     * @test
     */
    function forget_sets_options()
    {
        $error = ErrorView::forge($this->view, [
            'default' => 'tested-default',
            'status'  => [
                '123' => 'tested-status'
            ],
            'handler' => false,
        ]);
        $this->assertEquals('tested-default', $error->default_error);
        $this->assertEquals('tested-status',  $error->statusView['123']);
    }

    /**
     * @test
     */
    function getStream_returns_stream_with_error_code()
    {
        $error = ErrorView::forge($this->view, [
            'default' => 'tested-default',
            'status'  => [
                '123' => 'tested-status'
            ],
            'handler' => false,
        ]);
        $error->getStream('123', ['stream' => 'tested']);
        $this->assertEquals('tested-status', $this->view->getViewFile());

        $error->getStream('234', ['stream' => 'tested']);
        $this->assertEquals('tested-default', $this->view->getViewFile());
    }

    /**
     * for PhpStorm users (like me):
     *
     * this test hangs when running from PhpStorm.
     * run phpunit from terminal. to get code coverage, try:
     * phpunit --coverage-clover ../../Respond-coverage.xml
     *
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @group NoStorm
     */
    function invoke_will_emit()
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $error = ErrorView::forge($this->view, [
            'default' => 'error-default',
        ]);
        $error->setExitOnTerminate(false);

        $error->__invoke(new ErrorViewException('error-view', 123));
        $this->assertEquals('error-default', $this->view->getViewFile());
    }
}