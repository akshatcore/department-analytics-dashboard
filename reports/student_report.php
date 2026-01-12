<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* SAFE STUDENT ID */
if (isset($_GET['id'])) {
    $studentId = intval($_GET['id']);
} elseif (isset($_GET['student_id'])) {
    $studentId = intval($_GET['student_id']);
} else {
    echo "Student ID missing.";
    exit;
}

/* STUDENT INFO */
$studentRes = mysqli_query($conn,"SELECT * FROM students WHERE id=$studentId");
if (!$studentRes || mysqli_num_rows($studentRes) === 0) {
    echo "Invalid student record.";
    exit;
}
$student = mysqli_fetch_assoc($studentRes);

/* SUBJECT DATA */
$q = mysqli_query($conn,"
    SELECT 
        sub.name AS subject,
        m.marks,
        a.attendance_percent
    FROM subjects sub
    LEFT JOIN student_marks m 
        ON sub.id = m.subject_id AND m.student_id = $studentId
    LEFT JOIN student_attendance a 
        ON sub.id = a.subject_id AND a.student_id = $studentId
");

$subjects = [];
$marks = [];
$attendance = [];
$totalMarks = 0;
$totalAttendance = 0;
$count = 0;

while ($r = mysqli_fetch_assoc($q)) {
    $subjects[] = $r['subject'];
    $marks[] = intval($r['marks'] ?? 0);
    $attendance[] = intval($r['attendance_percent'] ?? 0);

    if ($r['marks'] !== null) {
        $totalMarks += $r['marks'];
        $totalAttendance += $r['attendance_percent'];
        $count++;
    }
}

$avgMarks = $count ? round($totalMarks / $count, 1) : 0;
$avgAttendance = $count ? round($totalAttendance / $count, 1) : 0;

/* REMARK */
if ($avgMarks >= 75) {
    $remark = "Excellent academic performance. Keep up the great work.";
} elseif ($avgMarks >= 50) {
    $remark = "Satisfactory performance. There is scope for improvement.";
} else {
    $remark = "Needs improvement. Academic support is recommended.";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Performance Report</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    font-family: Arial;
    background:#f4f6f8;
    padding:20px;
}
.container{
    background:#fff;
    padding:20px;
    border-radius:8px;
}
h2{text-align:center;margin-bottom:10px;}
.info p{margin:4px 0;font-size:14px;}

.flex{
    display:flex;
    gap:20px;
    margin-top:15px;
    align-items:center;
}

.chart-box{
    width:260px;
    aspect-ratio:1/1;
}

.chart-box canvas{
    width:100% !important;
    height:100% !important;
}

.table-box{
    flex:1;
}

table{
    width:100%;
    border-collapse:collapse;
    font-size:13px;
}
th,td{
    border:1px solid #ccc;
    padding:6px;
    text-align:center;
}
th{background:#f0f0f0;}

.section-title{
    margin-top:20px;
    font-weight:bold;
}

.remark{
    margin-top:15px;
    padding:12px;
    background:#eef5ff;
    border-left:5px solid #007bff;
}

.print-btn{margin-top:15px;}

@media print{
    body{background:#fff;padding:0;}
    .container{page-break-inside:avoid;}
    .flex,.remark{page-break-inside:avoid;}
    .print-btn{display:none;}
}
</style>
</head>

<body>

<div class="container">

<h2>Student Performance Report</h2>

<div class="info">
<p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
<p><strong>Department:</strong> <?php echo htmlspecialchars($student['department']); ?></p>
<p><strong>Year / Semester:</strong> <?php echo htmlspecialchars($student['year']." / ".$student['semester']); ?></p>
</div>

<!-- MARKS SECTION -->
<div class="section-title">Marks Analysis</div>
<div class="flex">

<div class="chart-box">
<canvas id="marksChart"></canvas>
</div>

<div class="table-box">
<table>
<tr>
<th>Subject</th>
<th>Marks</th>
<th>Status</th>
</tr>
<?php foreach ($subjects as $i => $sub) {
    $m = $marks[$i];
    if ($m >= 75) $status = "🟢 Good";
    elseif ($m >= 50) $status = "🟡 Average";
    else $status = "🔴 Needs Improvement";
?>
<tr>
<td><?php echo htmlspecialchars($sub); ?></td>
<td><?php echo $m; ?></td>
<td><?php echo $status; ?></td>
</tr>
<?php } ?>
</table>
</div>

</div>

<!-- ATTENDANCE SECTION -->
<div class="section-title">Attendance Analysis</div>
<div class="flex">

<div class="chart-box">
<canvas id="attendanceChart"></canvas>
</div>

<div class="table-box">
<table>
<tr>
<th>Subject</th>
<th>Attendance (%)</th>
</tr>
<?php foreach ($subjects as $i => $sub) { ?>
<tr>
<td><?php echo htmlspecialchars($sub); ?></td>
<td><?php echo $attendance[$i]; ?>%</td>
</tr>
<?php } ?>
</table>
</div>

</div>

<!-- REMARK -->
<div class="remark">
<strong>Overall Average Marks:</strong> <?php echo $avgMarks; ?><br>
<strong>Overall Attendance:</strong> <?php echo $avgAttendance; ?>%<br><br>
<strong>Remark:</strong> <?php echo $remark; ?>
</div>

<div class="print-btn">
<button onclick="window.print()">📄 Download / Print Report</button>
<button onclick="history.back()">⬅ Back</button>
<a href="../student/dashboard.php">🏠 Go to Dashboard</a>


</div>

</div>

<script>
new Chart(document.getElementById('marksChart'), {
    type:'pie',
    data:{
        labels:<?php echo json_encode($subjects); ?>,
        datasets:[{
            data:<?php echo json_encode($marks); ?>,
            backgroundColor:['#4caf50','#2196f3','#ff9800','#9c27b0','#f44336']
        }]
    },
    options:{responsive:true,maintainAspectRatio:true}
});

new Chart(document.getElementById('attendanceChart'), {
    type:'pie',
    data:{
        labels:<?php echo json_encode($subjects); ?>,
        datasets:[{
            data:<?php echo json_encode($attendance); ?>,
            backgroundColor:['#81c784','#64b5f6','#ffb74d','#ba68c8','#e57373']
        }]
    },
    options:{responsive:true,maintainAspectRatio:true}
});
</script>

</body>
</html>
