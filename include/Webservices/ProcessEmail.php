<?php
function vtws_process_email($email,$element, $user){
    global $log,$adb;
    
	$entityIds = array();
    $log->debug("starting vtws_process_email(".$email.")...");
    $log->debug(print_r($element, True));
    // 10xid
    $sql = "SELECT 
            vtiger_leaddetails.leadid as id
            , vtiger_ws_entity.id as wsid
            , vtiger_leaddetails.firstname
            , vtiger_leaddetails.lastname
            , vtiger_leaddetails.company
            , vtiger_leaddetails.email
            FROM vtiger_leaddetails
            JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_leaddetails.leadid AND vtiger_crmentity.deleted = 0
            JOIN vtiger_ws_entity on vtiger_ws_entity.name = 'Leads'
            WHERE vtiger_leaddetails.email = ?";
    $result = $adb->pquery($sql, array($email));
    
    $noofrows = $adb->num_rows($result);

    if($noofrows) {
    	while($resultrow = $adb->fetch_array($result)) {
    		$entityIds['Leads'][] = $resultrow['wsid']."x".$resultrow['id'];
    	}
    }
    
    
    // 12xid
    $sql = "SELECT 
    vtiger_contactdetails.contactid as id
    , vtiger_ws_entity.id as wsid
    , vtiger_contactdetails.firstname
    , vtiger_contactdetails.lastname
    , vtiger_account.accountname as company
    , vtiger_contactdetails.email
    FROM vtiger_contactdetails
    JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid AND vtiger_crmentity.deleted = 0
    JOIN vtiger_ws_entity on vtiger_ws_entity.name = 'Contacts'
    LEFT JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
    LEFT JOIN vtiger_crmentity accent on accent.crmid = vtiger_account.accountid AND accent.deleted = 0
    WHERE vtiger_contactdetails.email = ?";
    $result = $adb->pquery($sql, array($email));
    $noofrows = $adb->num_rows($result);

    if($noofrows) {
    	while($resultrow = $adb->fetch_array($result)) {
    		$entityIds['Contacts'][] = $resultrow['wsid']."x".$resultrow['id'];
    	}
    }
    // 11xid
    $sql = "SELECT 
    vtiger_account.accountid as id
    , vtiger_ws_entity.id as wsid
    , '' as firstname
    , '' as lastname
    , vtiger_account.accountname
    , vtiger_account.email1 as email
    FROM vtiger_account
    JOIN vtiger_crmentity accent on accent.crmid = vtiger_account.accountid AND accent.deleted = 0
    JOIN vtiger_ws_entity on vtiger_ws_entity.name = 'Accounts'
    WHERE vtiger_account.email1 = ?";
    $result = $adb->pquery($sql, array($email));
    $noofrows = $adb->num_rows($result);

    if($noofrows) {
    	while($resultrow = $adb->fetch_array($result)) {
    		$entityIds['Accounts'][] = $resultrow['wsid']."x".$resultrow['id'];
    	}
    }

    $log->debug("vtws_process_email terminated!");
    return $entityIds;
}
?>
