<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$lecturer_id = $conn->query("SELECT lecturer_id FROM lecturers WHERE user_id = $user_id")->fetch_assoc()['lecturer_id'] ?? 0;

// Add unit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_unit_id'])) {
    $unit_id = intval($_POST['add_unit_id']);
    $check = $conn->prepare("SELECT 1 FROM lecturer_units WHERE lecturer_id = ? AND unit_id = ?");
    $check->bind_param("ii", $lecturer_id, $unit_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO lecturer_units (lecturer_id, unit_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $lecturer_id, $unit_id);
        $stmt->execute();
    }
    header("Location: lecturer_manage_units.php");
    exit();
}

// Remove unit
if (isset($_GET['remove_unit_id'])) {
    $remove_unit_id = intval($_GET['remove_unit_id']);
    $stmt = $conn->prepare("DELETE FROM lecturer_units WHERE lecturer_id = ? AND unit_id = ?");
    $stmt->bind_param("ii", $lecturer_id, $remove_unit_id);
    $stmt->execute();
    header("Location: lecturer_manage_units.php");
    exit();
}

// Fetch all units
$all_units = $conn->query("SELECT * FROM units")->fetch_all(MYSQLI_ASSOC);

// Get assigned units
$assigned = $conn->query("
    SELECT lu.unit_id, u.unit_name 
    FROM lecturer_units lu 
    JOIN units u ON lu.unit_id = u.unit_id 
    WHERE lu.lecturer_id = $lecturer_id
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Units</title>
    <link rel="stylesheet" href="lecturer_manage.css">
</head>
<body>
    <h2>Your Units</h2>
    <ul>
        <?php foreach ($assigned as $u): ?>
            <li>
                <?= htmlspecialchars($u['unit_name']) ?>
                <a href="?remove_unit_id=<?= $u['unit_id'] ?>">Remove</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Add New Unit</h3>
    <form method="post">
        <select name="add_unit_id">
            <?php foreach ($all_units as $u): ?>
                <option value="<?= $u['unit_id'] ?>"><?= htmlspecialchars($u['unit_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add Unit</button>
    </form><br><br>

</body>
</html>
