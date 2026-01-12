<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

/* Badge logic */
function performanceBadge($value){
    if ($value >= 75) return "🟢 Good";
    if ($value >= 50) return "🟡 Average";
    return "🔴 Needs Improvement";
}

/* Department-wise data */
$data = mysqli_query($conn, "
    SELECT 
        s.department,
        ROUND(AVG(m.marks),1) AS avg_marks,
        ROUND(AVG(a.attendance_percent),1) AS avg_attendance
    FROM students s
    LEFT JOIN student_marks m ON s.id = m.student_id
    LEFT JOIN student_attendance a ON s.id = a.student_id
    GROUP BY s.department
");

$departments = [];
$marks = [];
$attendance = [];

while ($row = mysqli_fetch_assoc($data)) {
    $departments[] = $row['department'];
    $marks[] = $row['avg_marks'] ?? 0;
    $attendance[] = $row['avg_attendance'] ?? 0;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Department Performance Comparison</title>
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
    margin-bottom:25px;
}
.header h2{
    margin:0;
}
.header p{
    margin:6px 0 0;
    color:#555;
}

/* ===== GLASS CARD ===== */
.glass-card{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(14px);
    padding:25px;
    border-radius:16px;
    margin-bottom:30px;
    box-shadow:0 12px 30px rgba(0,0,0,0.08);
    animation:floatIn 0.8s ease;
}
@keyframes floatIn{
    from{opacity:0;transform:translateY(15px)}
    to{opacity:1;transform:translateY(0)}
}

/* ===== TABLE ===== */
.table-wrap{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(14px);
    border-radius:16px;
    box-shadow:0 12px 30px rgba(0,0,0,0.08);
    overflow:hidden;
}
table{
    width:100%;
    border-collapse:collapse;
}
th{
    background:#212529;
    color:#fff;
    padding:14px;
}
td{
    padding:14px;
    border-bottom:1px solid #e6e6e6;
    text-align:center;
}
tr:hover{
    background:rgba(13,110,253,0.06);
}

/* ===== ACTIONS ===== */
.actions{
    margin-top:20px;
}
.actions a{
    margin-right:14px;
    text-decoration:none;
    color:#0d6efd;
    font-weight:500;
}
.actions a:hover{
    text-decoration:underline;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <h2>🏫 Department Performance Comparison</h2>
    <p>Overall academic performance across departments</p>
</div>

<!-- CHART -->
<div class="glass-card">
    <canvas id="deptChart"></canvas>
</div>

<!-- TABLE -->
<div class="table-wrap">
<table>
<tr>
    <th>Department</th>
    <th>Avg Marks</th>
    <th>Avg Attendance</th>
    <th>Overall Performance</th>
</tr>

<?php
mysqli_data_seek($data, 0);
while ($row = mysqli_fetch_assoc($data)) {
    $overall = round((($row['avg_marks'] ?? 0) + ($row['avg_attendance'] ?? 0)) / 2, 1);
?>
<tr>
    <td><?php echo htmlspecialchars($row['department']); ?></td>
    <td><?php echo $row['avg_marks'] ?? 0; ?></td>
    <td><?php echo $row['avg_attendance'] ?? 0; ?>%</td>
    <td><?php echo performanceBadge($overall); ?></td>
</tr>
<?php } ?>
</table>
</div>

<!-- ACTIONS -->
<div class="actions">
    <a href="dashboard.php">⬅ Back to Dashboard</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<script>
new Chart(document.getElementById('deptChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($departments); ?>,
        datasets: [
            {
                label: 'Average Marks',
                data: <?php echo json_encode($marks); ?>,
                backgroundColor: 'rgba(54,162,235,0.7)'
            },
            {
                label: 'Average Attendance',
                data: <?php echo json_encode($attendance); ?>,
                backgroundColor: 'rgba(40,167,69,0.7)'
            }
        ]
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
