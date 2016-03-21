<?php
namespace Tuum\Respond\Service;

use Tuum\Respond\Interfaces\ViewDataInterface;

trait ViewerTrait
{
    /**
     * @param ViewDataInterface      $viewData
     * @param ViewHelper             $view
     * @return ViewHelper
     */
    protected function forgeDataView($viewData = null, $view = null)
    {
        return $view->setViewData($viewData);
    }
}