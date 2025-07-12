<?php
session_start();
include("include/dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);

    // Get update data
    $stmt = $conn->prepare("SELECT * FROM student_updates WHERE update_id = ?");
    $stmt->bind_param("i", $update_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $update = $result->fetch_assoc();

    if ($update) {
        // Update students table
       // ✅ FIXED: Update students table correctly
$updateStmt = $conn->prepare("   UPDATE students
    SET faculty_id = ?, course_id = ?, status = 'approved', year_of_study = ?
    WHERE user_id = ?
");
$updateStmt->bind_param("iiii", $update['faculty_id'], $update['course_id'], $update['year_of_study'], $update['user_id']);
$updateStmt->execute();


        // Get student_id and year_of_study
        $getStudent = $conn->prepare("SELECT student_id, year_of_study FROM students WHERE user_id = ?");
        $getStudent->bind_param("i", $update['user_id']);
        $getStudent->execute();
        $studentResult = $getStudent->get_result();
        $student = $studentResult->fetch_assoc();
        $student_id = $student['student_id'];
        $year_of_study = $student['year_of_study'];

        // Get course_id from course name
        $courseQuery = $conn->prepare("SELECT course_id FROM course WHERE course_name = ?");
        $courseQuery->bind_param("s", $update['student_course']);
        $courseQuery->execute();
        $courseResult = $courseQuery->get_result();
        $course = $courseResult->fetch_assoc();

        if ($course) {
            $course_id = $course['course_id'];

            // Insert into student_courses
            $insertSC = $conn->prepare("INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)");
            $insertSC->bind_param("ii", $student_id, $course_id);
            $insertSC->execute();

            // Assign units for this course & year
            $unitQuery = $conn->prepare("SELECT unit_id FROM units WHERE course_id = ? AND year_of_study = ?");
            $unitQuery->bind_param("ii", $course_id, $year_of_study);
            $unitQuery->execute();
            $unitResult = $unitQuery->get_result();

            $insertUnit = $conn->prepare("INSERT INTO student_units (unit_id, student_id) VALUES (?, ?)");
            while ($unit = $unitResult->fetch_assoc()) {
                $insertUnit->bind_param("ii", $unit['unit_id'], $student_id);
                $insertUnit->execute();
            }
        }

        // Delete from student_updates
        $deleteStmt = $conn->prepare("DELETE FROM student_updates WHERE update_id = ?");
        $deleteStmt->bind_param("i", $update_id);
        $deleteStmt->execute();
    }

    header("Location: manage_student_updates.php");
    exit();
}
?>
