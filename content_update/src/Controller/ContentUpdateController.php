<?php
namespace Drupal\content_update\Controller;

//use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\content_update\Services\ExternalContent;
use Drupal\content_update\Services\RemoveDrupalContent;
use Drupal\content_update\Services\UpdateCreateDrupalContent;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\content_update\Event\ContentUpdateEvents;
use Drupal\content_update\Event\CourseUpdateEvent;

/**
 * Description of ContentUpdateController
 * Provides the route to do the CRUD on Drupal
 * content provided there is a sensible number
 * of rows in the external source. 
 * 
 * Reports all actions in Drupal log. 
 *
 * @author mchaplin
 */
class ContentUpdateController extends ControllerBase{
    
   /**
   * The event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
    protected $eventDispatcher;
    private $updateCreateContent;
    public  $log = array();

    public function __construct(RemoveDrupalContent $removeCourses, UpdateCreateDrupalContent $updateCreateContent, LoggerChannelFactoryInterface $loggerFactory, EventDispatcherInterface $event_dispatcher) {
    
        $this->removeCourses = $removeCourses;
        $this->updateCreateContent = $updateCreateContent;
        $this->loggerFactory = $loggerFactory;
        $this->eventDispatcher = $event_dispatcher;
    }

    public function update()
    {        
        if ($this->removeCourses->abortFlag)
        {   
            //not enough external content to proceed with update
            $this->log[] = __CLASS__ . ": Aborting update. Insufficient external content.";
        }
        else
        {
            //proceed with updates/creates
            $this->updateCreateContent->createUpdate();
        }
        $this->log = array_merge($this->log, $this->removeCourses->getLog());
        $this->log = array_merge($this->log, $this->updateCreateContent->getLog());
        $logMessage = implode("<br />", $this->log);
        $this->loggerFactory->get('default')->debug($logMessage);
        
        //Dispatch the event including the update log & whether it was aborted
        $this->dispatchEvent($this->log, $this->removeCourses->abortFlag);
        
        return ['#markup' => $logMessage];
        //return new Response('did updates');
    }
    
    private function dispatchEvent($updateLog, $abortFlag)
    {
        $event = new CourseUpdateEvent($updateLog, $abortFlag);
        $this->eventDispatcher->dispatch(ContentUpdateEvents::COURSES_UPDATED, $event);
    }

        public static function create(ContainerInterface $container) {
        $removeCourses = $container->get('content_update.remove_drupal_content');
        $updateCreateContent = $container->get('content_update.update_create_drupal_content');
        $loggerFactory = $container->get('logger.factory');
        $eventDispatcher = $container->get('event_dispatcher');
 
        return new static($removeCourses, $updateCreateContent, $loggerFactory, $eventDispatcher);
    }
}
