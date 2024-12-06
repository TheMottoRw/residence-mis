<?php 
// include database connection file

$con=mysqli_connect("localhost","super","","crms");
if (mysqli_connect_errno()) {
	?>
	<script>
		window.alert("Could not connect to database. \nPlease try again later.")

	</script>
    <?php
}
else{
	if (isset($_POST["Province"]) && !empty($_POST["Province"])) {
		// get all district

		$sql="SELECT DistrictID,District FROM districts WHERE Province = ? ORDER BY district ASC";
		
		$stmt=$con->prepare($sql);
		$stmt->bind_param('i',$_POST["Province"]);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($districtid,$district);

		// count rows selected
		$rows=$stmt->num_rows;
		//display all districts

		if ($rows != 0) {
			echo '<option value="">~Select District</option>';
			while ($stmt->fetch()) {
				echo '<option value='.$districtid.'>'.$district.'</option>';
			}
		}
		else{
			echo '<option value="">~Districts not available</option>';
		}
	}

	if (isset($_POST['District']) && !empty($_POST['District'])) {
		// get all sectors

		$sql="SELECT SectorID,Sector FROM sectors WHERE District=? ORDER BY sector ASC";
		
		$stmt=$con->prepare($sql);
		$stmt->bind_param('i',$_POST['District']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($sectorid,$sector);

		// count rows selected
		$rows=$stmt->num_rows;
		//display all sectors
		if ($rows > 0) {
			echo '<option value="">~Select Sector</option>';
			while ($stmt->fetch()) {
				echo '<option value='.$sectorid.'>'.$sector.'</option>';
			}
		}
		else{
			echo '<option value="">~Sectors not available</option>';
		}
	}

	if (isset($_POST['Sector']) && !empty($_POST['Sector'])) {
		// get all cells

		$sql="SELECT CellID,Cell FROM cells WHERE sector=? ORDER BY cell ASC";
		
		$stmt=$con->prepare($sql);
		$stmt->bind_param('i',$_POST['Sector']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($cellid,$cell);

		// count rows selected
		$rows=$stmt->num_rows;
		//display all cells
		if ($rows > 0) {
			echo '<option value="">~Select cell</option>';
			while ($stmt->fetch()) {
				echo '<option value='.$cellid.'>'.$cell.'</option>';
			}
		}
		else{
			echo '<option value="">~Cells not available</option>';
		}
	}


	if (isset($_POST['Cell']) && !empty($_POST['Cell'])) {
		// get all villages

		$sql="SELECT VillageID,Village FROM villages WHERE cell=? ORDER BY village ASC";
		
		$stmt=$con->prepare($sql);
		$stmt->bind_param('i',$_POST['Cell']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($villageid,$village);

		// count rows selected
		$rows=$stmt->num_rows;
		//display all villages
		if ($rows > 0) {
			echo '<option value="">~Select village</option>';
			while ($stmt->fetch()) {
				echo '<option value='.$villageid.'>'.$village.'</option>';
			}
		}
		else{
			echo '<option value="">~villages not available</option>';
		}
	}

}

?>