<?php
include_once "includes/session_manager.php";
session_start();
session_controller();
ob_start();
$logged_in_user=$_SESSION['user'];
$logged_in_branch=$_SESSION['branch'];
$branch_name=$_SESSION['branch_name'];
$role=$_SESSION['role'];
$setup = "../setup.php";
$level = 2;

if (file_exists($setup)) {
	include_once($setup);
	include(include_file($functions, $level));
	include(include_file($uservar, $level)); // Variables of The User who is logged in
	// if($User_Role != 1){
	// 	// Redirect user if user type is not 1
	// 	header("location: ".include_file($home, $level));
	// }	
} // Endof If Statement if setup is included
else {
	echo "Debug Message: Setup Not Available";
	die();
} // Endof Else Statement

?>
<!DOCTYPE html>
<html>
<head>
	<?php
		if(file_exists(include_file($linkheader, $level))){
			include(include_file($linkheader, $level)); 
		}
	?>
	<title><?php echo SITE_NAME; ?></title>
    <script src="jquery-3.5.1.js"></script>
</head>
<body>
	<header>
		<div id="navbar">
			<?php
				if(file_exists(include_file($linknavbar, $level))){
					include(include_file($linknavbar, $level));
				}
			?>
		</div>
	</header>

	<section id="container">
        <div id="row" class="text-center">
            <h1>Welcome to <?php echo SITE_NAME; ?></h1>
        </div>
        <br>
        <div id="content">
        	<div class="contentdata">
                    <div class="col-md-3">

                        <?php
                        if ($role==1) {

                        ?>

                        <div class="nav ">
                            <p><a href="<?php echo include_file($authHome_pages, $level); ?>">Home</a></p>
                        </div>
                        
                        <div class="nav active">
                            <p><a href="<?php echo include_file($accounts_pages, $level); ?>">Accounts</a></p>
                        </div>
                        <div class="nav ">
                            <p><a href="<?php echo include_file($mondators_pages, $level); ?>">Mondators</a></p>
                        </div>
                        <div class="nav ">
                            <p><a href="<?php echo include_file($employees_pages, $level); ?>">Employees</a></p>
                        </div>
                        <div class="nav ">
                            <p><a href="<?php echo include_file($reports_pages, $level); ?>">Reports</a></p>
                        </div>
                        <div class="nav">
                            <p><a href="<?php echo include_file($notifications_pages, $level); ?>">Notifications</a></p>
                        </div>

                        <?php

                        }
                        elseif ($role==2 || $role==4) {
                        ?>

                        <div class="nav">
                            <p><a href="<?php echo include_file($authHome_pages, $level); ?>">Home</a></p>
                        </div>
                        <div class="nav">
                            <p><a href="<?php echo include_file($transactions_pages, $level); ?>">Transactions</a></p>
                        </div>
                        <div class="nav active">
                            <p><a href="<?php echo include_file($accounts_pages, $level); ?>">Accounts</a></p>
                        </div>
                        <div class="nav ">
                            <p><a href="<?php echo include_file($mondators_pages, $level); ?>">Mondators</a></p>
                        </div>
                        <div class="nav ">
                            <p><a href="<?php echo include_file($reports_pages, $level); ?>">Reports</a></p>
                        </div>
                        <div class="nav">
                            <p><a href="<?php echo include_file($notifications_pages, $level); ?>">Notifications</a></p>
                        </div>
                        <?php
                         }
                        else{
                        ?>

                        <div class="nav ">
                            <p><a href="<?php echo include_file($authHome_pages, $level); ?>">Home</a></p>
                        </div>
                        <div class="nav">
                            <p><a href="<?php echo include_file($transactions_pages, $level); ?>">Transactions</a></p>
                        </div>
                        <div class="nav active">
                            <p><a href="<?php echo include_file($accounts_pages, $level); ?>">Accounts</a></p>
                        </div>
                        <div class="nav ">
                            <p><a href="<?php echo include_file($mondators_pages, $level); ?>">Mondators</a></p>
                        </div>
                        <div class="nav">
                            <p><a href="<?php echo include_file($notifications_pages, $level); ?>">Notifications</a></p>
                        </div>
                        <?php }?>
                        <div class="nav time">
                            <p class="day"><?php echo dateChange(getCurrentTime()); ?></p>
                            <p class="time"><?php echo TimeChange(getCurrentTime()); ?></p>
                            <p class="out"><a href="<?php echo include_file($signout_pages, $level); ?>">Log Out</a></p>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div id="tab">
                            <div id="myTabContent" class="tab-content">
                                <nav>
                                    <ul id="myTab">
                                        <li><a href="<?php echo include_file($accounts_pages, $level); ?>"><span class="fa fa-eye"></span>View All Clients</a></li>
                                        <li class="active"><a href="javascript:;"><span class="glyphicon glyphicon-plus"></span>Create Account</a></li>
                                    </ul>
                                </nav>
                                <div id="FirstTerm">
                                    <div class="FirstTerm">
                                        <div class="middle">
                                            <form name="reg" method="post" enctype="multipart/data-form" action="<?php echo $_SERVER['PHP_SELF']."?action=next";?>">

                                                <div class="form-group">
                                                    <label for="title">Client National ID* :</label>
                                                    <input class="form-control" type="number" name="nid" min-length="16" max-length="16" placeholder="Enter Client National ID Number" <?php echo !empty(retainInput('nid'))?"value = '".retainInput('tel')."'":'';?>  />
                                                    <li style="list-style:none">
                                                        <?php if(isset($validation_u_message)){ echo $validation_u_message;} ?>
                                                    </li>
                                                </div>
                                                

                                                <div class="form-group">
                                                    <label for="title">First Name* :</label>		
                                                    <input class="form-control" type="text" name="firstname" placeholder="Enter Client First Name" <?php echo !empty(retainInput('firstname'))?"value = '".retainInput('firstname')."'":'';?> required />
                                                </div>

                                                <div class="form-group">
                                                    <label for="title">Middle Name* :</label>		
                                                    <input class="form-control" type="text" name="middlename" placeholder="Enter Client Middle Name" <?php echo !empty(retainInput('middlename'))?"value = '".retainInput('middlename')."'":'';?> />
                                                </div>

                                                <div class="form-group">
                                                    <label for="title">Last Name* :</label>		
                                                    <input class="form-control" type="text" name="lastname" placeholder="Enter Client Last Name" <?php echo !empty(retainInput('lastname'))?"value = '".retainInput('lastname')."'":'';?> required />
                                                </div>

                                                <div class="form-group">
                                                    <label for="title">Client Telephone* :</label>
                                                    <input class="form-control" type="number" name="tel" max-length="12" placeholder="Enter Client tel Number" <?php echo !empty(retainInput('tel'))?"value = '".retainInput('tel')."'":'';?>  />
                                                    <li style="list-style:none">
                                                        <?php if(isset($validation_u_message)){ echo $validation_u_message;} ?>
                                                    </li>
                                                </div>

                                                <div class="form-group">
                                                    <label for="title">Email* :</label>
                                                    <input class="form-control" type="email" name="email" placeholder="Enter Client email" <?php echo !empty(retainInput('email'))?"value = '".retainInput('email')."'":'';?> required />
                                                    <li style="list-style:none">
                                                        <?php if(isset($validation_u_message)){ echo $validation_u_message;} ?>
                                                    </li>
                                                </div>

                                                <div class="form-group">
                                                    <label for="title">Client Branch* :</label>
                                                    <select name="branch" id="branch" readonly>
                                                        <option value="">--Select Branch--</option>
                                                        <option value="<?php echo $logged_in_branch;?>" selected><?php
                                                        echo $branch_name;
                                                        ?></option>
                                                    </select>
                                                    <li style="list-style:none">
                                                        <?php if(isset($validation_u_message)){ echo $validation_u_message;} ?>
                                                    </li>
                                                </div>

                                                <div class="form-group">
                                                    <label for="title">Client Gender* :</label>
                                                    <select name="gender" id="">
                                                        <option value="">--Select Gender--</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                    <li style="list-style:none">
                                                        <?php if(isset($validation_u_message)){ echo $validation_u_message;} ?>
                                                    </li>
                                                </div>

                                                <div class="form-group">
                                                    <label for="title">Client Birthday* :</label>
                                                    <input class="form-control" type="date" name="bd" placeholder="Enter Client Birthday" <?php echo !empty(retainInput('bd'))?"value = '".retainInput('bd')."'":'';?>  />
                                                    <li style="list-style:none">
                                                        <?php if(isset($validation_u_message)){ echo $validation_u_message;} ?>
                                                    </li>
                                                </div>

                                                <div class="form-group">
                                                    <label for="title">Job Title* :</label>
                                                    <input class="form-control" type="text" name="job" placeholder="Enter Client Job Title" <?php echo !empty(retainInput('nid'))?"value = '".retainInput('tel')."'":'';?>  />
                                                    <li style="list-style:none">
                                                        <?php if(isset($validation_u_message)){ echo $validation_u_message;} ?>
                                                    </li>
                                                </div>

                                                <div class="form-group">
                                                    <label for="title">Client Province* :</label>
                                                    <select name="province" id="province">
                                                        <option value="">--Select Province--</option>
                                                        <?php
                                                        $provinceQ=mysqli_query($conn,"SELECT * FROM provinces ORDER BY province");
                                                        while($result=mysqli_fetch_assoc($provinceQ)){
                                                            echo "<option value='".$result['id']."'>".$result['province']."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <li style="list-style:none">
                                                        
                                                    </li>
                                                </div>
                                                <div class="form-group">
                                                    <label for="title">Client District* :</label>
                                                    <select name="district" id="district">
                                                        <option value="">--Select District--</option>
                                                    </select>
                                                    <li style="list-style:none">
                                                    </li>
                                                </div>
                                                <div class="form-group">
                                                    <label for="title">Client Sector* :</label>
                                                    <select name="sector" id="sector">
                                                        <option value="">--Select Sector--</option>
                                                    </select>
                                                    <li style="list-style:none">
                                                        
                                                    </li>
                                                </div>
                                                <div class="form-group">
                                                    <label for="title">Client Cell* :</label>
                                                    <select name="cell" id="cell">
                                                        <option value="">--Select Cell--</option>
                                                    </select>
                                                    <li style="list-style:none">
                                                        
                                                    </li>
                                                </div>
                                                <div class="form-group">
                                                    <label for="title">Client Village* :</label>
                                                    <select name="village" id="village">
                                                        <option value="">--Select Village--</option>
                                                    </select>
                                                    <li style="list-style:none">
                                                        
                                                    </li>
                                                </div>


                                                <div class="form-group">
                                                    <label for="title">Come Together* :</label>
                                                    <select name="together">
                                                        <option value="">--Select Together--</option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no">No</option>
                                                    </select>
                                                    <li style="list-style:none">
                                                    </li>
                                                </div>

                                               
                                                
                                                <!-- Sign Up buttons -->
                                                <div id="SignUpButtons">
                                                    <input name="st" type="hidden" value="subtd" />
                                                    </span><input class="reg-button btn btn-info" type="submit" value="Create Account"/>
                                                </div>
                                            </form> 
                                        </div>

                                        <?php
                                            if(isset($_POST['st'])){
                                                $validate = regVal();
                                                if($validate){
                                                    
                                                    $email = mysqli_real_escape_string($conn, $_POST['email']);
                                                    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
                                                    $middlename = mysqli_real_escape_string($conn, $_POST['middlename']);
                                                    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
                                                    $tel = mysqli_real_escape_string($conn, $_POST['tel']);
                                                    $nid = mysqli_real_escape_string($conn, $_POST['nid']);
                                                    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
                                                    $gender = mysqli_real_escape_string($conn, $_POST['gender']);  
                                                    $job = mysqli_real_escape_string($conn, $_POST['job']);  
                                                    $together = mysqli_real_escape_string($conn, $_POST['together']);  
                                                    $bd = mysqli_real_escape_string($conn, $_POST['bd']);   
                                                    $province = mysqli_real_escape_string($conn, $_POST['province']);
                                                    $district = mysqli_real_escape_string($conn, $_POST['district']);
                                                    $sector = mysqli_real_escape_string($conn, $_POST['sector']);
                                                    $cell = mysqli_real_escape_string($conn, $_POST['cell']);
                                                    $village = mysqli_real_escape_string($conn, $_POST['village']);         
                                                    $password = 1234567890;

                                                    //Hashing the password
                                                    $password1 = bin2hex(mhash(MHASH_MD5, $password, $salt));

                                                    // Client username generate

                                                    $lastId = getClientLastId();
                                                    $newClientId=$lastId+1;
                                                    $username=generateClientUsername($newClientId);

                                                    $query = mysqli_query($conn, "INSERT INTO users(name, email, password) VALUES(\"$username\", \"$email\", \"$password1\")") or die(mysqli_error($conn));

                                                    if($query){
                                                        $userId = mysqli_insert_id($conn); 

                                                        /// employee address searching
                                                        $addressQuery1=mysqli_query($conn,"SELECT id FROM addresses WHERE province=\"$province\" AND district=\"$district\" AND sector=\"$sector\" AND cell=\"$cell\" AND village=\"$village\"") or die(mysqli_error($conn));

                                                        if(mysqli_num_rows($addressQuery1) > 0){
                                                            while($addressData = mysqli_fetch_assoc($addressQuery1)){
                                                                $address = $addressData['id'];

                                                                $queryClient = mysqli_query($conn, "INSERT INTO clients(user, branch, address, firstname, middlename, lastname, gender, nid, email, tel, bd, job_title,together) VALUES(\"$userId\", \"$branch\", \"$address\", \"$firstname\", \"$middlename\", \"$lastname\", \"$gender\", \"$nid\", \"$email\", \"$tel\", \"$bd\", \"$job\", \"$together\")") or die(mysqli_error($conn));

                                                                if($queryClient){
                                                                    $clientId = mysqli_insert_id($conn); 
                                                                    $account = generateAccountNumber($clientId);
                                                                    $balance = 0;

                                                                    $queryClientAccount = mysqli_query($conn, "INSERT INTO accounts(client, account, balance) VALUES(\"$clientId\", \"$account\", \"$balance\")") or die(mysqli_error($conn));
                                                                    if($queryClientAccount){

                                                                        // Sending account information

                                                                        // THIS IS FOR MESSAGE API
                                                                        $phonenumber=$tel;
                                                                        $delivery= "Hello ".$firstname." ".$lastname.", Murakoze kuba umunyamuryango w'Umurenge SACCO. Nomero ya Konti yanyu ni :'".$account."'. Murasambwa kubika neza izi credentials. Username yanyu ni :'".$username."' , mugihe Ijambobanga ari :'".$password."'";

                                                                        $number = array();
                                                                        array_push($number, $mtn);

                                                                        $imploded_phone = implode(",", $number);

                                                                        $data = array(
                                                                            "sender"=>'+250782377042',
                                                                            "recipients"=> $phonenumber,
                                                                            "message"=>$delivery,
                                                                            );
                                                                        $url = "https://www.intouchsms.co.rw/api/sendsms/.json";
                                                                        $data = http_build_query($data);
                                                                        $username="cyprien";
                                                                        $password="cnine8976?";
                                                                        $ch = curl_init();
                                                                        curl_setopt($ch,CURLOPT_URL,$url);
                                                                        curl_setopt($ch,CURLOPT_USERPWD,$username.":".$password);
                                                                        curl_setopt($ch,CURLOPT_POST,true);
                                                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                                        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
                                                                        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
                                                                        $result = curl_exec($ch);
                                                                        $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
                                                                        curl_close($ch);

                                                                        if ($httpcode = 200) {
                                                                            $notify="Thank You For Adding New Client In Our System!</br> Click on Account Tab To View Registered Clients. And This Message Sent To Client:\"<b>".$delivery."</b>\"";

                                                                            header("location:notifications.php?notification=$notify");
                                                                        }
                                                                        else{
                                                                            header("location: ".include_file($accounts_pages, $level));
                                                                        }       
                                                                    }
                                                                }
                                                                else{
                                                                    $notify="Thank You For Trying To Add New Client In Our System!</br> Click on Account Tab To Re-Add Him/Her, Because No New Records saved.";
                                                                
                                                                    header("location: notifications.php?notification=$notify ");
                                                                }
                                                            }
                                                        }
                                                        else{
                                                            $addressInsertQ = mysqli_query($conn, "INSERT INTO addresses(province, district, sector,cell,village) VALUES(\"$province\", \"$district\", \"$sector\",\"$cell\",\"$village\")") or die(mysqli_error($conn));

                                                            if ($addressInsertQ) {
                                                                
                                                                /// employee address searching
                                                                $addressQuery1=mysqli_query($conn,"SELECT id FROM addresses WHERE province=\"$province\" AND district=\"$district\" AND sector=\"$sector\" AND cell=\"$cell\" AND village=\"$village\"") or die(mysqli_error($conn));
                                    
                                                                if(mysqli_num_rows($addressQuery1) > 0){
                                                                    while($addressData = mysqli_fetch_assoc($addressQuery1)){
                                                                        $address = $addressData['id'];

                                                                        $queryClient = mysqli_query($conn, "INSERT INTO clients(user, branch, address, firstname, middlename, lastname, gender, nid, email, tel, bd, job_title,together) VALUES(\"$userId\", \"$branch\", \"$address\", \"$firstname\", \"$middlename\", \"$lastname\", \"$gender\", \"$nid\", \"$email\", \"$tel\", \"$bd\", \"$job\", \"$together\")") or die(mysqli_error($conn));

                                                                        if($queryClient){
                                                                            $clientId = mysqli_insert_id($conn); 
                                                                            $account = generateAccountNumber($clientId);
                                                                            $balance = 0;

                                                                            $queryClientAccount = mysqli_query($conn, "INSERT INTO accounts(client, account, balance) VALUES(\"$clientId\", \"$account\", \"$balance\")") or die(mysqli_error($conn));
                                                                            if($queryClientAccount){

                                                                                // Sending account information

                                                                                // THIS IS FOR MESSAGE API
                                                                                $phonenumber=$tel;
                                                                                $delivery= "Hello ".$firstname." ".$lastname.", Murakoze kuba umunyamuryango w'Umurenge SACCO. Nomero ya Konti yanyu ni :'".$account."'. Murasambwa kubika neza izi credentials. Username yanyu ni :'".$username."' , mugihe Ijambobanga ari :'".$password."'";

                                                                                $number = array();
                                                                                array_push($number, $mtn);

                                                                                $imploded_phone = implode(",", $number);

                                                                                $data = array(
                                                                                    "sender"=>'+250782377042',
                                                                                    "recipients"=> $phonenumber,
                                                                                    "message"=>$delivery,
                                                                                );
                                                                                $url = "https://www.intouchsms.co.rw/api/sendsms/.json";
                                                                                $data = http_build_query($data);
                                                                                $username="cyprien";
                                                                                $password="cnine8976?";
                                                                                $ch = curl_init();
                                                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                                                curl_setopt($ch,CURLOPT_USERPWD,$username.":".$password);
                                                                                curl_setopt($ch,CURLOPT_POST,true);
                                                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                                                curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
                                                                                curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
                                                                                $result = curl_exec($ch);
                                                                                $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
                                                                                curl_close($ch);

                                                                                if ($httpcode = 200) {
                                                                                    
                                                                                    $notify="Account Creadted Successful! This Message Sent To The Client :\"<b>".$delivery."</b>\"";

                                                                                    header("location:notifications.php?notification=$notify");
                                                                                }
                                                                                else{
                                                                                    $notify="Thank You For Trying To Add New Client In Our System!</br> Click on Account Tab To Re-Add Him/Her, Because No New Records saved.";
                                                                
                                                                                    header("location: notifications.php?notification=$notify");
                                                                                }                    

                                                                                // END OF API  
                                                                            }
                                                                        }
                                                                        else{
                                                                           $notify="Thank You For Trying To Add New Client In Our System!</br> Click On Account Tab To Re-Add Him/Her, Because No New Records saved.";
                                                                
                                                                            header("location: notifications.php?notification=$notify"); 
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }				
                                                }
                                            }
                                        ?>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        	</div>
       	</div>
	</section>

	<footer></footer>

	<!-- JavaScript -->
    <?php  include(include_file($linkscript, $level)); ?>
</body>
    <script>
    $(document).ready(function(){   
        $('#province').change(function() {
            var provinceID = $(this).val();
            if (provinceID) {
                $.ajax({
                    method:'POST',
                    url:'http://localhost/sacco2/pages/address.php',
                    data:{province_id: provinceID},
                    dataType:'text',
                    success:function(data) {
                        $('#district').html(data);
                        $('#sector').html('<option value="">~Select district first</option>');
                        $('#cell').html('<option value="">~Select sector first</option>');
                        $('#village').html('<option value="">~Select cell first</option>');
                        
                    }
                });
            }
            else{
                $('#district').html('<option value="">~Select province first</option>');
                $('#sector').html('<option value="">~Select district first</option>');
                $('#cell').html('<option value="">~Select sector first</option>');
                $('#village').html('<option value="">~Select cell first</option>');
                
            }
        });

        $('#district').change(function() {
            var districtID = $(this).val();
            if (districtID) {
                $.ajax({
                    type:'POST',
                    url:'http://localhost/sacco2/pages/address.php',
                    data:{district_id:districtID},
                    dataType:'text',
                    success:function(html) {
                        $('#sector').html(html);
                        $('#cell').html('<option value="">~Select sector first</option>');
                        $('#village').html('<option value="">~Select cell first</option>');
                        
                    }
                });
            }
            else{
                $('#sector').html('<option value="">~Select district first</option>');
                $('#cell').html('<option value="">~Select sector first</option>');
                $('#village').html('<option value="">~Select cell first</option>');
                
            }
        });

        $('#sector').change(function() {
            var sectorID = $(this).val();
            if (sectorID) {
                $.ajax({
                    type:'POST',
                    url:'http://localhost/sacco2/pages/address.php',
                    data:{sector_id:sectorID},
                    dataType:'text',
                    success:function(data) {
                        $('#cell').html(data);
                        $('#village').html('<option value="">~Select cell first</option>');
                    }
                });
            }
            else{
                $('#cell').html('<option value="">~Select sector first</option>');
                $('#village').html('<option value="">~Select cell first</option>');
                
            }
        });

        $('#cell').change(function() {
            var cellID = $(this).val();
            if (cellID) {
                $.ajax({
                    type:'POST',
                    url:'http://localhost/sacco2/pages/address.php',
                    data:{cell_id:cellID},
                    dataType:'text',
                    success:function(html) {
                        $('#village').html(html);
                    }
                });
            }
            else{
                $('#village').html('<option value="">~Select cell first</option>');
            }
        });
        $('#branch_ditrict').change(function() {
            var branch_dist_Id = $(this).val();
            if (branch_dist_Id) {
                $.ajax({
                    type:'POST',
                    url:'http://localhost/sacco2/pages/address.php',
                    data:{branch_distId:branch_dist_Id},
                    dataType:'text',
                    success:function(html) {
                        $('#branch_sector').html(html);
                    }
                });
            }
            else{
                $('#branch_ditrict').html('<option value="">~Select branch\'s district first</option>');
            }
        });
        $('#branch_sector').change(function() {
            var branch_sect_Id = $(this).val();
            if (branch_sect_Id) {
                $.ajax({
                    type:'POST',
                    url:'http://localhost/sacco2/pages/address.php',
                    data:{branch_sectId:branch_sect_Id},
                    dataType:'text',
                    success:function(html) {
                        $('#branch').html(html);
                    }
                });
            }
            else{
                $('#branch').html('<option value="">~Select branch\'s sector first</option>');
            }
        });
    });     
</script>
</html>
<?php
	mysqli_close($conn); // Closing connection to database
	ob_end_flush();
?>