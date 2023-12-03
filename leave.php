<?php
ob_start();

require('top.inc.php');
include "./file.php";

function getTotalLeaveCountOfYear($sick_leave_type_id, $user_id){
    global $con;
    $count = 0;
    $sql = "SELECT sum(leave_to-leave_from+1) as count FROM `leave` WHERE leave_id = $sick_leave_type_id AND employee_id = $user_id and leave_status=2";
    $res = mysqli_query($con, $sql);
    while($row = mysqli_fetch_assoc($res)) {
        if (isset($row['count']))
            $count = $row['count'];
    }
    return $count;
}

if(isset($_GET['type']) && $_GET['type']=='delete' && isset($_GET['id'])){
    $id=mysqli_real_escape_string($con,$_GET['id']);
    mysqli_query($con,"delete from `leave` where id='$id'");
}
if(isset($_GET['type']) && $_GET['type']=='update' && isset($_GET['id'])){
    $id=mysqli_real_escape_string($con,$_GET['id']);
    $status=mysqli_real_escape_string($con,$_GET['status']);
    mysqli_query($con,"update `leave` set leave_status='$status' where id='$id'");
}
if($_SESSION['ROLE']==1){ 
    $sql="select sum(leave_to-leave_from+1) as leave_days, `leave`.*, employee.name,employee.id as eid from `leave`,employee where `leave`.employee_id=employee.id  group by leave_from, leave_to order by `leave`.id desc";
}else{
    $eid=$_SESSION['USER_ID'];
    $sql="select sum(leave_to-leave_from+1) as leave_days,`leave`.*, employee.name ,employee.id as eid from `leave`,employee where `leave`.employee_id='$eid' and `leave`.employee_id=employee.id  group by leave_from, leave_to order by `leave`.id desc";
}
$res=mysqli_query($con,$sql);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Leave Page</title>
    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
             border: 5px solid #e0e0e0;
        }

        .table th,
        .table td {
         padding: 8px;
            text-align: left;
            border-bottom: 1px solid black;
            border-right: 1px solid black;
        }

        .table th {
            background-color: #f5f5f5;
               font-weight: bold;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
             border-right: none;
        }

        .table-stats {
            overflow-x: auto;  
        }

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

    </style>
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>

    <div class="nav">
      <h1>Leave</h1>
      <!-- <button onclick="printData()">Print</button> -->
    </div>

    <div class="content pb-0">
      <div class="orders">
        <div class="row">
          <div class="col-xl-12">
            <div class="card">
              <div class="card-body">
                <?php if($_SESSION['ROLE']==2){ ?>
                  <h2 class="box_title_link">
                    <a href="add_leave.php" width="15%">Add Leave</a> | 
                    <a href="my leave.php">My Leave</a>
                  </h2>
                <?php } ?>
              </div>
              <h2 class="box-title">Leave History</h2>
              <div class="card-body--">
                <div class="table-stats order-table ov-h">
                  <table id="leaveTable" class="table">
                    <thead>
                      <tr>
                        <th width="5%">S.No</th>
                        <th width="5%">ID</th>
                        <th width="15%">Employee Name</th>
                        <th width="14%">From</th>
                        <th width="14%">To</th>
                        <th width="14%">Days</th>
                        <th width="15%">Leave Taken</th>
                        <th width="15%">Description</th>
                        <th width="18%">Leave Status</th>
                        <th width="10%"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      $i=1;
                      while($row=mysqli_fetch_assoc($res)){
                      ?>
                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $row['id']?></td>
                        <td><?php echo $row['name'].' ('.$row['eid'].')'?></td>
                        <td><?php echo $row['leave_from']?></td>
                        <td><?php echo $row['leave_to']?></td>
                        <td><?php echo $row["leave_days"]?></td>
                        <td><?php echo getTotalLeaveCountOfYear($row['leave_id'], $row['eid'])?></td>
                        <td><?php echo $row['leave_description']?></td>
                        <td>
                          <?php
                          if($row['leave_status']==1){
                            echo "Applied";
                          }if($row['leave_status']==2){
                            echo "Approved";
                          }if($row['leave_status']==3){
                            echo "Rejected";
                          }
                          ?>
                          <?php if($_SESSION['ROLE']==1){ ?>
                            <select class="form-control" onchange="update_leave_status('<?php echo $row['id']?>',this.options[this.selectedIndex].value)">
                              <option value="">Update Status</option>
                              <option value="2">Approved</option>
                              <option value="3">Rejected</option>
                            </select>
                          <?php } ?>
                        </td> 
                        <td>
                          <?php
                          if($row['leave_status']==1){ ?>
                            <a href="leave.php?id=<?php echo $row['id']?>&type=delete">Delete</a>
                          <?php } ?>
                        </td>
                      </tr>
                      <?php 
                      $i++;
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      function update_leave_status(id, select_value) {
        window.location.href = 'leave.php?id=' + id + '&type=update&status=' + select_value;
        alert("Status has been updated"); 
      }

      function printData() {
        var printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.open();
        printWindow.document.write('<html><head><title>Leave History</title></head><body>');
        printWindow.document.write('<h1>Leave History</h1>');
        printWindow.document.write(document.getElementById('leaveTable').innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
      }
    </script>
  </body>
</html>

<?php
ob_end_flush(); 

require('footer.inc.php');
?>
