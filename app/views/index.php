<?php
/** @var Renderer $this */
/** @var DataView $view */
use Tuum\Form\DataView;
use Tuum\View\Renderer;

$this->setLayout('layouts/layout');

?>
<h1>Hello Tuum/Respond</h1>

<p>helpers and responders for Psr-7 http/messages.</p>
<p>This is a package to turn micro-framework into ordinary framework for ordinary web site.</p>
<p>a.k.a. Tuum/Http, </p>

<h3>redirect with messages sample</h3>
<ul>
    <li><a href="jump">jump!!!</a></li>
</ul>

<h3>error samples</h3>

<ul>
    <li><a href="not-such-file">not found</a></li>
    <li><a href="throw">catch exception</a> </li>
</ul>