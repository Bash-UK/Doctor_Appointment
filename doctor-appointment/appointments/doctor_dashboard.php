<?php
session_start();
include('../includes/config.php');
include('../includes/header.php');

// Doctor-only access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    die("❌ Access denied. Doctors only.");
}

// Get filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : [];

// Ensure $status_filter is always an array
if (!is_array($status_filter)) {
    $status_filter = [$status_filter];
}

// Pagination settings
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Build WHERE conditions
$conditions = ["a.doctor_id = d.id AND d.user_id = " . intval($_SESSION['user_id'])];

if ($search) {
    $search_esc = $conn->real_escape_string($search);
    $conditions[] = "(u.name LIKE '%$search_esc%' OR a.appointment_date LIKE '%$search_esc%')";
}

if (!empty($status_filter)) {
    $escaped_statuses = array_map(function($s) use ($conn) {
        return "'" . $conn->real_escape_string($s) . "'";
    }, $status_filter);
    $conditions[] = "a.status IN (" . implode(',', $escaped_statuses) . ")";
}

$where_sql = '';
if (count($conditions) > 0) {
    $where_sql = " WHERE " . implode(" AND ", $conditions);
}

// Count total rows for pagination
$count_query = "
SELECT COUNT(*) as total 
FROM appointments a
JOIN users u ON a.user_id = u.id
JOIN doctors d ON a.doctor_id = d.id
WHERE d.user_id = " . intval($_SESSION['user_id']) . "
";

$total_result = $conn->query($count_query);
$total_row = $total_result->fetch_assoc();
$total_pages = ceil($total_row['total'] / $limit);

// Fetch appointments
$query = "
SELECT a.id, u.name AS patient_name, d.name AS doctor_name,
       a.appointment_date, a.appointment_time, a.status
FROM appointments a
JOIN users u ON a.user_id = u.id
JOIN doctors d ON a.doctor_id = d.id
WHERE d.user_id = " . intval($_SESSION['user_id']) . "
ORDER BY a.appointment_date, a.appointment_time
LIMIT $limit OFFSET $offset
";



$result = $conn->query($query);
if (!$result) die("❌ Query Error: " . $conn->error);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-confirmed { color: green; font-weight: bold; }
        .status-cancelled { color: red; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        form { margin-bottom: 15px; }
        form input, form select { padding: 5px; margin-right: 5px; }
    </style>
</head>
<body>
<h2>Doctor Dashboard</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> | <a href="../public/logout.php">Logout</a></p>

<form method="GET">
    <input type="text" name="search" placeholder="Search patient or date" value="<?php echo htmlspecialchars($search); ?>">

    <select name="status[]" multiple class="select2-multiple">
        <option value="confirmed" <?php if(in_array('confirmed', $status_filter)) echo 'selected'; ?>>Confirmed</option>
        <option value="pending" <?php if(in_array('pending', $status_filter)) echo 'selected'; ?>>Pending</option>
        <option value="cancelled" <?php if(in_array('cancelled', $status_filter)) echo 'selected'; ?>>Cancelled</option>
    </select>

    <button type="submit">Filter</button>
</form>
<p><small>Hold Ctrl (Windows) or Cmd (Mac) to select multiple statuses</small></p>

<table>
    <tr>
        <th>ID</th>
        <th>Patient</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $status_class = '';
            if ($row['status'] === 'confirmed') $status_class = 'status-confirmed';
            elseif ($row['status'] === 'cancelled') $status_class = 'status-cancelled';
            elseif ($row['status'] === 'pending') $status_class = 'status-pending';

            echo '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.$row['patient_name'].'</td>
                    <td>'.$row['appointment_date'].'</td>
                    <td>'.$row['appointment_time'].'</td>
                    <td class="'.$status_class.'">'.$row['status'].'</td>
                    <td>
                        <a href="update.php?id='.$row['id'].'" onclick="return confirm(\'Are you sure you want to update this appointment?\');">Update</a> | 
                        <a href="cancel.php?id='.$row['id'].'" onclick="return confirm(\'Are you sure you want to cancel this appointment?\');">Cancel</a>
                    </td>
                  </tr>';
        }
    } else {
        echo '<tr><td colspan="6">No appointments found.</td></tr>';
    }
    ?>
</table>

<!-- Pagination links -->
<?php if ($total_pages > 1): ?>
<p>
<?php
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        echo "<strong>$i</strong> ";
    } else {
        echo '<a href="doctor_dashboard.php?page='.$i.'&search='.urlencode($search).'&'.http_build_query(['status'=>$status_filter]).'">'.$i.'</a> ';
    }
}
?>
</p>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>
</body>
</html>
