<?php

/**
 *  MyTrucking - Simple Transport Management
 *  http://www.gomytrucking.com
 *  https://gomytrucking.com
 *
 *  @copyright 2014-2016 MyTrucking Limited
 *
 *  @package MyTrucking
 *
 *  @author David McKinley <dave@gomytrucking.com>
 *
 *  MYTRUCKING CSV EXPORT
 *
 *  Change Log
 *  ------------------------------------------------------------------------------------------------------------------
 *  :: 30 MAR 2015 :: DWM :: Created
 *  :: 14 JAN 2016 :: DWM :: Added Job Lines Sort Order check on Job Date either Ascending or Desending (default DESC)
 *  :: 20 JUN 2016 :: DWM :: Added GL Code and TransCode
 *  :: 23 JUN 2016 :: DWM :: Added Pickup Vehicle
 *
 */

use MyTrucking\Company;

/**
 * This file depends on following "global" variables:
 *
 * @var \MyTrucking\Database\Adapter\Mysqli $db
 * @var Company $company
 * @var array $rstCustomer
 */

/**
 * Deny direct access - count array of returned files (1==self)
 */
if(count(get_included_files())==1) exit("Direct access not permitted.");

require_once 'includes/helpers/globals.php';

$invoice = new invoices($db);

$csv_file_name = "MyTrucking.".date("YmdHis",time()).".csv";

//Get the Last Invoice Number
$LastInvoiceNumber = $rstCustomer['LastInvoiceNumber'];

$fileContent = '';
$fileContent .= "Date, ";
$fileContent .= "Due Date, ";
$fileContent .= "Job ID, ";
$fileContent .= "Order No., ";
$fileContent .= "Product, ";
$fileContent .= "Qty, ";
$fileContent .= "Unit, ";
$fileContent .= "Rate, ";
$fileContent .= "Amount, ";
$fileContent .= "Pickup Vehicle, ";
$fileContent .= "Delivery Vehicle, ";
$fileContent .= "Notes, ";
$fileContent .= "Description, ";
$fileContent .= "Pickup, ";
$fileContent .= "Delivery, ";
$fileContent .= "Reference 1, ";
$fileContent .= "Reference 2, ";
$fileContent .= "Reference 3, ";
$fileContent .= "Product GL Code, ";
$fileContent .= "Transaction Type, ";
$fileContent .= "Customer Code, ";
$fileContent .= "Trading Name, ";
$fileContent .= "First Name, ";
$fileContent .= "Last Name, ";
$fileContent .= "EmailAddress, ";
$fileContent .= "AddressLine1, ";
$fileContent .= "AddressLine2, ";
$fileContent .= "AddressLine3, ";
$fileContent .= "AddressTown, ";
$fileContent .= "AddressPostcode, ";
$fileContent .= "Phone1, ";
$fileContent .= "Phone2, ";
$fileContent .= 'Branch ID';
$fileContent .= "\r\n";                 // End of line carriage return

