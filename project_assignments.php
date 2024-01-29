<?php
ob_start();
require('top.inc.php');

if ($_SESSION['ROLE'] != 1) {
    header('location: add_employee.php?id=' . $_SESSION['USER_ID']);
    die();
}

$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'project_name';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$perPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;
$total_records_query = mysqli_query($con, "SELECT COUNT(*) AS total FROM project_assignments");
$total_records = mysqli_fetch_assoc($total_records_query)['total'];

$totalPages = ceil($total_records / $perPage);

// Additional error checking
$allowedSortColumns = ['project_name', 'employee_name', 'start_date', 'end_date'];
if (!in_array($sortColumn, $allowedSortColumns)) {
    die("Invalid sort column specified");
}

// Dynamically determine the ORDER BY column based on the selected sort column
$orderByColumn = ($sortColumn === 'project_name') ? 'p.name' :
                  (($sortColumn === 'employee_name') ? 'e.name' :
                  (($sortColumn === 'start_date') ? 'pa.start_date' :
                  (($sortColumn === 'end_date') ? 'pa.end_date' : '')));


// Check if the ORDER BY column is valid
if (!$orderByColumn) {
    die("Invalid sort column specified");
}

$project_assignments_res = mysqli_query($con, "SELECT pa.*, p.name AS project_name, p.description AS project_description, 
                                               p.start_date AS project_start_date, p.end_date AS project_end_date, p.status AS project_status,
                                               e.name AS employee_name
                                               FROM project_assignments pa
                                               JOIN projects p ON pa.project_id = p.id
                                               JOIN employee e ON pa.employee_id = e.id
                                               ORDER BY $orderByColumn $sortOrder
                                               LIMIT $perPage OFFSET $offset");

if (!$project_assignments_res) {
    die("Query failed: " . mysqli_error($con));
}

$project_assignments = [];
while ($assignment = mysqli_fetch_assoc($project_assignments_res)) {
    $project_assignments[] = $assignment;
}

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
foreach ($project_assignments as $row) {
    $name = strtolower($row['employee_name']);
    $trie->insert($name);
}

if (isset($_GET['autocomplete'])) {
    $query = strtolower($_GET['autocomplete']);
    $results = $trie->search($query);
    displayAutocompleteResults($results);
    exit;
}

$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'employee_name';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';

for ($i = 0; $i < count($project_assignments) - 1; $i++) {
    for ($j = 0; $j < count($project_assignments) - $i - 1; $j++) {
        if (isset($project_assignments[$j][$sortColumn]) && isset($project_assignments[$j + 1][$sortColumn])) {
            $compareA = strtolower($project_assignments[$j][$sortColumn]);
            $compareB = strtolower($project_assignments[$j + 1][$sortColumn]);

            if (($sortOrder === 'ASC' && $compareA > $compareB) || ($sortOrder === 'DESC' && $compareA < $compareB)) {
                $temp = $project_assignments[$j];
                $project_assignments[$j] = $project_assignments[$j + 1];
                $project_assignments[$j + 1] = $temp;
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Assignments Page</title>
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" type="text/css" href="css/table.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha384-ezBsV+3ie8izX1x0FqUzD95GR2HRy7YvPX6a1qDw1NHh7ZNpjW4d1ZWh+OupwQ" crossorigin="anonymous">
</head>

<body>
    <div class="nav">
        <h1>Task Details</h1>
        <!-- <button onclick="printData()">Print</button> -->
    </div>

    <div class="content">
        <form action="project_assignments.php" method="get" class= "header">

            <input type="hidden" name="autocomplete" value="<?php echo $_GET['autocomplete'] ?? ''; ?>">
            <h2>Employee Task Details</h2>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search..." oninput="handleAutocomplete(this.value)">
                <button class="btn-info" type="button" onclick="searchTable()">Search</button>
            
                <div id="autocompleteResults"></div>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th width="" class="sort-icon asc" onclick="sortTable('project_name')">Project Name</th>
                    <th width="15%" class="sort-icon asc" onclick="sortTable('employee_name')">Employee Name</th>
                    <!-- Add other table header columns as needed -->
                    <th>Description</th>
                    <th class="sort-icon asc" ">Start Date</th>
                    <th class="sort-icon asc">End Date</th>
                    <th>Status</th>
                    <!-- <th>Effective Working Days</th>
                    <th>Calculated Salary</th>
                    <th>Actions</th> -->
                </tr>
            </thead>

         <tbody>
    <?php foreach ($project_assignments as $assignment) : ?>
        <tr>
            <td><?= $assignment['project_name']; ?></td>
            <td><?= $assignment['employee_name']; ?></td>
            <td><?= $assignment['project_description']; ?></td>
            <td><?= $assignment['project_start_date']; ?></td>
            <td><?= $assignment['project_end_date']; ?></td>
            <td>
                <?php
                $status = $assignment['project_status'];
                echo ($status !== null && $status !== '') ? $status : 'Not Updated';
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
        </table>
       <?php if (count($project_assignments) > 0) { ?>

            <div class="pagination">
                <?php if ($currentPage > 1) { ?>
                    <a href="?page=<?php echo $currentPage - 1; ?>&sort=<?php echo $sortKey; ?>">Previous</a>
                <?php } ?>
                <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                    <a href="?page=<?php echo $i; ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>" <?php echo ($i == $currentPage) ? 'style="background-color: #333;"' : ''; ?>><?php echo $i; ?></a>
                <?php } ?>
                <?php if ($currentPage < $totalPages) { ?>
                    <a href="?page=<?php echo $currentPage + 1; ?>&sort=<?php echo $sortKey; ?>">Next</a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <script>

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


    function fetchAutocompleteResults(query) {
    fetch('project_assignments.php?autocomplete=' + query)
        .then(response => response.text())
        .then(data => displayAutocompleteResults(data))
        .catch(error => console.error('Error fetching autocomplete results:', error));
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

      
        var newOrder = (sortParam === columnName && orderParam === 'ASC') ? 'DESC' : 'ASC';

   
        window.location.href = "project_assignments.php?sort=" + columnName + "&order=" + newOrder;
    }

    
</script>


</body>


</html>
