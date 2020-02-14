<?php
namespace Drupal\content_update\Services;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\content_update\Services\ExternalContent;

/**
 * Description of RemoveDrupalContent
 * Removes stale content from Drupal that 
 * no longer exists in the external source
 * provided there is a sensible number of rows
 * in the external source
 *
 * @author mchaplin
 */
class RemoveDrupalContent {
    
    private $removeAction;
    private $allExistingCourses = array();
    private $externalContent;
    private $coursesToRemove;
    private $rowsmin;
    private $entityQuery;
    public  $log = array();
    public  $abortFlag = false;


    public function __construct($removeAction, QueryFactory $entityQuery, ExternalContent $externalContent, $rowsmin) {
        $this->removeAction = $removeAction;
        $this->entityQuery = $entityQuery;
        $this->externalContent = $externalContent;
        $this->rowsmin = $rowsmin;
        $this->getAllCourses();
        
        if (count($this->externalContent->externalContentGood) > $this->rowsmin)
        {
            $this->removeDrupalLeaflets();
        }    
        else
        {    
            $this->log[] = __CLASS__ . ": Less than " . $this->rowsmin . " courses. Aborting";
            $this->abortFlag = true;
        }
    }
    
    /*
     * Get all published courses
    */
    public function getAllCourses(){

        $entity_query_service = $this->entityQuery; //$container->get('entity.query');
        $queryCourses = $entity_query_service->get('node')
                    ->condition('status', 1)
                    ->condition('type', 'course');
        $nids = $queryCourses->execute();

        if ($nids) {
            foreach ($nids as $key => $nid):
                $node = entity_load('node', $nid);
                $this->allExistingCourses[$node->field_course_code->getString()] = $node;
            endforeach;
        }
    }
    
    /*
     * Compare Drupal leaflets with incoming EBS leaflets
     * If the Drupal value is absent from the incoming leaflet
     * data, delete/unpublish the node in Drupal. 
     */
    public function removeDrupalLeaflets(){
	$externalContent = $this->externalContent->externalContentGood;
        $this->coursesToRemove = array_diff_key($this->allExistingCourses, $externalContent);
        //kint($this->coursesToRemove);
        
        if ($this->removeAction == 'delete'){
            $nids = array();
            foreach ($this->coursesToRemove as $courseCode => $node):
                $nids[] = $node->nid->getString();
                $this->log[] = __CLASS__ . ": Removing $courseCode";
            endforeach;
           
            $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
            $entities = $storage_handler->loadMultiple($nids);
            $storage_handler->delete($entities);
            
        }else if ($this->removeAction == 'unpublish'){
            foreach ($this->coursesToRemove as $courseCode => $node):
                $node->setPublished(false);
                $node->save();
                $this->log[] = __CLASS__ . ": Removing $courseCode";
            endforeach;    
        }
        
        $this->log[] = __CLASS__ . ": Removed " . count($this->coursesToRemove) . " courses.";

    }

    public function getRemoveAction() {
        return $this->removeAction;
    }
    
    public function getLog() {
        return $this->log;
    }
}
