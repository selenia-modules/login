<?php
namespace Selenia\Plugins\Login\Config;

use Electro\Core\Assembly\Services\ModuleServices;
use Electro\Interfaces\DI\InjectorInterface;
use Electro\Interfaces\Http\RequestHandlerInterface;
use Electro\Interfaces\Http\RouterInterface;
use Electro\Interfaces\ModuleInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Selenia\Plugins\Login\Controllers\Login;

class LoginModule implements ModuleInterface, RequestHandlerInterface
{
  /** @var RouterInterface */
  private $router;
  /** @var LoginSettings */
  private $settings;

  function __invoke (ServerRequestInterface $request, ResponseInterface $response, callable $next)
  {
    return $this->router
      ->set ([
        $this->settings->urlPrefix () . '...' => [
          'login'  => Login::class,
          'logout' => controller ([Login::class, 'logout']),
        ],
      ])
      ->__invoke ($request, $response, $next);
  }

  function configure (InjectorInterface $injector, ModuleServices $module, LoginSettings $settings,
                      RouterInterface $router)
  {
    $injector->share (LoginSettings::class);
    $this->settings = $settings;
    $this->router   = $router;
    $module
      ->provideTranslations ()
      ->provideViews ()
      ->publishPublicDirAs('modules/selenia-modules/login')
      ->registerRouter ($this, 'login', 'platform');
  }

}