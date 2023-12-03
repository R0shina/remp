
function openEditPopup(employeeId, employeeName, monthlySalary) {
  document.getElementById("editEmployeeId").value = employeeId;
  document.getElementById("editEmployeeName").value = employeeName;
  document.getElementById("editMonthlySalary").value = monthlySalary;

  document.getElementById("editModal").style.display = "block";
}

function closeEditPopup() {
  document.getElementById("editModal").style.display = "none";
}

function saveEdit() {
  // Get form data
  var formData = {
    editEmployeeId: document.getElementById("editEmployeeId").value,
    editEmployeeName: document.getElementById("editEmployeeName").value,
    editMonthlySalary: document.getElementById("editMonthlySalary").value,
  };

  // Send an AJAX request to update the record
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4) {
      if (xhr.status == 200) {
        // Parse the JSON response
        var updatedData = JSON.parse(xhr.responseText);
        // Call the updateTableValues function with the updated data
        updateTableValues(updatedData);
        // Close the edit modal
        closeEditPopup();
      } else {
        console.error("Error updating record. Status: " + xhr.status);
      }
    }
  };

  xhr.open("POST", "salary_details.php", true);
  xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
  xhr.send(JSON.stringify(formData));
}

function updateTableValues(updatedData) {
  var employeeId = updatedData.employee_id;
  var employeeName = updatedData.employee_name;
  var monthlySalary = updatedData.monthly_salary;
  var calculatedSalary = updatedData.calculated_salary; // Adjust this based on your actual data

  console.log("Updating table values for employee ID " + employeeId);
  console.log("New employee name: " + employeeName);
  console.log("New monthly salary: " + monthlySalary);
  console.log("New calculated salary: " + calculatedSalary);

  // Update the table cell values based on the edited values
  var employeeNameCell = document.getElementById("employeeName_" + employeeId);
  var monthlySalaryCell = document.getElementById(
    "monthlySalary_" + employeeId
  );
  var calculatedSalaryCell = document.getElementById(
    "calculatedSalary_" + employeeId
  ); // Assuming you have a cell for calculated salary

  if (employeeNameCell && monthlySalaryCell && calculatedSalaryCell) {
    employeeNameCell.innerText = employeeName;
    monthlySalaryCell.innerText = monthlySalary;
    calculatedSalaryCell.innerText = calculatedSalary;

    // Add more cells here if needed

    console.log("Table values updated successfully.");
  } else {
    console.error("Error updating table values. Cells not found.");
  }
}




