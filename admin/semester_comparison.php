<?php
session_start();
include("../config/db.php");

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$data = mysqli_query($conn,"
SELECT semester, AVG(marks) avg_marks
FROM students s
JOIN student_marks m ON s.id=m.student_id
GROUP BY semester
");

$sem=[];$avg=[];$colors=[];

while($r=mysqli_fetch_assoc($data)){
    $sem[]=$r['semester'];
    $avg[]=round($r['avg_marks']);
    $colors[]="rgb(".rand(50,200).",".rand(50,200).",".rand(50,200).")";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Semester Comparison</title>
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
.header .actions a,
.header .actions button{margin-left:10px}

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
.btn-secondary{background:#6c757d}
.btn-primary{background:#0d6efd}
.btn-danger{background:#dc3545}
.btn:hover{opacity:0.9}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div>
        <h2>📊 Semester-wise Performance Comparison</h2>
        <p>Average academic performance across semesters</p>
    </div>
    <div class="actions">
        <a class="btn btn-primary" href="dashboard.php">🏠 Dashboard</a>
        <button class="btn btn-secondary" onclick="history.back()">⬅ Back</button>
        <a class="btn btn-danger" href="../auth/logout.php">Logout</a>
    </div>
</div>

<!-- CHART -->
<div class="glass-card">
    <canvas id="semChart"></canvas>
</div>

<script>
new Chart(semChart,{
    type:'bar',
    data:{
        labels:<?php echo json_encode($sem); ?>,
        datasets:[{
            label:'Average Marks',
            data:<?php echo json_encode($avg); ?>,
            backgroundColor:<?php echo json_encode($colors); ?>
        }]
    },
    options:{
        responsive:true,
        scales:{
            y:{beginAtZero:true,max:100}
        }
    }
});
</script>

</body>
</html>
