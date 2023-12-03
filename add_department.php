 <?php
 ob_start();
require('top.inc.php');
if($_SESSION['ROLE']!=1){
	header('location:add_employee.php?id='.$_SESSION['USER_ID']);
	die();
}
$department='';
$id='';
if(isset($_GET['id'])){
	$id=mysqli_real_escape_string($con,$_GET['id']);
	$res=mysqli_query($con,"select * from department where id='$id'");
	$row=mysqli_fetch_assoc($res);
	$department=$row['department'];
}
if(isset($_POST['department'])){
	$department=mysqli_real_escape_string($con,$_POST['department']);
	if($id>0){
		$sql="update department set department='$department' where id='$id'";
	}else{
		$sql="insert into department(department) values('$department')";
	}
	mysqli_query($con,$sql);
	header('location:department.php');
	die();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <link rel="stylesheet" type="text/css" href="style.css">
    <title>Document</title>
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
            /* padding-top : 25px; */
           
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
        height: 20vh; 
    }

         .card {
   background-color: #f2f2f2;
   border-radius: 5px;
   box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
   padding: 20px;
        width: 100%; 
        max-width: 400px;
       
}
		

 label.form-control-label {
        font-size: 24px;
        color: #333; 
        font-weight: bold;
          
  
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
        /* Add any other styles you want to apply */
    }

    .btn-info:hover {
        background-color:#6e79aa; /* Change background color on hover */
        /* Add any other hover styles you want to apply */
    }

    /* Adjust the styles for full-width buttons */
    .btn-block {
        width: 100%;
    }
		</style>
</head>
<body>
   <div class="nav">
         <h1>Department</h1>
      </div>
    <div class="content ">
            <div class="animated fadeIn">
               <div class="row">
                  <div class="col-lg-12">
                     <div class="card">

                        <div class="card-body card-block">
                           <form method="post">
							   <div class="form-group">
								<label for="department" class=" form-control-label">Department Name</label>
								<input type="text" value="<?php echo $department?>" name="department" placeholder="Enter your department name" pattern="[a-zA-Z'-'\s]*"  class="form-control" required></div>
							   
							   <button  type="submit" class="btn btn-lg btn-info btn-block">
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
</html>

                  
<?php
ob_end_flush(); 

require('footer.inc.php');
?>