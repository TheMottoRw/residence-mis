<?php
class Administrative{

    public $conn;
    function __construct(){
        $this->conn = new mysqli('localhost', 'super', '', 'crms'); // Update with your database credentials
    }
    public function provinces(){
        $sql = "SELECT * FROM  provinces";
        $stmt = $this->conn->prepare($sql);
        
        // Check if prepare() failed
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }
    
        // $stmt->bind_param("s", $houseNo);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return json_encode($data);
    }
    public function districtByProvince($id){

        $sql = "SELECT * FROM  districts WHERE ProvinceID = ?";
        $data = $this->retrieve($sql,$id);
        return $data;
    }
    public function sectorByDistrict($id){
        $sql = "SELECT * FROM  sectors WHERE DistrictID = ?";
        $data = $this->retrieve($sql,$id);
        return $data;
    }
    public function cellBySector($id){
        $sql = "SELECT * FROM  cells WHERE SectorID = ?";
        $data = $this->retrieve($sql,$id);
        return $data;
    }
    public function villageByCell($id){
        $sql = "SELECT * FROM  villages WHERE CellID = ?";
        $data = $this->retrieve($sql,$id);
        return $data;
    }
    public function retrieve($query,$param){
        $stmt = $this->conn->prepare($query);
        
        // Check if prepare() failed
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }
    
        $stmt->bind_param("s", $param);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return json_encode($data);
    }
}
?>