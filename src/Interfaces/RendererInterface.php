<?php
namespace Tuum\Respond\Interfaces;

/**
 * Interface RendererInterface
 * 
 * for rendering a template file ($template) with data ($data)
 *
 * @package Tuum\Respond\Interfaces
 */
interface RendererInterface
{
    /**
     * @param string $template
     * @param array  $data
     * @param array  $helper
     * @return string
     */
    public function __invoke($template, array $data, array $helper = []);
}