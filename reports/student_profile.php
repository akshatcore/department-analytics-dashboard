<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* ===============================
   SAFE STUDENT ID HANDLING
   =============================== */
if (isset($_GET['id'])) {
    $studentId = intval($_GET['id']);
} elseif (isset($_GET['student_id'])) {
    $studentId = intval($_GET['student_id']);
} else {
    echo "Student ID missing.";
    exit;
}

/* ===============================
   BADGE LOGIC
   =============================== */
function performanceBadge($value){
    if ($value >= 75) return "🟢 Good";
    if ($value >= 50) return "🟡 Average";
    return "🔴 Needs Improvement";
}

/* ===============================
   DATE FILTER
   =============================== */
$range = $_GET['range'] ?? 'all';
$dateCondition = "";

if ($range === 'week') {
    $dateCondition = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($range === 'month') {
    $dateCondition = "AND MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())";
} elseif ($range === 'year') {
    $dateCondition = "AND YEAR(created_at)=YEAR(CURDATE())";
}

/* ===============================
   STUDENT INFO (SAFE)
   =============================== */
$studentRes = mysqli_query($conn, "SELECT * FROM students WHERE id=$studentId");

if (!$studentRes || mysqli_num_rows($studentRes) === 0) {
    echo "Invalid student record.";
    exit;
}

$student = mysqli_fetch_assoc($studentRes);

/* ===============================
   SAFE METRICS
   =============================== */
$avgMarks = 0;
$marksRes = mysqli_query(
    $conn,
    "SELECT AVG(marks) a FROM student_marks WHERE student_id=$studentId $dateCondition"
);
if ($marksRes) {
    $row = mysqli_fetch_assoc($marksRes);
    $avgMarks = round($row['a'] ?? 0, 1);
}

$avgAttendance = 0;
$attRes = mysqli_query(
    $conn,
    "SELECT AVG(attendance_percent) a FROM student_attendance WHERE student_id=$studentId $dateCondition"
);
if ($attRes) {
    $row = mysqli_fetch_assoc($attRes);
    $avgAttendance = round($row['a'] ?? 0, 1);
}

/* ===============================
   GOALS & PROGRESS
   =============================== */
$goalMarks = 75;
$goalAttendance = 80;

$marksProgress = $goalMarks > 0 ? min(100, round(($avgMarks / $goalMarks) * 100)) : 0;
$attendanceProgress = $goalAttendance > 0 ? min(100, round(($avgAttendance / $goalAttendance) * 100)) : 0;

/* ===============================
   KPIs
   =============================== */
$academicKPI = performanceBadge($avgMarks);
$attendanceKPI = performanceBadge($avgAttendance);
$overallScore = round(($avgMarks + $avgAttendance) / 2, 1);
$overallKPI = performanceBadge($overallScore);

/* ===============================
   SUBJECT-WISE GRAPH (SAFE)
   =============================== */
$subjects = [];
$marks = [];

$graphRes = mysqli_query($conn, "
    SELECT sub.name, AVG(m.marks) avg_marks
    FROM subjects sub
    LEFT JOIN student_marks m
        ON sub.id = m.subject_id
        AND m.student_id = $studentId
        $dateCondition
    GROUP BY sub.id
");

if ($graphRes) {
    while ($g = mysqli_fetch_assoc($graphRes)) {
        $subjects[] = $g['name'];
        $marks[] = round($g['avg_marks'] ?? 0, 1);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Profile</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* ===== BACKGROUND ===== */
body{
    font-family:'Segoe UI',Arial,sans-serif;
    padding:20px;
    background:linear-gradient(-45deg,#e3f2fd,#e8f5e9,#fce4ec,#ede7f6);
    background-size:400% 400%;
    animation:bgMove 14s ease infinite;
}
@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* ===== HEADER ===== */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}
.header h2{margin:0;}
.header p{margin:6px 0 0;color:#555}
.header .actions a,
.header .actions button{margin-left:10px}

/* ===== FILTER ===== */
.filter-btn{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    margin-right:6px;
    font-size:14px;
}
.active{background:#0d6efd;color:#fff}
.inactive{background:#e0e0e0;color:#000}

/* ===== CARDS ===== */
.cards{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
    margin-bottom:25px;
}
.card{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(14px);
    padding:20px;
    border-radius:16px;
    width:230px;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    animation:floatIn 0.8s ease;
}
@keyframes floatIn{
    from{opacity:0;transform:translateY(15px)}
    to{opacity:1;transform:translateY(0)}
}

/* ===== PROGRESS ===== */
.progress{
    background:#e0e0e0;
    border-radius:10px;
    height:12px;
    overflow:hidden;
}
.progress-bar{
    height:12px;
    background:#198754;
}

/* ===== BUTTONS ===== */
.btn{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
    color:#fff;
}
.btn-primary{background:#0d6efd}
.btn-secondary{background:#6c757d}
.btn-success{background:#198754}
.btn-danger{background:#dc3545}
.btn:hover{opacity:0.9}

/* ===== CHART CARD ===== */
.chart-card{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(14px);
    padding:25px;
    border-radius:18px;
    box-shadow:0 12px 30px rgba(0,0,0,0.1);
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div>
        <h2><?php echo htmlspecialchars($student['name']); ?></h2>
        <p><?php echo htmlspecialchars($student['department']." | ".$student['year']." | ".$student['semester']); ?></p>
    </div>
    <div class="actions">
        <?php if ($_SESSION['role'] === 'faculty') { ?>
        <a class="btn btn-success" href="../faculty/add_marks.php?student_id=<?php echo $studentId; ?>">➕ Add / Update Marks</a>
        <?php } ?>
        <a class="btn btn-primary" href="student_report.php?id=<?php echo $studentId; ?>" target="_blank">📄 Report</a>
        <button class="btn btn-secondary" onclick="history.back()">⬅ Back</button>
        <a class="btn btn-danger" href="../auth/logout.php">Logout</a>
    </div>
</div>

<!-- FILTER -->
<div style="margin-bottom:20px;">
<a class="filter-btn <?php echo ($range=='all')?'active':'inactive'; ?>" href="?id=<?php echo $studentId; ?>&range=all">All</a>
<a class="filter-btn <?php echo ($range=='week')?'active':'inactive'; ?>" href="?id=<?php echo $studentId; ?>&range=week">Weekly</a>
<a class="filter-btn <?php echo ($range=='month')?'active':'inactive'; ?>" href="?id=<?php echo $studentId; ?>&range=month">Monthly</a>
<a class="filter-btn <?php echo ($range=='year')?'active':'inactive'; ?>" href="?id=<?php echo $studentId; ?>&range=year">Yearly</a>
</div>

<!-- KPI -->
<div class="cards">
    <div class="card"><h3>Academic KPI</h3><p><?php echo $academicKPI; ?></p></div>
    <div class="card"><h3>Attendance KPI</h3><p><?php echo $attendanceKPI; ?></p></div>
    <div class="card"><h3>Overall KPI</h3><p><?php echo $overallKPI; ?></p></div>
</div>

<!-- PROGRESS -->
<div class="cards">
    <div class="card">
        <h3>Marks Progress</h3>
        <p><?php echo $avgMarks; ?> / <?php echo $goalMarks; ?></p>
        <div class="progress"><div class="progress-bar" style="width:<?php echo $marksProgress; ?>%"></div></div>
        <p><?php echo $marksProgress; ?>%</p>
    </div>

    <div class="card">
        <h3>Attendance Progress</h3>
        <p><?php echo $avgAttendance; ?>% / <?php echo $goalAttendance; ?>%</p>
        <div class="progress"><div class="progress-bar" style="width:<?php echo $attendanceProgress; ?>%"></div></div>
        <p><?php echo $attendanceProgress; ?>%</p>
    </div>
</div>

<!-- CHART -->
<div class="chart-card">
    <h3>📊 Subject-wise Performance</h3>
    <canvas id="marksChart"></canvas>
</div>

<script>
new Chart(document.getElementById('marksChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($subjects); ?>,
        datasets: [{
            label: 'Average Marks',
            data: <?php echo json_encode($marks); ?>,
            backgroundColor: 'rgba(54,162,235,0.7)'
        }]
    },
    options: {
        responsive:true,
        scales: {
            y: { beginAtZero:true, max:100 }
        }
    }
});
</script>

</body>
</html>
