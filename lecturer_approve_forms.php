<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

// Get lecturer_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$lecturer = $res->fetch_assoc();

$lecturer_id = $lecturer['lecturer_id'];

$forms = $conn->query("
    SELECT f.form_id, f.title, f.description 
    FROM feedback_forms f
    JOIN form_lecturers fl ON f.form_id = fl.form_id
    WHERE fl.lecturer_id = $lecturer_id AND fl.is_approved = 0
");

while ($form = $forms->fetch_assoc()):
?>
    <div>
        <h4><?= $form['title'] ?></h4>
        <p><?= $form['description'] ?></p>
        <form method="post" action="approve_form.php">
            <input type="hidden" name="form_id" value="<?= $form['form_id'] ?>">
            <button type="submit">Approve</button>
        </form>
    </div>
<?php endwhile; ?>
