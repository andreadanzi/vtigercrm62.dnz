<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
/***********************************************************
danzi.tn@20150427 template per la creazione di un modulo
                  da utilizzare dopo aver creato il modulo con la console
**/
chdir(dirname(__FILE__) . '/../..');
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Package.php';
include_once 'includes/main/WebUI.php';

include_once 'include/Webservices/Utils.php';

$Vtiger_Utils_Log = true;


$SINGLE_MODULENAME = 'Rumor';
$MODULENAME = $SINGLE_MODULENAME.'s';


$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if ($moduleInstance || file_exists('modules/'.$MODULENAME)) {
    $tabid = $moduleInstance->id;
    echo "\nModule ". $MODULENAME . " is present\n";
    Vtiger_Filter::deleteForModule($moduleInstance);
    // Create default custom filter (mandatory)
	$filterAll = new Vtiger_Filter();
	$filterAll->name = 'All';
	$filterAll->isdefault = true;
	$moduleInstance->addFilter($filterAll);
	$fieldName = "name";
	$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
	if( $field )  $filterAll->addField($field);
	else echo "\nField ". $fieldName . " does not exists\n"; 
    
    $block_name = 'LBL_'. strtoupper($moduleInstance->name) . '_INFORMATION';
    $block = Vtiger_Block::getInstance($block_name,$moduleInstance);
    if($block) {
        
        $fieldName = strtolower($moduleInstance->name).'_no';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = $SINGLE_MODULENAME.' Number';
            $field->uitype = 4;
            $field->columntype = 'VARCHAR(100)';
            $field->typeofdata = 'V~O'; //Varchar~Optional
            $block->addField($field); 
        }
        
        
        /** Related to Account */
        $fieldName = 'accountid';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label= 'SINGLE_Accounts';
            $field->uitype = 10;
            $field->columntype = 'INT(19)';
            $field->typeofdata = 'I~O';
            $field->displaytype = 1;
            $field->summaryfield = 1;
            $field->quickcreate = 1;
            $block->addField($field);
            $field->setRelatedModules(Array('Accounts'));
            //relazione 1 a n Accounts (for Customer and Competitor)
            $accounts = Vtiger_Module::getInstance('Accounts');
            $accounts->setRelatedList($moduleInstance, $MODULENAME, Array('ADD','SELECT'), 'get_dependents_list');
        }
        $filterAll->addField($field,1);
        
        /** Rumor Type **/
        $fieldName = strtolower($SINGLE_MODULENAME).'_type';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = $SINGLE_MODULENAME.' Type';
            $field->uitype = 15;
            $field->summaryfield = 1;
            $field->columntype = 'VARCHAR(255)';
            $field->typeofdata = 'V~O';// Varchar~Optional
            $block->addField($field); 
            $field->setPicklistValues( Array ('---', 'News', 'Technical', 'Market', 'Contractors', 'Other') );
        }
        $filterAll->addField($field,2);
        
        /** Rumor Relevance **/
        $fieldName = strtolower($SINGLE_MODULENAME).'_relevance';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = $SINGLE_MODULENAME.' Relevance';
            $field->uitype = 15;
            $field->summaryfield = 1;
            $field->columntype = 'VARCHAR(255)';
            $field->typeofdata = 'V~O';// Varchar~Optional
            $block->addField($field); 
            $field->setPicklistValues( Array ('---', 'Low', 'Medium', 'High') );
        }
        $filterAll->addField($field,3);
        
        /** Due Date **/
        $fieldName = 'due_date';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = 'Due Date';
            $field->uitype = 5;
            $field->summaryfield = 1;
            $field->typeofdata = 'D~O';
            $block->addField($field);           
        }
        $filterAll->addField($field,4);
        
        /** Rumor Status **/
        $fieldName = strtolower($SINGLE_MODULENAME).'_status';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = $moduleInstance->basetable;
            $field->label = 'Status';
            $field->uitype = 15;
            $field->summaryfield = 1;
            $field->columntype = 'VARCHAR(255)';
            $field->typeofdata = 'V~O';// Varchar~Optional
            $block->addField($field); 
            $field->setPicklistValues( Array ('---', 'to be verified', 'verified useful', 'verified useless', 'archived') );
        }
        $filterAll->addField($field,5);

        /** bloccco descrizione */
        $blockDescription = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION',$moduleInstance);
        if( $blockDescription ) {
             echo "\nDescriptio block for ". $MODULENAME . " is available\n";
        } else {
            $blockDescription = new Vtiger_Block();
		    $blockDescription->label = 'LBL_DESCRIPTION_INFORMATION';
		    $moduleInstance->addBlock($blockDescription);
            echo "Descriptio block for ". $MODULENAME . " created!\n";
        }
        
        /** Rumor Description **/
        $fieldName = 'description';
        $field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
        if( $field ) {
            echo "\nField ". $fieldName . " exists\n";
        } else {
            $field = new Vtiger_Field();
            $field->name = $fieldName;
            $field->table = 'vtiger_crmentity';
            $field->label = 'Description';
            $field->uitype = 19;
            $field->typeofdata = 'V~O';// Varchar~Optional
            $blockDescription->addField($field);
        }
        
        /** n:n relations with Potentials**/
        $relModule = Vtiger_Module::getInstance('Potentials');
        $moduleInstance->unsetRelatedList($relModule,'Potentials');
        $moduleInstance->setRelatedList($relModule, 'Potentials',Array('ADD','SELECT'));
        $relModule->unsetRelatedList($moduleInstance,$MODULENAME);
        $relModule->setRelatedList($moduleInstance, $MODULENAME,Array('ADD','SELECT'));
        
        /** n:n relations with Documents**/
        $relModule = Vtiger_Module::getInstance('Documents');
        $moduleInstance->unsetRelatedList($relModule,'Documents','get_attachments');
        $moduleInstance->setRelatedList($relModule, 'Documents',Array('ADD','SELECT'),'get_attachments');
        /** n:n relations with Calendar**/
        $relModule = Vtiger_Module::getInstance('Calendar');
        $moduleInstance->unsetRelatedList($relModule,'Activities','get_activities');
        $moduleInstance->setRelatedList($relModule, 'Activities',Array('ADD'),'get_activities');
        $moduleInstance->unsetRelatedList($relModule,'Activity History','get_history');
        $moduleInstance->setRelatedList($relModule, 'Activity History',Array('ADD'),'get_history');
        /*Dashboard Widgets*/   
        $moduleInstance->addLink('DASHBOARDWIDGET', 'Rumors to be verified', 'index.php?module='.$MODULENAME.'&view=ShowWidget&name=TbvRumors','', '1');
        $moduleInstance->addLink('DASHBOARDWIDGET', 'Rumors by status', 'index.php?module='.$MODULENAME.'&view=ShowWidget&name=RumorsByStatus','', '2');
        $home = Vtiger_Module::getInstance('Home');
        $home->addLink('DASHBOARDWIDGET', 'Rumors to be verified', 'index.php?module='.$MODULENAME.'&view=ShowWidget&name=TbvRumors','', '15');
        $home->addLink('DASHBOARDWIDGET', 'Rumors by status', 'index.php?module='.$MODULENAME.'&view=ShowWidget&name=RumorsByStatus','', '16');
        if(file_exists('modules/ModTracker/ModTrackerUtils.php')) {
	        require_once 'modules/ModTracker/ModTrackerUtils.php';
	        ModTrackerUtils::modTrac_changeModuleVisibility($tabid, 'module_enable');
        }
        
        if(file_exists('modules/ModComments/ModComments.php')) {
	        require_once 'modules/ModComments/ModComments.php';
	        ModComments::removeWidgetFrom($MODULENAME);
	        ModComments::addWidgetTo($MODULENAME);
        }
    } else {
        echo "Block ". $block_name . " is not present\n";
    }
} else {
    echo "Module ". $MODULENAME . " is not present\n";
}

?>
