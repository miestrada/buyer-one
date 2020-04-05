<?php

namespace App\Services;

use App\Controller\APIJsonRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiJsonRequestSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        $controller = is_array($controller) ? $controller[0] : $controller;

        if ($controller instanceof APIJsonRequestInterface && $event->getRequest()->getContentType() === 'json')
            foreach (json_decode($event->getRequest()->getContent(), true) as $key => $value)
                $event->getRequest()->request->set($key, $value);

    }

}