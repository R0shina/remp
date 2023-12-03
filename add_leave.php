<?php
ob_start();
require('top.inc.php');
if(isset($_POST['submit'])){
	$date_range[] = explode("-",$_POST["daterange"]);
	// var_dump($_POST);

	$daterange = $date_range[0];
$leave_from = $daterange[0]."-".$daterange[1]."-".$daterange[2];
$leave_to = $daterange[3]."-".$daterange[4]."-".$daterange[5];


	// var_dump($_POST['leave_from']);
	
	//$leave_from=trim(str_replace("/", "-", $date_range[0][0]));
	//$leave_to=trim(str_replace("/", "-", $date_range[0][1]));

	// $leave_from=$date_range[0][0];
	// $leave_to=$date_range[0][1];
	$leave_id=mysqli_real_escape_string($con,$_POST['leave_id']);
	// $leave_from=mysqli_real_escape_string($con,$_POST['leave_from']);
	// $leave_to=mysqli_real_escape_string($con,$_POST['leave_to']);
	
	$employee_id=$_SESSION['USER_ID'];
	$leave_description=mysqli_real_escape_string($con,$_POST['leave_description']);
	$sql="insert into `leave`(leave_id,leave_from,leave_to,employee_id,leave_description,leave_status) values('$leave_id','$leave_from','$leave_to','$employee_id','$leave_description',1)";
	mysqli_query($con,$sql);
	header('location:leave.php');
	die();
}
?>


<html>
  <head>
    <title>Home Page</title>
  <link rel="stylesheet" type="text/css" href="style.css">
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
            margin-top: 30px;
            margin-left: 250px;
            padding: 10px;
            box-sizing: border-box;
         }


         btn-info {
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
  <body>

<div class="nav">
         <h1>Leave Form</h1>
      </div>
    <div class="content pb-0">
            <div class="animated fadeIn">
               <div class="row">
                  <div class="col-lg-12">
                     <div class="card">
                        <!-- <div class="card-header"><strong>Leave Form</strong></div> -->
                        <div class="card-body card-block">
                           <form method="post">
						   
								<div class="form-group">
									<label class=" form-control-label">Leave Type</label>
									<select name="leave_id" required class="form-control">
										<option value="">Select Leave</option>
										<?php
										$res=mysqli_query($con,"select * from leave_type order by leave_type desc");
										while($row=mysqli_fetch_assoc($res)){
											echo "<option value=".$row['id'].">".$row['leave_type']."</option>";
										}
										?>
									</select>
								</div>

								<div class="form-group">
									<label class=" form-control-label" width="100%">Leave Date</label><br>
									<input type="text" name="daterange" class="form-control" />
								</div>
			
								<div class="form-group">
									<label class=" form-control-label">Leave Description</label>
									<input type="text" name="leave_description"  pattern="[a-zA-Z'-'\s]*"   class="form-control" >
								</div>
								 <button  type="submit" name="submit" class="btn btn-lg btn-info btn-block">
							   <span id="payment-button-amount">Submit</span>
							   </button>

							  
							  </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>  


<script>
$(function() {
  $('input[name="daterange"]').daterangepicker({
	locale: {
            format: 'YYYY-MM-DD'
        },
    opens: 'left'
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
});
</script>
 </body>
</html>

<?php
ob_end_flush(); 

require('footer.inc.php');
?>
