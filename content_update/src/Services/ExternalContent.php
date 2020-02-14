<?php
namespace Drupal\content_update\Services;

use Drupal\content_update\Services\FieldMapping;

/**
 * Gets content from external source eg db and checks it
 *
 * @author mchaplin
 */
class ExternalContent {
    //put your code here
    
    public $externalContent;
    public $externalContentGood;
    public $externalContentBad;
    private $fieldMapping;
    
    public function __construct(FieldMapping $fieldMapping) 
    {
        $this->fieldMapping = $fieldMapping->getFieldMapping();
        $this->setAllContent();
        $this->sortContent();
    }
    
    /*
     * Read all the external data from eg a database or REST API
     * This is just dummied here for simplicity
     */
    public function setAllContent()
    {
        $externalContentRaw[] = array('Full_Description' => 'Basket Weaving Advanced',
                'Overview' => 'You will learn to weave baskets',
                'Qual_Type' => '',
                'UCAS_Course_Code' => '',
                'Course_Code' => 'A1');
        $externalContentRaw[] = array('Full_Description' => 'Cake making',
                'Overview' => 'You will become a great baker',
                'Qual_Type' => 'ACME certificate of baking',
                'UCAS_Course_Code' => '',
                'Course_Code' => 'B2');
        $externalContentRaw[] = array('Full_Description' => 'GCSE Maths',
                'Overview' => 'A GCSE maths course over 2 years',
                'Qual_Type' => 'GCSE',
                'UCAS_Course_Code' => '',
                'Course_Code' => 'B3');
        $externalContentRaw[] = array('Full_Description' => 'A level German',
                'Overview' => 'A level German part time evenings',
                'Qual_Type' => 'A level',
                'UCAS_Course_Code' => '',
                'Course_Code' => 'B4');
        $externalContentRaw[] = array('Full_Description' => 'BSc Computing',
                'Overview' => '2 year foundation degree',
                'Qual_Type' => 'BSc',
                'UCAS_Course_Code' => 'UCAS12345',
                'Course_Code' => 'C5');
        
        //convert to keyed array with unique keys
        foreach ($externalContentRaw as $course){
            $this->externalContent[$course['Course_Code']] = $course;
        }        
    }
    
    /*
     * Sort external data into good and bad depending
     * on whether a required field is empty in a row
     */
    public function sortContent() {
        
        foreach($this->externalContent as $content):
            $badFields = false;

            //if any of the important fields are empty, flag it and break
            $fieldMapping = $this->fieldMapping; 
            foreach ($fieldMapping as $key => $infoFields){
                
                if ($infoFields[2] && empty($content[$key])){
                        //dsm($leaflet->leaflet_code.": ".$fieldname);
                       $badFields = true;
                       break;
                }
            } 

            if ($badFields){
                $this->externalContentBad[$content['Course_Code']] = $content;
            }else{
                $this->externalContentGood[$content['Course_Code']] = $content;   	
            }

        endforeach;        
    }    
}