$sql = "SELECT jobs.*, ";
$sql .= "jobs.JobID as jID, ";
$sql .= "joblines.*, ";
$sql .= "joblines.Note as jNote, ";
$sql .= "stockclass.*, ";
$sql .= "clients.*, ";
$sql .= "pickup_vehicle.VehicleName as PickupVehicle, ";
$sql .= "delivery_vehicle.VehicleName as DeliveryVehicle, ";
$sql .= "origin.TradingName as oTradingName, ";
$sql .= "destination.TradingName as dTradingName ";
$sql .= "FROM jobs ";
$sql .= "LEFT JOIN joblines ON jobs.JobID = joblines.JobID ";
$sql .= "LEFT JOIN clients ON jobs.ChargeClientID = clients.ClientID ";
$sql .= "LEFT JOIN clients as origin ON jobs.OriginClientID = origin.ClientID ";
$sql .= "LEFT JOIN clients as destination ON jobs.DestinationClientID = destination.ClientID ";
$sql .= "LEFT JOIN stockclass ON joblines.ClassID = stockclass.ClassID ";
$sql .= "LEFT JOIN vehicles as pickup_vehicle ON joblines.TruckID1 = pickup_vehicle.VehicleID ";
$sql .= "LEFT JOIN vehicles as delivery_vehicle ON joblines.TruckID2 = delivery_vehicle.VehicleID ";
if ( isset( $uri['multipledaysheet'] ) ) {
    if ( ctype_digit ( $uri['multipledaysheet'] ) ) {
        $sql .= "LEFT JOIN custom_fields cf ON cf.CompanyID = jobs.CompanyID ";
        $sql .= "LEFT JOIN product_daysheet_group pdg ON pdg.customFieldID = cf.customFieldID AND cf.field = " . $uri['multipledaysheet'] . " ";
    }
}
$sql .= "WHERE joblines.CompanyID = " . $company->getId() . " ";
$sql .= "AND joblines.is_split_parent = 0 ";
$sql .= "AND joblines.is_billable = 1 ";
$sql .= "AND joblines.Completed = 1 ";
$sql .= "AND joblines.Invoiced = 0 ";
$sql .= "AND joblines.CancelJob = 0 ";
$sql .= "AND joblines.xeroPosted = 1 ";
$sql .= "AND joblines.price_increase_due = 0 ";
$sql .= "AND clients.ClientCode IS NOT NULL ";
$sql .= "AND TRIM(clients.ClientCode) <> '' ";
if ( isset( $uri['multipledaysheet'] ) ) { $sql .= "AND pdg.productID = stockclass.ClassID "; }
if(_POST('mt_bulk', '')){
    if ( $_POST['mt_bulk'] == '123' ) {
        $sql .= "AND (clients.TradingName like '0%' ";
        $sql .= "  OR clients.TradingName like '1%' ";
        $sql .= "  OR clients.TradingName like '2%' ";
        $sql .= "  OR clients.TradingName like '3%' ";
        $sql .= "  OR clients.TradingName like '4%' ";
        $sql .= "  OR clients.TradingName like '5%' ";
        $sql .= "  OR clients.TradingName like '6%' ";
        $sql .= "  OR clients.TradingName like '7%' ";
        $sql .= "  OR clients.TradingName like '8%' ";
        $sql .= "  OR clients.TradingName like '9%') ";
    } elseif ( $_POST['mt_bulk'] == 'All' ) {
    } else {
        $sql .= "AND clients.TradingName like '" . $_POST['mt_bulk'] . "%' ";
    }
    $sql .= "ORDER BY clients.TradingName, ";
    if($rstCustomer['JobLinesSortOrderOnInvoices']==0) {$sql .= "JobDate, ";} else {$sql .= "JobDate DESC, ";}
} else {
    $sql .= "AND ChargeClientID = ".$_POST['mt']."  ORDER BY ";
    if($rstCustomer['JobLinesSortOrderOnInvoices']==0) {$sql .= "JobDate, ";} else {$sql .= "JobDate DESC, ";}
}
$sql .= "joblines.JobID, joblines.LineItemID ";

//echo $sql."<br>";
//exit;

$result = $db->query($sql);
$i = 1;
$LastClientID = null;

