<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$student = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT id,name FROM students WHERE user_id=$userId")
);
$studentId = $student['id'];

/* Subject-wise marks */
$marksQ = mysqli_query($conn,"
    SELECT sub.name, AVG(m.marks) marks
    FROM subjects sub
    JOIN student_marks m ON sub.id=m.subject_id
    WHERE m.student_id=$studentId
    GROUP BY sub.id
");

/* Subject-wise attendance */
$attQ = mysqli_query($conn,"
    SELECT sub.name, AVG(a.attendance_percent) attendance
    FROM subjects sub
    JOIN student_attendance a ON sub.id=a.subject_id
    WHERE a.student_id=$studentId
    GROUP BY sub.id
");

$markLabels=[]; $markData=[]; $markColors=[];
$attLabels=[]; $attData=[]; $attColors=[];

function randColor(){
    return "rgba(".rand(50,200).",".rand(50,200).",".rand(50,200).",0.85)";
}

while($r=mysqli_fetch_assoc($marksQ)){
    $markLabels[]=$r['name'];
    $markData[]=$r['marks'];
    $markColors[]=randColor();
}

while($r=mysqli_fetch_assoc($attQ)){
    $attLabels[]=$r['name'];
    $attData[]=$r['attendance'];
    $attColors[]=randColor();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* ===== ANIMATED BACKGROUND ===== */
body{
    font-family:'Segoe UI',Arial,sans-serif;
    padding:20px;
    background:linear-gradient(-45deg,#e3f2fd,#fce4ec,#e8f5e9,#ede7f6);
    background-size:400% 400%;
    animation:bgMove 12s ease infinite;
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
.header .actions button{margin-left:10px;}

/* ===== GLASS SECTIONS ===== */
.section{
    background:rgba(255,255,255,0.65);
    backdrop-filter:blur(14px);
    padding:22px;
    border-radius:14px;
    margin-bottom:35px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    animation:floatIn 0.8s ease;
}
@keyframes floatIn{
    from{opacity:0;transform:translateY(15px)}
    to{opacity:1;transform:translateY(0)}
}
.section h3{margin-top:0}

/* ===== FLEX ===== */
.flex{
    display:flex;
    gap:35px;
    align-items:center;
}
.left{width:40%;}
.right{width:60%;}

/* ===== PIE CONTAINER ===== */
.chart-glass{
    background:rgba(255,255,255,0.55);
    backdrop-filter:blur(10px);
    padding:20px;
    border-radius:16px;          /* FIX: no oval */
    box-shadow:0 0 25px rgba(0,0,0,0.08);
    animation:pulse 4s ease-in-out infinite;

    display:flex;                /* center chart */
    align-items:center;
    justify-content:center;

    aspect-ratio: 1 / 1;         /* perfect square */
    max-width:320px;
}

/* ===== LEGEND ===== */
.legend{
    display:flex;
    align-items:center;
    margin-bottom:10px;
    padding:8px;
    border-radius:8px;
    transition:all 0.25s ease;
}
.legend:hover{
    background:rgba(0,123,255,0.08);
    transform:translateX(4px);
}
.color-box{
    width:14px;
    height:14px;
    margin-right:10px;
    border-radius:4px;
}

/* ===== BUTTONS ===== */
.btn{
    padding:8px 14px;
    border-radius:8px;
    border:none;
    cursor:pointer;
    text-decoration:none;
    font-size:14px;
}
.btn-primary{background:#007bff;color:#fff;}
.btn-secondary{background:#6c757d;color:#fff;}
.btn-danger{background:#dc3545;color:#fff;}
.btn:hover{opacity:0.9}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <h2>Welcome, <?php echo htmlspecialchars($student['name']); ?></h2>
    <div class="actions">
        <a class="btn btn-primary" href="../student/lab_resources.php">🧪 Lab & Resources</a>
        <a class="btn btn-primary" href="../reports/student_report.php?id=<?php echo $studentId; ?>" target="_blank">📄 Download Report</a>
        <button class="btn btn-secondary" onclick="history.back()">⬅ Back</button>
        <a class="btn btn-danger" href="../auth/logout.php">Logout</a>
    </div>
</div>

<!-- MARKS -->
<div class="section">
<h3>📊 Subject-wise Marks</h3>
<div class="flex">
    <div class="left chart-glass">
        <canvas id="marksChart"></canvas>
    </div>
    <div class="right">
        <?php for($i=0;$i<count($markLabels);$i++){ ?>
        <div class="legend">
            <div class="color-box" style="background:<?php echo $markColors[$i]; ?>"></div>
            <strong><?php echo htmlspecialchars($markLabels[$i]); ?></strong>
            &nbsp;— <?php echo round($markData[$i],1); ?>
        </div>
        <?php } ?>
    </div>
</div>
</div>

<!-- ATTENDANCE -->
<div class="section">
<h3>📊 Attendance Record</h3>
<div class="flex">
    <div class="left chart-glass">
        <canvas id="attChart"></canvas>
    </div>
    <div class="right">
        <?php for($i=0;$i<count($attLabels);$i++){ ?>
        <div class="legend">
            <div class="color-box" style="background:<?php echo $attColors[$i]; ?>"></div>
            <strong><?php echo htmlspecialchars($attLabels[$i]); ?></strong>
            &nbsp;— <?php echo round($attData[$i],1); ?>%
        </div>
        <?php } ?>
    </div>
</div>
</div>

<script>
new Chart(document.getElementById('marksChart'),{
    type:'pie',
    data:{labels:<?php echo json_encode($markLabels); ?>,
    datasets:[{data:<?php echo json_encode($markData); ?>,
    backgroundColor:<?php echo json_encode($markColors); ?>}]}
});

new Chart(document.getElementById('attChart'),{
    type:'pie',
    data:{labels:<?php echo json_encode($attLabels); ?>,
    datasets:[{data:<?php echo json_encode($attData); ?>,
    backgroundColor:<?php echo json_encode($attColors); ?>}]}
});
</script>

</body>
</html>
