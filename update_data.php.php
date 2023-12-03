<?php
require('file.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveEdit'])) {
    $editEmployeeId = $_POST['editEmployeeId'];
    $editEmployeeName = mysqli_real_escape_string($con, $_POST['editEmployeeName']);
    $editMonthlySalary = mysqli_real_escape_string($con, $_POST['editMonthlySalary']);

    $updateSql = "UPDATE salary SET employee_name = '$editEmployeeName', monthly_salary = '$editMonthlySalary' WHERE employee_id = $editEmployeeId";
    mysqli_query($con, $updateSql);

    // Fetch the updated data from the database
    $selectUpdatedDataSql = "SELECT * FROM salary WHERE employee_id = $editEmployeeId";
    $updatedData = mysqli_query($con, $selectUpdatedDataSql);
    $updatedRow = mysqli_fetch_assoc($updatedData);

    // Echo the updated data as JSON
    echo json_encode($updatedRow);
    exit; // Terminate the script after echoing JSON data
}
?>
