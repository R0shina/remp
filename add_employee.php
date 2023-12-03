<?php
ob_start();
require('top.inc.php');
$name='';
$email='';
$mobile='';
$department_id='';
$address='';
$birthday='';
$id='';
if(isset($_GET['id'])){
	$id=mysqli_real_escape_string($con,$_GET['id']);
	if($_SESSION['ROLE']==2 && $_SESSION['USER_ID']!=$id){
		die('Access denied');
	}
	$res=mysqli_query($con,"select * from employee where id='$id'");
	$row=mysqli_fetch_assoc($res);
	$name=$row['name'];
	$email=$row['email'];
	$mobile=$row['mobile'];
	$department_id=$row['department_id'];
	$address=$row['address'];
	$birthday=$row['birthday'];
}
if(isset($_POST['submit'])){
	$name=mysqli_real_escape_string($con,$_POST['name']);
	$email=mysqli_real_escape_string($con,$_POST['email']);
	$mobile=mysqli_real_escape_string($con,$_POST['mobile']);
	$password=mysqli_real_escape_string($con,$_POST['password']);
	$department_id=mysqli_real_escape_string($con,$_POST['department_id']);
	$address=mysqli_real_escape_string($con,$_POST['address']);
	$birthday=mysqli_real_escape_string($con,$_POST['birthday']);
	if($id>0){
		$sql="update employee set name='$name',email='$email',mobile='$mobile',password='$password',department_id='$department_id',address='$address',birthday='$birthday' where id='$id'";
	}else{
		$sql="insert into employee(name,email,mobile,password,department_id,address,birthday,role) values('$name','$email','$mobile','$password','$department_id','$address','$birthday','2')";
	}
	mysqli_query($con,$sql);
	header('location:employee.php');
	die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <link rel="stylesheet" type="text/css" href="style.css">
	   <link rel="stylesheet" type="text/css" href="index.css">
    <title>Document</title>
</head>
<style>
	.nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            left:250px;
            background-color: white;
            padding: 20px;
             padding-top:20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
           
         }

   .nav h1 {
            margin: 0;
            padding: 0;
            color: black;
            font-size: 24px;
            font-weight: bold;
         }

            .content {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 6vh; 
    }

         .card {
   background-color: #f2f2f2;
   border-radius: 5px;
   box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
   padding: 20px;
        width: 100%; 
        max-width: 800px;
       
}
	
.form{
	display : flex;
	   gap: 10px; 
}


 .form .form-group {
        width: 100%; /* Set the width of the form groups to 100% */
    padding-top: 10px;
	}

    .form .form-control {
        width: 100%; 
    }

 label.form-control-label {
        font-size: 20px;
        color: #333; 
        font-weight: bold;
		padding-top : 12px;
          
  
    }

    .form-control {
        width: 100%; /* Make the input full-width */
          /* padding-bottom:20px; */
        padding: 10px; /* Adjust padding as needed */
        margin-top: 15px; /* Adjust margin as needed */
        border: 1px solid #ddd; /* Add border style */
        border-radius: 5px; /* Add border radius for rounded corners */
        box-sizing: border-box; /* Include padding and border in the element's total width and height */
        /* Add any other styles you want to apply */
    }

     .btn-info {
        background-color:#929ABF; /* Button background color */
        color: #fff; /* Button text color */
        font-size: 18px; /* Button font size */
        padding: 10px; /* Adjust padding as needed */
        border: 1px solid #6e79aa;/* Button border color */
        border-radius: 5px; /* Add border radius for rounded corners */
        cursor: pointer; /* Show pointer cursor on hover */
       width : 20%;
    }

    .btn-info:hover {
        background-color:#6e79aa; /* Change background color on hover */
        /* Add any other hover styles you want to apply */
    }

    /* Adjust the styles for full-width buttons */
    .btn-block {
        width: 100%;
    }

	 .error-message {
        color: red;
        font-size: 12px;
    }
	</style>
<body>
	<div class="nav">
         <h1>Employee</h1>
      </div>
      <div class="col-lg-12">
    			 <div class="card">
                	<div class="card-body card-block">
                        <form method="post">
						<div class ="form">
								<div class="form-group">
									<label class=" form-control-label">Name</label>
									<input type="text" value="<?php echo $name?>" name="name" placeholder="Enter Employee name" pattern="[a-zA-Z'-'\s]*"    class="form-control"  oninvalid="this.setCustomValidity('Please Enter in Alphabetical Order')" required>
								</div>
								<div class="form-group">
									<label class=" form-control-label">Email</label>
									<input type="email" value="<?php echo $email?>" name="email" placeholder="Enter Employee email"  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" class="form-control" oninvalid="this.setCustomValidity('Please Enter valid email')" required>
								</div>
						</div>

	
						<div class ="form">
							
								<div class="form-group">
									<label class=" form-control-label">Mobile</label>
									<input type="tel" value="<?php echo $mobile?>" name="mobile" placeholder="Enter employee mobile" pattern="(\+977)?[9][7-8]\d{8}" max=10 class="form-control" oninvalid="this.setCustomValidity('Please Enter 10 digit')">
								</div>
								<div class="form-group">
									<label class=" form-control-label">Password</label>
									<input type="password"  name="password" placeholder="Enter employee password" class="form-control" required>
								</div>

					</div>

					
								<div class="form-group">
									<label class=" form-control-label">Department</label>
									<select name="department_id" required class="form-control">
										<option value="">Select Department</option>
										<?php
										$res=mysqli_query($con,"select * from department order by department desc");
										while($row=mysqli_fetch_assoc($res)){
											if($department_id==$row['id']){
												echo "<option selected='selected' value=".$row['id'].">".$row['department']."</option>";
											}else{
												echo "<option value=".$row['id'].">".$row['department']."</option>";
											}
										}
										?>
									</select>
								</div>
								
								<div class = "form">
						<div class="form-group">
							 		<label class=" form-control-label">Address</label>
									<input type="text" value="<?php echo $address?>" name="address" placeholder="Enter employee address" class="form-control" required>
								</div>
								<div class="form-group">
									<label class="form-control-label">Birthday</label>
        <input type="date" value="<?php echo $birthday?>" name="birthday" id="birthday" placeholder="Enter employee birthday" max="2010-12-31" class="form-control" required oninput="validateAge()">
        <div id="age-error" class="error-message" style="display: none;">Employee must be above 18 years old</div>
   </div>
					</div>
							  
							   <button  type="submit" name="submit" class="btn btn-lg btn-info btn-block" >
							   <span id="payment-button-amount">Submit</span>
							   </button>
					
							  </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
</body>

<script>
    function validateAge() {
        var birthdayInput = document.getElementById('birthday');
        var selectedDate = new Date(birthdayInput.value);
        var currentDate = new Date();

        // Calculate age
        var age = currentDate.getFullYear() - selectedDate.getFullYear();

        // Check if the birthday has occurred this year
        if (currentDate.getMonth() < selectedDate.getMonth() || (currentDate.getMonth() === selectedDate.getMonth() && currentDate.getDate() < selectedDate.getDate())) {
            age--;
        }

        // Check if the age is less than 18
        if (age < 18) {
            document.getElementById('age-error').style.display = 'block';
            birthdayInput.setCustomValidity('Employee must be above 18 years old');
        } else {
            document.getElementById('age-error').style.display = 'none';
            birthdayInput.setCustomValidity('');
        }
    }
</script>
</html>



<?php
ob_end_flush(); 
require('footer.inc.php');
?>