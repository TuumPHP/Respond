<?php
namespace App\App\Controller;

use App\App\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Controller\PresenterTrait;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder;

class LoginPresenter implements PresenterInterface
{
    use PresenterTrait;

    /**
     * @param Dispatcher $app
     * @return LoginPresenter
     */
    public static function forge($app)
    {
        $self            = new self;
        $self->responder = $app->get(Responder::class);

        return $self;
    }

    /**
     * renders $view and returns a new $response.
     *
     * @param ViewDataInterface      $viewData
     * @return ResponseInterface
     */
    public function dispatch($viewData)
    {
        $login = $this->responder->session()->get('login.name');
        if ($login) {
            return $this->view($viewData)
                ->render('layouts/UserHeaderLogIn', ['login' => $login]);
        }

        return $this->view($viewData)
            ->render('layouts/UserHeaderLoginForm');
    }
}