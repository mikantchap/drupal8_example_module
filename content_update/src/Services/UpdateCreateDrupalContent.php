<?php
namespace Drupal\content_update\Services;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\content_update\Services\ExternalContent;
use Drupal\node\Entity\Node;
use Drupal\content_update\Services\FieldMapping;

/**
 * Description of UpdateCreateDrupalContent
 * Creates or updates Drupal content based on the
 * course code.
 *
 * @author mchaplin
 */
class UpdateCreateDrupalContent {
    
    private $externalContent;
    private $entityQuery;
    private $fieldMapping;
    private $log = array();

    public function __construct(QueryFactory $entityQuery, ExternalContent $externalContent, FieldMapping $fieldMapping) 
    {
        $this->externalContent = $externalContent;
        $this->entityQuery = $entityQuery;
        $this->fieldMapping = $fieldMapping->getFieldMapping();
    }
    
    public function createUpdate()
    {
      foreach ($this->externalContent->externalContentGood as $key => $fieldValues)
      {
        $entity_query_service = $this->entityQuery;
        $queryCourses = $entity_query_service->get('node')
                    ->condition('status', 1)
                    ->condition('type', 'course')
                    ->condition('field_course_code', $fieldValues['Course_Code'], '=');
        $nid = $queryCourses->execute();

        if (count($nid) == 0)
        {
            $this->log[] = __CLASS__ . ': Creating course '. $fieldValues['Course_Code'];
            $this->createCourse($fieldValues);
        }
        else if (count($nid) == 1)
        {
            $this->log[] = __CLASS__ . ': Updating course '. $fieldValues['Course_Code'];
            $this->updateCourse($fieldValues, $nid);    
        }
        else if (count($nid) > 1)
        {
            //Report and do nothing
            $this->log[] = __CLASS__ .': Duplicates courses found for '. $fieldValues['Course_Code'];
        }    
      }
    }

    private function createCourse($fieldValues) {
        
        $nodeValues = array();
        foreach ($this->fieldMapping as $exFieldTitle => $values)
        {
            $nodeValues[$values[0]] = $fieldValues[$exFieldTitle];
        }
        $nodeValues['type'] = 'course';
        $nodeValues['status'] = 1;
        
        $node = Node::create($nodeValues);        
        $node->save();        
	$this->log[] = __CLASS__ . ": Created node id ". $node->nid->getString(). " " . $fieldValues['Course_Code'] . " " . $fieldValues['Full_Description'];
    }
    
    private function updateCourse($fieldValues, $nid) {
        $node = Node::load(array_pop($nid));
        
        foreach ($this->fieldMapping as $exFieldTitle => $values)
        {
            $node->{$values[0]}->value = $fieldValues[$exFieldTitle];
        }
        $node->save();
    }
    
    public function getLog() {
        return $this->log;
    }
}
