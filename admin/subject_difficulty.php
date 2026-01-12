<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

/* Fetch subject-wise average marks */
$data = mysqli_query($conn, "
    SELECT sub.name AS subject, AVG(m.marks) AS avg_marks
    FROM subjects sub
    LEFT JOIN student_marks m ON sub.id = m.subject_id
    GROUP BY sub.id
");

$subjects = [];
$averages = [];
$colors = [];

while ($row = mysqli_fetch_assoc($data)) {
    $subjects[] = $row['subject'];
    $avg = round($row['avg_marks'] ?? 0, 1);
    $averages[] = $avg;

    /* Color logic */
    if ($avg >= 75) {
        $colors[] = "rgba(40,167,69,0.8)";   // Green
    } elseif ($avg >= 50) {
        $colors[] = "rgba(255,193,7,0.8)";   // Yellow
    } else {
        $colors[] = "rgba(220,53,69,0.8)";   // Red
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Subject Difficulty Analysis</title>
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
.header p{
    margin:6px 0 0;
    color:#555;
}
.header .actions a,
.header .actions button{margin-left:10px}

/* ===== LEGEND ===== */
.legend{
    margin:15px 0 25px;
}
.legend span{
    margin-right:20px;
    font-weight:600;
}

/* ===== GLASS CARD ===== */
.glass-card{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(14px);
    padding:25px;
    border-radius:16px;
    box-shadow:0 12px 30px rgba(0,0,0,0.08);
    animation:floatIn 0.8s ease;
}
@keyframes floatIn{
    from{opacity:0;transform:translateY(15px)}
    to{opacity:1;transform:translateY(0)}
}

/* ===== BUTTONS ===== */
.btn{
    padding:8px 14px;
    border-radius:8px;
    border:none;
    cursor:pointer;
    text-decoration:none;
    font-size:14px;
    color:#fff;
}
.btn-primary{background:#0d6efd}
.btn-secondary{background:#6c757d}
.btn-danger{background:#dc3545}
.btn:hover{opacity:0.9}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div>
        <h2>📘 Subject Difficulty Analysis</h2>
        <p>Difficulty level based on average student marks</p>
    </div>
    <div class="actions">
        <a class="btn btn-primary" href="dashboard.php">🏠 Dashboard</a>
        <button class="btn btn-secondary" onclick="history.back()">⬅ Back</button>
        <a class="btn btn-danger" href="../auth/logout.php">Logout</a>
    </div>
</div>

<!-- LEGEND -->
<div class="legend">
    <span style="color:#28a745;">🟢 Easy (≥75)</span>
    <span style="color:#ffc107;">🟡 Moderate (50–74)</span>
    <span style="color:#dc3545;">🔴 Difficult (&lt;50)</span>
</div>

<!-- CHART -->
<div class="glass-card">
    <canvas id="difficultyChart"></canvas>
</div>

<script>
new Chart(document.getElementById('difficultyChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($subjects); ?>,
        datasets: [{
            label: 'Average Marks',
            data: <?php echo json_encode($averages); ?>,
            backgroundColor: <?php echo json_encode($colors); ?>
        }]
    },
    options: {
        responsive:true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
</script>

</body>
</html>
