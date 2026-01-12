<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty') {
    header("Location: ../auth/login.php");
    exit;
}

/* ===============================
   SAFE STUDENT ID HANDLING
   =============================== */
if (isset($_GET['student_id'])) {
    $studentId = intval($_GET['student_id']);
} elseif (isset($_POST['student_id'])) {
    $studentId = intval($_POST['student_id']);
} else {
    echo "Student ID missing.";
    exit;
}

/* Fetch subjects */
$subjects = mysqli_query($conn, "SELECT * FROM subjects");

/* Save marks */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectId = intval($_POST['subject']);
    $marks = intval($_POST['marks']);
    $attendance = intval($_POST['attendance']);

    /* Check if record exists */
    $check = mysqli_query($conn, "
        SELECT id FROM student_marks 
        WHERE student_id=$studentId AND subject_id=$subjectId
    ");

    if ($check && mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "
            UPDATE student_marks 
            SET marks=$marks 
            WHERE student_id=$studentId AND subject_id=$subjectId
        ");
    } else {
        mysqli_query($conn, "
            INSERT INTO student_marks (student_id, subject_id, marks)
            VALUES ($studentId, $subjectId, $marks)
        ");
    }

    /* Attendance */
    $checkA = mysqli_query($conn, "
        SELECT id FROM student_attendance 
        WHERE student_id=$studentId AND subject_id=$subjectId
    ");

    if ($checkA && mysqli_num_rows($checkA) > 0) {
        mysqli_query($conn, "
            UPDATE student_attendance 
            SET attendance_percent=$attendance
            WHERE student_id=$studentId AND subject_id=$subjectId
        ");
    } else {
        mysqli_query($conn, "
            INSERT INTO student_attendance (student_id, subject_id, attendance_percent)
            VALUES ($studentId, $subjectId, $attendance)
        ");
    }

    /* ✅ CORRECT REDIRECT */
    header("Location: ../reports/student_profile.php?id=$studentId");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add / Update Marks</title>

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

<h2>📝 Add / Update Marks</h2>

<form method="POST">
    <input type="hidden" name="student_id" value="<?php echo $studentId; ?>">

    <select name="subject" required>
        <option value="">Select Subject</option>
        <?php while ($s = mysqli_fetch_assoc($subjects)) { ?>
        <option value="<?php echo $s['id']; ?>">
            <?php echo htmlspecialchars($s['name']); ?>
        </option>
        <?php } ?>
    </select>

    <input type="number" name="marks" placeholder="Marks (0–100)" min="0" max="100" required>
    <input type="number" name="attendance" placeholder="Attendance %" min="0" max="100" required>

    <button>💾 Save</button>
</form>

<div class="actions">
    <a class="btn btn-secondary" href="../reports/student_profile.php?id=<?php echo $studentId; ?>">⬅ Back</a>
    <a class="btn btn-primary" href="dashboard.php">🏠 Dashboard</a>
</div>

</div>

</body>
</html>
