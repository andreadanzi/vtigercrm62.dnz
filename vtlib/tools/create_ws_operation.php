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


$operationId = vtws_addWebserviceOperation('process_email', 'include/Webservices/danzi.tn/ProcessEmail.php', 'vtws_process_email', 'POST');
vtws_addWebserviceOperationParam($operationId, 'email', 'String', 1);
vtws_addWebserviceOperationParam($operationId, 'element', 'encoded', 2);

?>
