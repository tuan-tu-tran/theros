<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Regroup kernel events listeners
 */
class KernelListener
{
    private $router;
    public function __construct(RouterInterface $router)
    {
        $this->router=$router;
    }

    /**
     * Check if the controller requires a logged user.
     * Also, if there is a logged user, check that he has initd his password
     */
    public function onControllerEvent(FilterControllerEvent $event)
    {
        $request=$event->getRequest();
        $controller=$event->getController();
        if(is_array($controller)){
            $instance=$controller[0];
            $action = $controller[1];
            if(is_a($instance, "AppBundle\\Controller\\IProtected")){
                $session = $request->getSession();
                $user=$session->get("user");
                if (!$user) {
                    //redirect to login
                    $event->setController(function(){
                        return new RedirectResponse($this->router->generate("login"));
                    });
                } else {
                    if (!$user->passwordChanged && $request->get("_route") != "init_password") {
                        $event->setController(function(){
                            return new RedirectResponse($this->router->generate("init_password"));
                        });
                    }
                }
            }
        }
    }
}

