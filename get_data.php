<?php
require_once 'db.php';

// Reading DataTables parameters
$draw = isset($_GET['draw']) ? $_GET['draw'] : 1;
$start = isset($_GET['start']) ? $_GET['start'] : 0;
$length = isset($_GET['length']) ? $_GET['length'] : 10;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// Column sorting
$orderColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 0;
$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'ASC';
$columns = array('column1', 'column2', 'column3'); // Replace with your actual column names
$orderBy = $columns[$orderColumn];

// Build the SQL query
$sql = "SELECT * FROM YourTable WHERE 1=1";
$countSql = "SELECT COUNT(*) as total FROM YourTable WHERE 1=1";

// Add search condition if search value exists
if (!empty($search)) {
    $searchCondition = " AND (column1 LIKE ? OR column2 LIKE ? OR column3 LIKE ?)";
    $sql .= $searchCondition;
    $countSql .= $searchCondition;
}

// Add ordering
$sql .= " ORDER BY $orderBy $orderDir OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

// Prepare and execute count query
$params = array();
if (!empty($search)) {
    $searchParam = "%$search%";
    $params = array($searchParam, $searchParam, $searchParam);
}
$countStmt = sqlsrv_query($conn, $countSql, $params);
$totalRecords = sqlsrv_fetch_array($countStmt)['total'];

// Execute main query
$queryParams = $params;
array_push($queryParams, $start, $length);
$stmt = sqlsrv_query($conn, $sql, $queryParams);

// Fetch results
$data = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $data[] = $row;
}

// Prepare response
$response = array(
    "draw" => intval($draw),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
);

header('Content-Type: application/json');
echo json_encode($response);
?>