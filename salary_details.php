<?php
ob_start();
require('top.inc.php');
include "./file.php";

if (isset($_GET['deleteId'])) {
    $deleteEmployeeId = $_GET['deleteId'];


    $deleteSql = "DELETE FROM salary WHERE employee_id = $deleteEmployeeId";

    if (mysqli_query($con, $deleteSql)) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . mysqli_error($con);
    }

    exit();
}


$perPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;

$selectedYear = isset($_GET['year']) ? mysqli_real_escape_string($con, $_GET['year']) : date('Y');

$yearFilter = "AND YEAR(salary_month) = $selectedYear";

$selectedMonth = isset($_GET['month']) ? mysqli_real_escape_string($con, $_GET['month']) : date('m');
$monthFilter = "AND YEAR(salary_month) = $selectedYear AND MONTH(salary_month) = $selectedMonth";

$selectSql = "SELECT * FROM salary WHERE 1 $monthFilter LIMIT $offset, $perPage";
$result = mysqli_query($con, $selectSql);


$totalRows = mysqli_num_rows(mysqli_query($con, "SELECT * FROM salary WHERE 1 $monthFilter"));
$totalPages = ceil($totalRows / $perPage);


$selectSql = "SELECT * FROM salary WHERE 1 $monthFilter LIMIT $offset, $perPage";
$result = mysqli_query($con, $selectSql);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (!is_null($data)) {
    class TrieNode {
        public $children = array();
        public $isEndOfWord = false;
    }

    class Trie {
        private $root;

        public function __construct() {
            $this->root = new TrieNode();
        }

        public function insert($word) {
            $node = $this->root;
            for ($i = 0; $i < strlen($word); $i++) {
                $char = $word[$i];
                if (!isset($node->children[$char])) {
                    $node->children[$char] = new TrieNode();
                }
                $node = $node->children[$char];
            }
            $node->isEndOfWord = true;
        }

        public function search($prefix) {
            $node = $this->root;
            $result = array();
            for ($i = 0; $i < strlen($prefix); $i++) {
                $char = $prefix[$i];
                if (!isset($node->children[$char])) {
                    return $result;
                }
                $node = $node->children[$char];
            }
            $this->findAllWords($node, $prefix, $result);
            return $result;
        }

        private function findAllWords($node, $prefix, &$result) {
            if ($node->isEndOfWord) {
                $result[] = $prefix;
            }
            foreach ($node->children as $char => $childNode) {
                $this->findAllWords($childNode, $prefix . $char, $result);
            }
        }
    }

    $trie = new Trie();


    foreach ($data as $row) {
        $name = strtolower($row['employee_name']);
        $trie->insert($name);
    }

    if (isset($_GET['autocomplete'])) {
        $query = strtolower($_GET['autocomplete']);
        $results = $trie->search($query);
        displayAutocompleteResults($results);
        exit;
    }
}

$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';

for ($i = 0; $i < count($data) - 1; $i++) {
    for ($j = 0; $j < count($data) - $i - 1; $j++) {
      
        if (isset($data[$j][$sortColumn]) && isset($data[$j + 1][$sortColumn])) {
            $compareA = strtolower($data[$j][$sortColumn]);
            $compareB = strtolower($data[$j + 1][$sortColumn]);

            if (($sortOrder === 'ASC' && $compareA > $compareB) || ($sortOrder === 'DESC' && $compareA < $compareB)) {
               
                $temp = $data[$j];
                $data[$j] = $data[$j + 1];
                $data[$j + 1] = $temp;
            }
        }
    }
}



ob_end_flush();
require('footer.inc.php');

