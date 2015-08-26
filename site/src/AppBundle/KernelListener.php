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
                }
            }
        }
    }
}

