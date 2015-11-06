<?php
namespace tests\Service;

use Tuum\Respond\Service\ViewData;

class ViewDataTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ViewData
     */
    private $view;
    
    function setup()
    {
        $this->view = new ViewData();
    }
    
    function test()
    {
        $this->assertEquals('Tuum\Respond\Service\ViewData', get_class($this->view));
    }

    /**
     * @test
     */
    function viewData_stores_data()
    {
        $this->view->inputData(['inputs' => 'tested'])
            ->inputErrors(['errors' => 'tested'])
            ->success('message: success')
            ->alert('message: alert')
            ->error('message: error')
            ->setData('value', 'tested');
        
        $this->assertEquals(['inputs' => 'tested'], $this->view->getInputData());
        $this->assertEquals(['errors' => 'tested'], $this->view->getInputErrors());
        $this->assertEquals(['value' => 'tested'], $this->view->getData());
        $this->assertEquals(['message' => 'message: success', 'type' => ViewData::MESSAGE_SUCCESS], $this->view->getMessages()[0]);
        $this->assertEquals(['message' => 'message: alert', 'type' => ViewData::MESSAGE_ALERT], $this->view->getMessages()[1]);
        $this->assertEquals(['message' => 'message: error', 'type' => ViewData::MESSAGE_ERROR], $this->view->getMessages()[2]);
    }
}