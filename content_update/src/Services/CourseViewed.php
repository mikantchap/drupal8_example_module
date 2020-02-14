<?php

namespace Drupal\content_update\Services;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
//use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * If a user tries to view unpublished content, gets
 * redirected to the home page. Reports this into the Drupal
 * log and sets an apology message. 
 *
 * @author mchaplin
 */
class CourseViewed implements EventSubscriberInterface {
    
    private $loggerfactory;
    
    public function __construct(LoggerChannelFactoryInterface $loggerfactory)
    {
        $this->loggerfactory = $loggerfactory;
    }

    /*
     * Let the user know that the course is no longer running
     * and redirect to home page 
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getResponse()->getStatusCode();

        if ($request == '403') 
        {
            $this->loggerfactory->get('Content Update module')->debug($request);
            drupal_set_message('Sorry that course is no longer available.', 'warning');
            $host = \Drupal::request()->getHost();
            $redirect = new RedirectResponse("http://" . $host);
            $event->setResponse($redirect, 301);
        }
    }    

    public static function getSubscribedEvents()
    {
        $events[KernelEvents::RESPONSE][] = 'onKernelResponse';
        return $events;
    }
}
