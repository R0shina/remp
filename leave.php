<?php
ob_start();

require('top.inc.php');
include "./file.php";

function getTotalLeaveCountOfYear($leave_type_id, $user_id)
{
    global $con;
    $count = 0;
    $sql = "SELECT sum(leave_to-leave_from+1) as count FROM `leave` WHERE leave_id = $leave_type_id AND employee_id = $user_id and leave_status=2";
    $res = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        if (isset($row['count']))
            $count = $row['count'];
    }
    return $count;
}

if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    mysqli_query($con, "delete from `leave` where id='$id'");
}
if (isset($_GET['type']) && $_GET['type'] == 'update' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    $status = mysqli_real_escape_string($con, $_GET['status']);
    mysqli_query($con, "update `leave` set leave_status='$status' where id='$id'");
}
if ($_SESSION['ROLE'] == 1) {
    $sql = "SELECT sum(leave_to-leave_from+1) as leave_days, `leave`.*, employee.name, employee.id as eid, leave_type.leave_type FROM `leave`
    INNER JOIN employee ON `leave`.employee_id=employee.id 
    INNER JOIN leave_type ON `leave`.leave_id=leave_type.id 
    GROUP BY leave_from, leave_to ORDER BY `leave`.id DESC";
} else {
    $eid = $_SESSION['USER_ID'];
    $sql = "SELECT sum(leave_to-leave_from+1) as leave_days,`leave`.*, employee.name ,employee.id as eid, leave_type.leave_type FROM `leave`
    INNER JOIN employee ON `leave`.employee_id=employee.id 
    INNER JOIN leave_type ON `leave`.leave_id=leave_type.id 
    WHERE `leave`.employee_id='$eid' GROUP BY leave_from, leave_to ORDER BY `leave`.id DESC";
}

$res = mysqli_query($con, $sql);

class TrieNode
{
    public $children = array();
    public $isEndOfWord = false;
}

class Trie
{
    private $root;

    public function __construct()
    {
        $this->root = new TrieNode();
    }