function displayAutocompleteResults($results) {
    foreach ($results as $result) {
        echo '<div class="autocomplete-result">' . $result . '</div>';
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Employee Salary Details</title>
    <!-- <link rel="stylesheet" type="text/css" href="style.css"> -->
    <script src="script.js"></script>


    <link rel="stylesheet" type="text/css" href="css/style.css">
      <link rel="stylesheet" type="text/css" href="css/salary.css">
            <!-- <link rel="stylesheet" type="text/css" href="css/table.css"> -->

</head>

<body>
    <div class="nav">
        <h1>Salary Details</h1>
    </div>
    <div class="content-container">
        <div class="salary-table">
            <h2>Employee Salary Details</h2>
            <div class="sub-menu">
                <div class="month-filter">
      <form action="salary_details.php?year=<?php echo $selectedYear; ?>" method="get">
            <div class="year-filter">
            <label for="year">Select Year:</label>
            <select name="year" id="year">
            <?php
            $currentYear = date('Y');
            for ($i = $currentYear; $i >= ($currentYear - 10); $i--) {
                $selected = ($selectedYear == $i) ? 'selected' : '';
                echo "<option value='$i' $selected>$i</option>";
            }
            ?>
            </select>
        </div>
        <label for="month">Select Month:</label>
        <select name="month" id="month">
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                $selected = ($selectedMonth == $i) ? 'selected' : '';
                                echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                            }
                            ?>
                        </select>
                        <button type="submit">Apply</button>
                    </form>
                </div>
<form action="salary_details.php" method="get">
    <input type="hidden" name="month" value="<?php echo $selectedMonth; ?>">
    <input type="hidden" name="autocomplete" value="<?php echo $_GET['autocomplete'] ?? ''; ?>">
                <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search..." oninput="handleAutocomplete(this.value)">
    <button class="btn-info" type="button" onclick="searchTable()">Search</button>
    
    <div id="autocompleteResults"></div>
        </div>
            </div>
            </form>
            <table>
                <thead>
                    <tr>
                         <th width="5%"  class="sort-icon asc" onclick="sortTable('employee_id')">ID</th>
                        <th width="12%" class="sort-icon asc" onclick="sortTable('employee_name')">Name</th>
                        <!-- <th>Employee ID</th>
                        <th>Employee Name</th> -->
                        <th>Monthly Salary</th>
                        <th>Daily Salary</th>
                        <th>Total Working Days</th>
                        <th>Total Leave Days</th>
                        <th>Effective Working Days</th>
                        <th>Calculated Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php foreach ($data as $row) { ?>                            <tr>
                                <td><?php echo $row['employee_id']; ?></td>
                                <td><?php echo $row['employee_name']; ?></td>
                                <td><?php echo $row['monthly_salary']; ?></td>
                                <td><?php echo $row['daily_salary']; ?></td>
                                <td><?php echo $row['total_working_days']; ?></td>
                                <td><?php echo $row['total_leave_days']; ?></td>
                                <td><?php echo $row['effective_working_days']; ?></td>
                                <td><?php echo $row['calculated_salary']; ?></td>
                                <td>
                                    <?php
                                    $currentMonth = date('m');
                                    if ($selectedMonth == $currentMonth) {
                                    ?>
                                        <!-- <button onclick="openEditPopup(<?php echo $row['employee_id']; ?>, '<?php echo $row['employee_name']; ?>', <?php echo $row['monthly_salary']; ?>)">Edit</button> -->
                                    <?php
                                    }
                                    ?>
                                    <button onclick="confirmAndDelete(<?php echo $row['employee_id']; ?>, <?php echo $selectedMonth; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="9">
                                <div class="no-data-message">No salary details found for the selected Month.</div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <div class="pagination">
                    <?php if ($currentPage > 1) { ?>
                        <a href="?page=<?php echo $currentPage - 1; ?>&month=<?php echo $selectedMonth; ?>">Previous</a>
                    <?php } ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                        <a href="?page=<?php echo $i; ?>&month=<?php echo $selectedMonth; ?>" <?php echo ($i == $currentPage) ? 'style="background-color: #333;"' : ''; ?>><?php echo $i; ?></a>
                    <?php } ?>
                    <?php if ($currentPage < $totalPages) { ?>
                        <a href="?page=<?php echo $currentPage + 1; ?>&month=<?php echo $selectedMonth; ?>">Next</a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>


    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveEdit'])) {
       
        $editEmployeeId = $_POST['editEmployeeId'];
        $editEmployeeName = mysqli_real_escape_string($con, $_POST['editEmployeeName']);
        $editMonthlySalary = mysqli_real_escape_string($con, $_POST['editMonthlySalary']);

        $updateSql = "UPDATE salary SET employee_name = '$editEmployeeName', monthly_salary = '$editMonthlySalary' WHERE employee_id = $editEmployeeId";
        mysqli_query($con, $updateSql);

        $selectUpdatedDataSql = "SELECT * FROM salary WHERE employee_id = $editEmployeeId";
        $updatedData = mysqli_query($con, $selectUpdatedDataSql);
        $updatedRow = mysqli_fetch_assoc($updatedData);

      
        header('Location: salary_details.php');
        exit(); 
    }
    ?>
<script>
    function confirmAndDelete(employeeId, selectedMonth) {
        if (confirm("Are you sure you want to delete this record?")) {
           
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                       
                        window.location.reload();
                    } else {
                        console.error("Error deleting record. Status: " + xhr.status);
                    }
                }
            };

            xhr.open("GET", "salary_details.php?deleteId=" + employeeId, true);
            xhr.send();
        }
    }

    document.getElementById("searchInput").addEventListener("input", function () {
        var query = this.value;
        handleAutocomplete(query);
        searchTable(); 
    });

  function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementsByTagName("table")[0];
    tr = table.getElementsByTagName("tr");

    var found = false;

    for (i = 1; i < tr.length; i++) { 
        td = tr[i].getElementsByTagName("td")[1]; 
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
                found = true;
            } else {
                tr[i].style.display = "none";
            }
        }
    }

  
    if (!found) {
        var noResultsRow = table.insertRow(1);
        var noResultsCell = noResultsRow.insertCell(0);
        noResultsCell.colSpan = tr[0].cells.length; 
        noResultsCell.innerHTML = "No results found";

       
        noResultsCell.style.textAlign = "center";
        noResultsCell.style.padding = "10px"; 
    } else {
       
        var noResultsRow = table.rows[1];
        if (noResultsRow && noResultsRow.cells[0].innerHTML === "No results found") {
            table.deleteRow(1);
        }
    }
}


    function handleAutocomplete(query) {
        const minLength = 2;
        var resultsContainer = document.getElementById("autocompleteResults");

        if (query.length >= minLength) {
         
            fetchAutocompleteResults(query);
        } else {
            clearAutocompleteResults();
        }
    }

    function fetchAutocompleteResults(query) {
     
        fetch("employee.php?autocomplete=" + query)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status + ' ' + response.statusText);
                }
                return response.text();
            })
            .then(data => displayAutocompleteResults(data))
            .catch(error => console.error('Error:', error));
    }

    function displayAutocompleteResults(resultsHTML) {
        var resultsContainer = document.getElementById("autocompleteResults");
        resultsContainer.innerHTML = resultsHTML;

      
        var resultElements = resultsContainer.querySelectorAll(".autocomplete-result");
        resultElements.forEach(function (resultElement) {
            resultElement.addEventListener("click", function () {
                var selectedValue = resultElement.innerText;
                document.getElementById("searchInput").value = selectedValue;
                clearAutocompleteResults();
                searchTable();
            });
        });
    }

    function clearAutocompleteResults() {
        var resultsContainer = document.getElementById("autocompleteResults");
        resultsContainer.innerHTML = "";
    }

    document.addEventListener('DOMContentLoaded', function () {
        var url = new URL(window.location.href);
        var sortParam = url.searchParams.get("sort");
        var orderParam = url.searchParams.get("order");

        var sortIcons = document.querySelectorAll('.sort-icon');
        sortIcons.forEach(icon => icon.classList.remove('asc', 'desc'));

        if (sortParam) {
            var sortIcon = document.querySelector(`.sort-icon[onclick="sortTable('${sortParam}')"]`);
            sortIcon.classList.add(orderParam.toLowerCase());

            var oppositeSortIcon = document.querySelector(`.sort-icon[onclick="sortTable('${sortParam}')"]:not(.${orderParam.toLowerCase()})`);
            if (oppositeSortIcon) {
                oppositeSortIcon.style.display = 'none';
            }
        }
    });

function sortTable(columnName) {
    var url = new URL(window.location.href);
    var sortParam = url.searchParams.get("sort");
    var orderParam = url.searchParams.get("order");
    var yearParam = url.searchParams.get("year");
    var monthParam = url.searchParams.get("month");

    // Determine the new order
    var newOrder = (sortParam === columnName && orderParam === 'ASC') ? 'DESC' : 'ASC';

    // Log the generated URL to the console for debugging
    console.log("Generated URL:", "salary_details.php?sort=" + columnName + "&order=" + newOrder + "&year=" + yearParam + "&month=" + monthParam);

    // Update the URL
    window.location.href = "salary_details.php?sort=" + columnName + "&order=" + newOrder + "&year=" + yearParam + "&month=" + monthParam;
}

</script>


</body>

</html>

<?php
ob_end_flush();
require('footer.inc.php');
?>
