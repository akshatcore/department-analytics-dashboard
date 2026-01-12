<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dept = $_POST['department'];
    $year = $_POST['year'];
    $sem  = $_POST['semester'];

    mysqli_query($conn,"
        INSERT INTO users (name,email,password,role)
        VALUES ('$name','$email','$password','student')
    ");

    $uid = mysqli_insert_id($conn);

    mysqli_query($conn,"
        INSERT INTO students (user_id,name,department,year,semester)
        VALUES ($uid,'$name','$dept','$year','$sem')
    ");

    mysqli_query($conn,"
        INSERT INTO audit_logs (user_name, action)
        VALUES ('".$_SESSION['name']."', 'Added student: $name')
    ");

    mysqli_query($conn,"
        INSERT INTO audit_logs (user_name, action)
        VALUES ('".$_SESSION['name']."', 'Updated marks for student ID $studentId')
    ");

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Student</title>

<style>
/* ===== BACKGROUND ===== */
body{
    font-family:'Segoe UI',Arial,sans-serif;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(-45deg,#e3f2fd,#e8f5e9,#fce4ec,#ede7f6);
    background-size:400% 400%;
    animation:bgMove 14s ease infinite;
}
@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* ===== CARD ===== */
.card{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(14px);
    padding:30px;
    width:380px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,0.12);
    animation:slideUp 0.8s ease;
}
@keyframes slideUp{
    from{opacity:0;transform:translateY(20px)}
    to{opacity:1;transform:translateY(0)}
}

h2{
    text-align:center;
    margin-bottom:20px;
}

/* ===== FORM ===== */
input,select,button{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:14px;
}

button{
    background:#0d6efd;
    color:#fff;
    border:none;
    cursor:pointer;
    transition:0.3s;
}
button:hover{
    background:#084298;
}

/* ===== ACTIONS ===== */
.actions{
    display:flex;
    justify-content:space-between;
    margin-top:15px;
}
.btn{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
    color:#fff;
}
.btn-secondary{background:#6c757d}
.btn-primary{background:#198754}
.btn:hover{opacity:0.9}
</style>
</head>

<body>

<div class="card">

<h2>➕ Add New Student</h2>

<form method="POST">
    <input name="name" placeholder="Student Name" required>
    <input name="email" placeholder="Student Email" required>
    <input name="password" placeholder="Temporary Password" required>

    <select name="department" required>
        <option value="">Select Department</option>
        <option value="IT">IT</option>
        <option value="CM">CM</option>
        <option value="ME">ME</option>
        <option value="CE">CE</option>
    </select>

    <select name="year" required>
        <option value="">Select Year</option>
        <option value="1st">1st</option>
        <option value="2nd">2nd</option>
        <option value="3rd">3rd</option>
    </select>

    <select name="semester" required>
        <option value="">Select Semester</option>
        <option value="Semester 1">Semester 1</option>
        <option value="Semester 2">Semester 2</option>
        <option value="Semester 3">Semester 3</option>
        <option value="Semester 4">Semester 4</option>
        <option value="Semester 5">Semester 5</option>
        <option value="Semester 6">Semester 6</option>
    </select>

    <button>💾 Add Student</button>
</form>

<div class="actions">
    <button class="btn btn-secondary" onclick="history.back()">⬅ Back</button>
    <a class="btn btn-primary" href="dashboard.php">🏠 Dashboard</a>
</div>

</div>

</body>
</html>
