<?php
include_once "DbApi.php";
include_once "Administrative.php";
include_once "MailUtils.php";
$dbApi = new DbApi();
$administrative = new Administrative();
if(!isset($_SESSION)){
    session_start();
}
switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        # code...
        switch($_GET['find']){
            case 'byHouseNo':
                echo $dbApi->findHouseByNo($_GET['house_no']);
                break;
            case 'province':
                echo $administrative->provinces();
                break;
            case 'district':
                echo $administrative->districtByProvince($_GET['province']);
                break;
            case 'sector':
                echo $administrative->sectorByDistrict($_GET['district']);
                break;
            case 'cell':
                echo $administrative->cellBySector($_GET['sector']);
                break;
            case 'village':
                echo $administrative->villageByCell($_GET['cell']);
                break;
            case 'byResidentNo':
                echo $dbApi->findResidentByID($_GET['residentno']);
                break;
            case 'removeTenant':
                echo $dbApi->removeTenant($_GET['house_no']);
                break;
            case 'jailed':
                echo $dbApi->jailed($_SESSION);
                break;
            case 'removeJailed':
                echo $dbApi->removeJailed($_GET);
                break;
            case 'abroad':
                echo $dbApi->citizenAbroad($_SESSION);
                break;
            case 'removeAbroad':
                echo $dbApi->removeCitizenAbroad($_GET);
                break;
            case 'email':
                echo sendEmail("","","");
                break;
            }
            break;
    case 'POST':
        switch ($_POST['action']) {
            case 'addJailed':
                $_POST['UserId'] = $_SESSION['ID'];
                echo $dbApi->addJailed($_POST);
                break;
            case 'addAbroad':
                $_POST['UserId'] = $_SESSION['ID'];
                echo $dbApi->addCitizenAbroad($_POST);
                break;
        }
        break;
}

?>