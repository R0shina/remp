<?php
ob_start();

require('top.inc.php');
require('trie.php');

$trie = new TrieNamespace\Trie();
if ($_SESSION['ROLE'] != 1) {
    header('location:add_employee.php?id=' . $_SESSION['USER_ID']);
    die();
}

if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    mysqli_query($con, "DELETE FROM employee WHERE id='$id'");
}

// Fetch data from the database
$res = mysqli_query($con, "SELECT e.*, d.department as department_name FROM employee e
                           LEFT JOIN department d ON e.department_id = d.id
                           WHERE e.role IN (2, 3)");

if (!$res) {
    die("Error in SQL query: " . mysqli_error($con));
}

$data = array();

while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
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

// $trie = new Trie();

// Insert data into the Trie, converting names to lowercase
foreach ($data as $row) {
    $name = strtolower($row['name']);
    $trie->insert($name);
}

// Autocomplete logic
if (isset($_GET['autocomplete'])) {
    $query = strtolower($_GET['autocomplete']);
    $results = $trie->search($query);
    displayAutocompleteResults($results);
    exit;
}

// Sorting based on the specified column and order using Bubble Sort
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';

for ($i = 0; $i < count($data) - 1; $i++) {
    for ($j = 0; $j < count($data) - $i - 1; $j++) {
        $compareA = strtolower($data[$j][$sortColumn]);
        $compareB = strtolower($data[$j + 1][$sortColumn]);

        if (($sortOrder === 'ASC' && $compareA > $compareB) || ($sortOrder === 'DESC' && $compareA < $compareB)) {
            // Swap the elements
            $temp = $data[$j];
            $data[$j] = $data[$j + 1];
            $data[$j + 1] = $temp;
        }
    }
}



ob_end_flush();
require('footer.inc.php');

// Function to display autocomplete results as HTML
function displayAutocompleteResults($results) {
    foreach ($results as $result) {
        echo '<div class="autocomplete-result">' . $result . '</div>';
    }
}


$perPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;


$sql = "SELECT e.*, d.department as department_name FROM employee e
        LEFT JOIN department d ON e.department_id = d.id
        WHERE e.role IN (2, 3)";


$res = mysqli_query($con, $sql);

// Calculate total pages for pagination
$total_records_query = mysqli_query($con, "SELECT COUNT(*) AS total FROM `employee`");
$total_records = mysqli_fetch_assoc($total_records_query)['total'];
$totalPages = ceil($total_records / $perPage);


?>
<!DOCTYPE html>
<html>

<head>
    <title>Employee Page</title>

       <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" type="text/css" href="css/table.css">
</head>

<body>
    <div class="nav">
        <h1>Employee</h1>
    </div>
    <div class="content">
        <div class="card-body">
            <h2 class="emp"><a href="add_employee.php">Add Employee</a></h2>
          <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search..." oninput="handleAutocomplete(this.value)">
    <button class="btn-info" type="button" onclick="searchTable()">Search</button>
    <!-- Add the autocomplete results container -->
    <div id="autocompleteResults"></div>
        </div>

        </div>
        <div class="card-body--">
            <div class="table-stats order-table ov-h">
                <table>
                <thead>
                    <tr>  
                        <th width="5%">S.No</th>
                        <th width="5%"  class="sort-icon asc" onclick="sortTable('id')">ID</th>
                        <th width="12%" class="sort-icon asc" onclick="sortTable('name')">Name</th>
           
                            <th width="12%">Email</th>
                            <th width="5%">Address</th>
                            <th width="10%">Department</th>
                            <th width="5%">Mobile</th>
                            <th width="5%">Role</th>
                            <th width="10%">Action</th>
                    </tr>
                 </thead>
          

                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($data as $row) { ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo $row['id'] ?></td>
                                <td><?php echo $row['name'] ?></td>
                                <td><?php echo $row['email'] ?></td>
                                <td><?php echo $row['address'] ?></td>
                                <td><?php echo $row['department_name'] ?></td>
                                <td><?php echo $row['mobile'] ?></td>
                                <td>
                                    <?php
                                    // Display the position based on the role value
                                    switch ($row['role']) {
                                        case 1:
                                            echo "Main Admin";
                                            break;
                                        case 2:
                                            echo "Employee";
                                            break;
                                        case 3:
                                            echo "HR Manager";
                                            break;
                                        default:
                                            echo "Unknown";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="add_employee.php?id=<?php echo $row['id'] ?>">Edit</a> |
                                    <a href="employee.php?id=<?php echo $row['id'] ?>&type=delete">Delete</a>
                                </td>
                            </tr>
                        <?php
                            $i++;
                        } ?>
                    </tbody>
                </table>

                                 <div class="pagination">
    <?php if ($currentPage > 1) { ?>
        <a href="?page=<?php echo $currentPage - 1; ?>">Previous</a>
    <?php } ?>
    <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
        <a href="?page=<?php echo $i; ?>" <?php echo ($i == $currentPage) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
    <?php } ?>
    <?php if ($currentPage < $totalPages) { ?>
        <a href="?page=<?php echo $currentPage + 1; ?>">Next</a>
    <?php } ?>
</div>

    </div>

            <!-- </div></div> -->

        </div>
    </div>

<script>
function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementsByTagName("table")[0];
    tr = table.getElementsByTagName("tr");

    var found = false;

    for (i = 1; i < tr.length; i++) { // Start from index 1 to skip the header row
        td = tr[i].getElementsByTagName("td")[2]; // Assuming the third column is the 'Name' column
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

    // Display "No results found" message if no matches were found
    if (!found) {
        var noResultsRow = table.insertRow(1); // Insert a new row just below the header
        var noResultsCell = noResultsRow.insertCell(0);
        noResultsCell.colSpan = tr[0].cells.length; // Set colspan to cover all columns
        noResultsCell.innerHTML = "No results found";

        // Center the text in the cell using CSS
        noResultsCell.style.textAlign = "center";
        noResultsCell.style.padding = "10px"; // Add some padding for better visibility
    } else {
        // Remove the "No results found" row if it exists
        var noResultsRow = table.rows[1];
        if (noResultsRow && noResultsRow.cells[0].innerHTML === "No results found") {
            table.deleteRow(1);
        }
    }
}

    document.getElementById("searchInput").addEventListener("input", function () {
        var query = this.value;
        handleAutocomplete(query);
    });

    function handleAutocomplete(query) {
        const minLength = 2;
        var resultsContainer = document.getElementById("autocompleteResults");

        if (query.length >= minLength) {
            // Call your PHP script to get autocomplete results
            fetchAutocompleteResults(query);
        } else {
            clearAutocompleteResults();
        }
    }

    function fetchAutocompleteResults(query) {
        // Modify the URL to include the query parameter
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

        // Handle click on autocomplete result
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

        // Remove existing classes from all sort icons
        var sortIcons = document.querySelectorAll('.sort-icon');
        sortIcons.forEach(icon => icon.classList.remove('asc', 'desc'));

        // Find the sort icon for the initial sorting column and add the appropriate class
        if (sortParam) {
            var sortIcon = document.querySelector(`.sort-icon[onclick="sortTable('${sortParam}')"]`);
            sortIcon.classList.add(orderParam.toLowerCase());

            // Hide the opposite sorting button
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

        // Determine the new order
        var newOrder = (sortParam === columnName && orderParam === 'ASC') ? 'DESC' : 'ASC';

        // Update the URL
        window.location.href = "employee.php?sort=" + columnName + "&order=" + newOrder;
    }
</script>

</body>

</html>

<?php
ob_end_flush();
require('footer.inc.php');
?>
