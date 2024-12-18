<?php
include_once "Helpers.php";
class DbApi{
    public $conn;
    function __construct(){
        $this->conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials
    }
    function findHouseByNo($houseNo){
            // Fetch authorizer's details from the user table
    $sql = "SELECT t.*,p.Province as ProvinceName,d.District as DistrictName,s.Sector as SectorName,c.Cell as CellName,v.Village as VillageName FROM  houses t  INNER JOIN provinces p ON t.Province=p.ProvinceID INNER JOIN districts d ON d.DistrictID=t.District INNER JOIN sectors s ON s.SectorID=t.Sector INNER JOIN cells c ON c.CellID=t.Cell INNER JOIN villages v ON v.VillageID=t.Village WHERE HouseNo = ?";
    $stmt = $this->conn->prepare($sql);
    
    // Check if prepare() failed
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("s", $houseNo);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return json_encode($data);
}
    function findResidentByID($houseNo){
            // Fetch authorizer's details from the user table
    $sql = "SELECT r.* FROM  resident r WHERE ID = ?";
    $stmt = $this->conn->prepare($sql);

    // Check if prepare() failed
    if ($stmt === false) {
        die('MySQL prepare error: ' . $this->conn->error);
    }

    $stmt->bind_param("s", $houseNo);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return json_encode($data);
}
    function removeTenant($houseNo){
            // Fetch authorizer's details from the user table
        $stmt0 = $this->conn->prepare("UPDATE resident SET HouseNo='',Status='Pending' WHERE HouseNo=?");
        $stmt0->bind_param("s", $houseNo);
        $stmt0->execute();
        header("location: ../tenants.php?message=Successfull removed tenant");
        return;
}
    function addJailed($arr){
            // Fetch authorizer's details from the user table
        $stmt0 = $this->conn->prepare("INSERT jailed SET ResidentId=?,reason=?,JailedBy=?,status='Inprison'");
        $stmt0->bind_param("isi", $arr['ResidentID'],$arr['reason'],$arr['UserId']);
        $stmt0->execute();
        $stmt = $this->conn->prepare("UPDATE resident SET Status='Jailed' WHERE ID=?");
        $stmt->bind_param("i", $arr['ResidentID']);
        if($stmt->execute()) return true;
        return false;
}
    function jailed($arr){
            // Fetch authorizer's details from the user table
        $stmt0 = $this->conn->prepare("SELECT j.*,r.* FROM jailed j INNER JOIN resident r ON r.ID=j.ResidentID WHERE j.JailedBy=?");
        $stmt0->bind_param("i", $arr['ID']);
        $stmt0->execute();

}
    function removeJailed($arr){
            // Fetch authorizer's details from the user table
        $stmt0 = $this->conn->prepare("UPDATE resident SET Status='Available' WHERE ID=?");
        $stmt0->bind_param("i",$arr['ResidentID']);
        $stmt0->execute();
        $stmt = $this->conn->prepare("UPDATE jailed SET status='Released' WHERE id=?");
        $stmt->bind_param("i",$arr['id']);
        $stmt->execute();
        header("location: ../jailed.php?message=Successfull released prisoner");
    }
    function addCitizenAbroad($arr){
            // Fetch authorizer's details from the user table
        $stmt0 = $this->conn->prepare("INSERT citizen_abroad SET ResidentId=?,Country=?,City=?,State=?,AddedBy=?,Status='Abroad'");
        $stmt0->bind_param("isssi", $arr['ResidentID'],$arr['Country'],$arr['City'],$arr['State'],$arr['UserId']);
        $stmt0->execute();
        $stmt = $this->conn->prepare("UPDATE resident SET Status='Abroad' WHERE ID=?");
        $stmt->bind_param("i", $arr['ResidentID']);
        if($stmt->execute()) return true;
        return false;
}
    function citizenAbroad($arr){
            // Fetch authorizer's details from the user table
        $stmt0 = $this->conn->prepare("SELECT cab.*,r.* FROM citizen_abroad cab INNER JOIN resident r ON r.ID=cab.ResidentID WHERE cab.AddedBy=?");
        $stmt0->bind_param("i", $arr['ID']);
        $stmt0->execute();

}
    function removeCitizenAbroad($arr){
            // Fetch authorizer's details from the user table
        $stmt0 = $this->conn->prepare("UPDATE resident SET Status='Pending' WHERE ID=?");
        $stmt0->bind_param("i",$arr['ResidentID']);
        $stmt0->execute();
        $stmt = $this->conn->prepare("UPDATE citizen_abroad SET status='Incountry' WHERE id=?");
        $stmt->bind_param("i",$arr['id']);
        $stmt->execute();
        header("location: ../citizenAbroad.php?message=Successfull citizen back home");
    }
    function rejectCertificateRequest($arr){
            // Fetch authorizer's details from the user table
        $stmt0 = $this->conn->prepare("UPDATE certificate_requests SET status='Rejected',RejectionReason=? WHERE RequestNo=?");
        $stmt0->bind_param("si",$arr['reason'],$arr['RequestNo']);
        $stmt0->execute();
        //send email notification
        $stmt = $this->conn->prepare("SELECT r.Lastname,r.Telephone,r.Email FROM certificate_requests cr INNER JOIN resident r ON r.ID=cr.ID WHERE RequestNo=?");
        $stmt->bind_param("i",$arr['RequestNo']);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        sendRequest(array("to"=>$data['Telephone']."@yopmail.com","subject"=>"Certificate request rejected","body"=>"Dear ".$data['Lastname'].",<br>We are sadened to let you know that your certificate request has been <b><font color='red'>REJECTED</font></b> for the reason of <b>".$arr['reason']."</b><br>Best Regards,<br>CRMS"));
        header("location: ../CertificateRequestsView.php?message=Successfull rejected request");
    }
}
?>