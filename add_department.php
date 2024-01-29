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
<!-- <link rel="stylesheet" type="text/css" href="css/table.css"> -->
    <title>Document</title>
    <style>
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            left: 250px;
            background-color: white;
            padding: 20px;
            padding-top: 20px;
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

        
        .animated fadeIn {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 20vh;
          /* margin-top: 15px; */
        }

        .card {
            background-color: #f2f2f2;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 400px;
            margin-top: 120px;
              /* padding-bottom : 20px; */
        }

        label.form-control-label {
            font-size: 24px;
            color: #333;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .btn-info {
            background-color: #929ABF;
            color: #fff;
            font-size: 18px;
            padding: 10px;
            border: 1px solid #6e79aa;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-info:hover {
            background-color: #6e79aa;
        }

        .btn-block {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="nav">
        <h1>Department</h1>
    </div>
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body card-block">
                            <form method="post">
                                <div class="form-group">
                                    <label for="department" class="form-control-label">Department Name</label>
                                    <input type="text" value="<?php echo $department?>" name="department" placeholder="Enter your department name" pattern="[a-zA-Z'-'\s]*" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-lg btn-info btn-block">
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
