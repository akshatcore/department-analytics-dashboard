<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../auth/login.php");
    exit;
}

$studentId = $_GET['id'];

/* Get user_id */
$student = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT user_id FROM students WHERE id=$studentId")
);

$userId = $student['user_id'];

/* Delete related data */
mysqli_query($conn, "DELETE FROM student_marks WHERE student_id=$studentId");
mysqli_query($conn, "DELETE FROM student_attendance WHERE student_id=$studentId");
mysqli_query($conn, "DELETE FROM students WHERE id=$studentId");
mysqli_query($conn, "DELETE FROM users WHERE id=$userId");

/* (Optional) Audit log */
mysqli_query($conn, "
    INSERT INTO audit_logs (user_name, action)
    VALUES ('".$_SESSION['name']."', 'Deleted student ID $studentId')
");

header("Location: dashboard.php");
exit;
