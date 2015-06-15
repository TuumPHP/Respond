<?php
/** @var Renderer $this */
/** @var DataView $view */
use Tuum\Form\DataView;
use Tuum\View\Renderer;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Tuum/Respond Sample</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</head>
<body>

<nav id="header" class="navbar navbar-inverse">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="/">Tuum/Respond</a>
        </div>
    </div>
</nav>

<div class="content">

    <div class="col-md-12">

        <?= $this->getContent();?>

    </div>

</div>

<nav id="footer" class="nav navbar-fixed-bottom navbar-default">
    <div class="container">
        <h4>Tuum/Respond.</h4>
        <p><em>Tuum</em> means 'yours' in Latin; so it happens to the same pronunciation as 'stack' in Japanese. </p>
    </div>
</nav>

</body>
</html>