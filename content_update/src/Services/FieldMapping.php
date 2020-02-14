<?php

namespace Drupal\content_update\Services;
/**
 * Description of FieldMapping
 *
 * @author mchaplin
 */
class FieldMapping {
    
    private $fieldMapping = array();
    
    public function __construct() {
        $this->setFieldmapping();
    }
    
    /*
     * Maps external field (db?) names to Drupal field names
     */
    public function setFieldmapping()
    {    
        //External source fieldname => array(Drupal Field machine name, Drupal label (for info), required (boolean))
        $this->fieldMapping = array(
                'Full_Description' => array('title', 'title', true),
                'Overview' => array('body', 'Overview', true),
                'Qual_Type' => array('field_qualification_type', 'Qualification type', false),
                'UCAS_Course_Code' => array('field_ucas_code', 'UCAS', false),
                'Course_Code' => array('field_course_code', 'Course code', true)
        );  

    }    
    
    public function getFieldMapping()
    {
        return $this->fieldMapping;
    }
}
