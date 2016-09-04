<?php

use App\App\Dispatcher;
use App\App\DocumentMap;
use App\App\UploadController;
use Koriym\Printo\Printo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Form\Components\NavBar;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;

return function (Dispatcher $app) {

    /** @var Responder $responder */
    $responder = $app->get(Responder::class);
    
    /**
     * for top page /
     */
    $app->add('/',
        function (ServerRequestInterface $request, ResponseInterface $response) use ($responder) {
            if (!$responder->session()->get('first.time')) {
                $responder->session()->set('first.time', true);
                $responder->getViewData()
                    ->setSuccess('Thanks for downloading Tuum/Respond.');
            }
            return $responder
                ->view($request, $response)
                ->render('index');
        });

    $app->add('/login',
        function (ServerRequestInterface $request, ResponseInterface $response) use($responder) {
            $post = $request->getParsedBody();
            $view = $responder->getViewData();
            if (isset($post['logout'])) {
                $responder->session()->set('login.name', null);
                $view->setSuccess('logged out');
            }
            elseif (isset($post['login'])) {
                if ($post['login']) {
                    $responder->session()->set('login.name', $post['login']);
                    $view->setSuccess('logged as: ' . $post['login']);
                } else {
                    $view->setAlert('enter login name');
                }
            }
            return $responder
                ->withView($view)
                ->redirect($request, $response)
                ->toPath('/');
        });

    /**
     * for displaying form for /jump
     */
    $app->add('/jump', \App\App\Controller\JumpController::class);

    /**
     * file upload sample, /upload.
     */
    $app->add('/upload', UploadController::class);

    /**
     * for other samples
     */
    $app->add('/content',
        function (ServerRequestInterface $request, $response) {
            return Respond::view($request, $response)
                ->asContents('<h1>Contents</h1><p>this is a string content in a layout file</p>', [
                    'nav' => new NavBar('samples', 'content')
                ]);
        });

    $app->add('/objGraph',
        function (ServerRequestInterface $request, $response) {
            return Respond::view($request, $response)
                ->asContents((new Printo(Respond::getResponder($request))), [
                    'nav' => new NavBar('samples', 'objGraph')
                ]);
        });

    /**
     * @throw \Exception
     */
    $app->add('/throw',
        function () {
            throw new \Exception('always throws exception');
        });

    $app->add('/forbidden', function ($req, $res) use($responder) {
        return $responder->view($req, $res)->render('forbidden');
    });

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $app->add('/forms',
        function (ServerRequestInterface $request, $response) {
            Respond::getViewData($request)
                ->setData([
                    'text' => 'this is text-value',
                    'date' => date('Y-m-d'),
                ]);
            return Respond::view($request, $response)->render('forms');
        });

    $app->add('/docs/(?P<pathInfo>.*)', DocumentMap::class);
    
    return $app;
};