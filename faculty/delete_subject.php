<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../auth/login.php");
    exit;
}

$subjectId = $_GET['id'];

/* Delete dependent records */
mysqli_query($conn, "DELETE FROM student_marks WHERE subject_id=$subjectId");
mysqli_query($conn, "DELETE FROM student_attendance WHERE subject_id=$subjectId");
mysqli_query($conn, "DELETE FROM subjects WHERE id=$subjectId");

/* (Optional) Audit log */
mysqli_query($conn, "
    INSERT INTO audit_logs (user_name, action)
    VALUES ('".$_SESSION['name']."', 'Deleted subject ID $subjectId')
");

header("Location: manage_subjects.php");
exit;