    public function insert($word)
    {
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

    public function search($prefix)
    {
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

    private function findAllWords($node, $prefix, &$result)
    {
        if ($node->isEndOfWord) {
            $result[] = $prefix;
        }
        foreach ($node->children as $char => $childNode) {
            $this->findAllWords($childNode, $prefix . $char, $result);
        }
    }
}

$trie = new Trie();


$data = array();
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

foreach ($data as $row) {
    $name = strtolower($row['name']);
    $trie->insert($name);
}

if (isset($_GET['autocomplete'])) {
    $query = strtolower($_GET['autocomplete']);
    $results = $trie->search($query);
    displayAutocompleteResults($results);
    exit;
}

$perPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;

$sql .= " LIMIT $offset, $perPage";

$res = mysqli_query($con, $sql);

$total_records_query = mysqli_query($con, "SELECT COUNT(*) AS total FROM `leave`");
$total_records = mysqli_fetch_assoc($total_records_query)['total'];
$totalPages = ceil($total_records / $perPage);

$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'leave_from';
$sortOrder = isset($_GET['order']) && strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC';

$sql .= " ORDER BY $sortColumn $sortOrder";

ob_end_flush();
require('footer.inc.php');

function displayAutocompleteResults($results)
{
    foreach ($results as $result) {
        echo '<div class="autocomplete-result">' . $result . '</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leave Page</title>
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" type="text/css" href="css/table.css">
</head>
<body>

<div class="nav">
        <h1>Leave</h1>
    </div>
<div class="content pb-0">
    <div class="card-body">
        <?php if ($_SESSION['ROLE'] == 2) { ?>
            <h2 class="box_title_link">
                <a href="add_leave.php" width="15%">Add Leave</a> |
                <a href="my leave.php">My Leave</a>
            </h2>
        <?php } ?>
    </div>

    <div class="card-body">
        <h2 class="">Leave History</h2>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search..." oninput="handleAutocomplete(this.value)">
            <button class="btn-info" type="button" onclick="searchTable()">Search</button>
            <div id="autocompleteResults"></div>
        </div>
    </div>

    <table id="leaveTable" class="table">
        <thead>
        <tr>
            <th width="5%">S.No</th>
            <th width="5%">ID</th>
            <th width="15%">Employee Name</th>
            <th width="14%">From</th>
            <!-- <th width="14%" class="sort-icon asc"><a href="javascript:void(0);" onclick="sortTable('leave_from')">From</a></th> -->
            <th width="14%">To</th>
            <th width="10%">Leave Type</th>
            <th width="15%">Leave Taken</th>
            <th width="15%">Description</th>
            <th width="18%">Leave Status</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        while ($row = mysqli_fetch_assoc($res)) {
            ?>
            <tr>
                <td><?php echo $i ?></td>
                <td><?php echo $row['id'] ?></td>
                <td><?php echo $row['name'] . ' (' . $row['eid'] . ')' ?></td>
                <td><?php echo $row['leave_from'] ?></td>
                <td><?php echo $row['leave_to'] ?></td>
                <td><?php echo $row['leave_type'] ?></td>
                <td>
                    <?php
                    $leaveFrom = strtotime($row['leave_from']);
                    $leaveTo = strtotime($row['leave_to']);

                    if ($leaveFrom !== false && $leaveTo !== false) {
                        echo ($leaveTo - $leaveFrom) / (60 * 60 * 24) + 1;
                    } else {
                        echo "Invalid date range";
                    }
                    ?>
                </td>
                <td><?php echo $row['leave_description'] ?></td>
                <td>
                    <?php
                    if ($row['leave_status'] == 1) {
                        echo "Applied";
                    } if ($row['leave_status'] == 2) {
                        echo "Approved";
                    } if ($row['leave_status'] == 3) {
                        echo "Rejected";
                    }
                    ?>
                    <?php if ($_SESSION['ROLE'] == 1) { ?>
                        <select class="form-control" <?php echo ($row['leave_status'] != 1) ? 'disabled' : ''; ?>
                                onchange="update_leave_status('<?php echo $row['id'] ?>',this.options[this.selectedIndex].value)">
                            <option value=""
                                    <?php echo ($row['leave_status'] != 1) ? 'disabled selected' : ''; ?>>Update Status
                            </option>
                            <option value="2" <?php echo ($row['leave_status'] != 1) ? 'disabled' : ''; ?>>Approved
                            </option>
                            <option value="3" <?php echo ($row['leave_status'] != 1) ? 'disabled' : ''; ?>>Rejected
                            </option>
                        </select>
                    <?php } ?>
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

      function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementsByTagName("table")[0];
    tr = table.getElementsByTagName("tr");

    var found = false;

    for (i = 1; i < tr.length; i++) { 
        td = tr[i].getElementsByTagName("td")[2]; 
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

     
        noResultsCell.style.textAlign = "center";
        noResultsCell.style.padding = "10px";
    } else {
        
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
           
            fetchAutocompleteResults(query);
        } else {
            clearAutocompleteResults();
        }
    }

    function fetchAutocompleteResults(query) {
   
        fetch("leave.php?autocomplete=" + query)
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
    var currentUrl = new URL(window.location.href);
    var sortParam = currentUrl.searchParams.get("sort");
    var orderParam = currentUrl.searchParams.get("order");

    var newOrder = (sortParam === columnName && orderParam === 'ASC') ? 'DESC' : 'ASC';

    currentUrl.searchParams.set("sort", columnName);
    currentUrl.searchParams.set("order", newOrder);
    window.location.href = currentUrl.toString();
}

document.addEventListener('DOMContentLoaded', function () {
    var sortIcons = document.querySelectorAll('.sort-icon');
    
    if (sortIcons.length > 0) {
        var url = new URL(window.location.href);
        var sortParam = url.searchParams.get("sort");
        var orderParam = url.searchParams.get("order");

        sortIcons.forEach(icon => icon.classList.remove('asc', 'desc'));

        if (sortParam) {
            var sortIcon = document.querySelector(`.sort-icon[onclick="sortTable('${sortParam}')"]`);
            if (sortIcon) {
                sortIcon.classList.add(orderParam.toLowerCase());

                var oppositeSortIcon = document.querySelector(`.sort-icon[onclick="sortTable('${sortParam}')"]:not(.${orderParam.toLowerCase()})`);
                if (oppositeSortIcon) {
                    oppositeSortIcon.style.display = 'none';
                }
            }
        }
    }
});



    </script>
  </body>
</html>

<?php
ob_end_flush(); 

require('footer.inc.php');
?>