while($row = $result->fetch_assoc()) {
    $i++;

    if($row['ClientID']!=$LastClientID) {
        $LastInvoiceNumber++;
        $LastClientID=$row['ClientID'];
    }

    // Reset vars
    $FirstName = "";
    $LastName = "";
    $EmailAddress = "";
    $AddressLine1 = "";
    $AddressLine2 = "";
    $AddressLine3 = "";
    $AddressPostcode = "";
    $AddressTown = "";

    $InvoiceDate = $row['JobDate'];

    /**
     *   SET BILLING DATE
     */
    $InvoiceDate = $invoice->getInvoiceBillingDate($rstCustomer['InvoiceBillingDate'],$InvoiceDate);

    // Invoice and Invoice Due Dates
    $InvoiceDueDate = $invoice->getInvoiceDueDate($rstCustomer['InvoiceDue'],$InvoiceDate);
    $InvoiceDueDate = date ( "d-m-Y" , strtotime ( "$InvoiceDueDate" ) );

    /**
     *   FIXED PRICE LINEITEM
     *   If this is a fixed price line item
     *   SET Quantity = 1 AND UnitAmount = TotalPrice
     */
    if ( $row['TotalChanged'] == 1 )
    {
        $inv_qty  = 1;
        $inv_rate = $row['TotalPrice'];
    } else {
        $inv_qty  = $row['Quantity'];
        $inv_rate = $row['UnitPrice'];
    }



    // Populate vars
    if(strlen($row['FirstName']) > 0) { $FirstName = xmlEscape($row['FirstName']); }
    if(strlen($row['LastName']) > 0) { $LastName = xmlEscape($row['LastName']); }
    if(strlen($row['EmailAddress']) > 0) { $EmailAddress = xmlEscape($row['EmailAddress']); }
    $AddressLine1 = xmlEscape($row['PostalAddress1']);
    if(strlen($row['PostalAddress2']) > 0) { $AddressLine2 = xmlEscape($row['PostalAddress2']); }
    if(strlen($row['PostalAddress3']) > 0) { $AddressLine3 = xmlEscape($row['PostalAddress3']); }
    if(strlen($row['PostalTown']) > 0) { $AddressTown = xmlEscape($row['PostalTown']); }
    if(strlen($row['PostalPostCode']) > 0) { $AddressPostcode = xmlEscape($row['PostalPostCode']); }

    // Add data
    $fileContent .= forward_reverse_date($InvoiceDate).",";
    $fileContent .= $InvoiceDueDate.",";
    $fileContent .= $row['jID'].",";
    $fileContent .= str_replace(",","",$row['OrderNumber']).",";
    $fileContent .= str_replace(",","",$row['ClassName']).",";
    $fileContent .= $inv_qty.",";
    $fileContent .= str_replace(",","",$row['Unit']).",";
    $fileContent .= number_format($inv_rate,2,".","").",";
    $fileContent .= number_format($row['TotalPrice'],2,".","").",";

    if ( strlen ( $row['PickupVehicle'] > 0 ) && strlen ( $row['DeliveryVehicle'] == 0 ) ) {

        /**
         *    Pickup vehicle selected but not the Delivery vehicle
         *    make the Delivery vehicle same as the Pickup vehicle
         */
        $fileContent .= str_replace(",","",$row['PickupVehicle']).",";
        $fileContent .= str_replace(",","",$row['PickupVehicle']).",";

    } elseif ( strlen ( $row['DeliveryVehicle'] > 0 ) && strlen ( $row['PickupVehicle'] == 0 ) ) {

        /**
         *    Delivery vehicle selected but not the Pickup vehicle
         *    make the Pickup vehicle same as the Delivery vehicle
         */
        $fileContent .= str_replace(",","",$row['DeliveryVehicle']).",";
        $fileContent .= str_replace(",","",$row['DeliveryVehicle']).",";

    } else {

        /**
         *    Vehicles are either different or both are blank - leave as is
         */
        $fileContent .= str_replace(",","",$row['PickupVehicle']).",";
        $fileContent .= str_replace(",","",$row['DeliveryVehicle']).",";

    }

    $fileContent .= str_replace(",","",trim($row['jNote'])).",";
    $fileContent .= str_replace(",","",trim($row['InvoiceNote'])).",";
    $fileContent .= str_replace(",","",trim($row['oTradingName'])).",";
    $fileContent .= str_replace(",","",trim($row['dTradingName'])).",";
    $fileContent .= str_replace(",","",$row['Reference1']).",";
    $fileContent .= str_replace(",","",$row['Reference2']).",";
    $fileContent .= str_replace(",","",$row['Reference3']).",";
    $fileContent .= str_replace(",","",$row['ProductGLCode']).",";
    $fileContent .= str_replace(",","",$row['TransactionType']).",";
    $fileContent .= str_replace(",","",$row['ClientCode']).",";
    $fileContent .= str_replace(",","",$row['TradingName']).",";
    $fileContent .= str_replace(",","",$FirstName).",";
    $fileContent .= str_replace(",","",$LastName).",";
    $fileContent .= htmlentities($row['EmailAddress']).",";
    $fileContent .= str_replace(",","",$AddressLine1).",";
    $fileContent .= str_replace(",","",$AddressLine2).",";
    $fileContent .= str_replace(",","",$AddressLine3).",";
    $fileContent .= str_replace(",","",$AddressTown).",";
    $fileContent .= $AddressPostcode.",";
    $fileContent .= str_replace(",","",trim($row['Phone1'])).",";
    $fileContent .= str_replace(",","",trim($row['Phone2'])).",";
    switch ($company->getId()) {
        case 122: // Ellesmere
            $branchId = 'MAIN';
            break;
        case 155: // Cheviot
            $branchId = 'CT';
            break;
        case 1216: // Banks Peninsula
            $branchId = 'BPT';
            break;
        default:
            $branchId = '';
            break;
    }
    $fileContent .= $branchId;
    $fileContent .= "\r\n";                 // End of line carriage return
}

//Update the Last Invoice Number
update_string("customers","LastInvoiceNumber",$LastInvoiceNumber,"CustomerID", $company->getId());

/**
 * Update Invoiced Flag for each Activity invoiced
 */
$audit_session=create_audit_session_id();
$result->data_seek(0);
while ($row = $result->fetch_assoc()) {
    $sql='UPDATE joblines SET Invoiced = 1, InvoicedTimestamp = "'.date("Y-m-d H:i:s").'" WHERE LineItemID = "'.$row['LineItemID'].'" AND JobID = "'.$row['JobID'].'"';
    //$sql='UPDATE joblines SET InvoicedTimestamp = "'.date("Y-m-d H:i:s").'" WHERE LineItemID = "'.$row['LineItemID'].'" AND JobID = "'.$row['JobID'].'"';
    if(!$db->query($sql)) {
        $msg.=$db->error.'<br/>';
    } else {
        audit_change('joblines',$row['LineItemID'],'Invoiced','','1',$_SESSION['login_user'],$audit_session);
    }
}

// Redirect output to a client's web browser (Excel5)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment;filename='.$csv_file_name);
print $fileContent;

?>