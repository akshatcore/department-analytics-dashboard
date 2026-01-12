<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>
/* ===== ANIMATED BACKGROUND ===== */
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:'Segoe UI',Arial,sans-serif;
    background:linear-gradient(-45deg,#e3f2fd,#fce4ec,#e8f5e9,#ede7f6);
    background-size:400% 400%;
    animation:bgMove 12s ease infinite;
}

@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* ===== LOGIN CARD ===== */
.login-box{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(16px);
    padding:35px;
    width:360px;
    border-radius:20px;
    box-shadow:0 25px 50px rgba(0,0,0,0.15);
    animation:fadeUp 0.8s ease;
}

@keyframes fadeUp{
    from{opacity:0;transform:translateY(20px)}
    to{opacity:1;transform:translateY(0)}
}

.login-box h2{
    text-align:center;
    margin-bottom:25px;
}

/* ===== INPUTS ===== */
label{
    font-weight:600;
    font-size:14px;
}

input{
    width:100%;
    padding:12px;
    margin-top:6px;
    margin-bottom:16px;
    border-radius:10px;
    border:1px solid #ccc;
}

input:focus{
    outline:none;
    border-color:#0d6efd;
    box-shadow:0 0 0 3px rgba(13,110,253,0.2);
}

/* ===== ROLE BUTTONS ===== */
.role-box{
    display:flex;
    gap:10px;
    margin:10px 0 20px;
}

.role-box input{
    display:none;
}

.role-box label{
    flex:1;
    text-align:center;
    padding:10px;
    border-radius:10px;
    background:#e9ecef;
    cursor:pointer;
    font-weight:600;
    transition:0.3s;
}

.role-box input:checked + label{
    background:#0d6efd;
    color:#fff;
}

/* ===== BUTTON ===== */
button{
    width:100%;
    padding:12px;
    border-radius:12px;
    border:none;
    background:#0d6efd;
    color:#fff;
    font-size:15px;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#084298;
}
</style>
</head>

<body>

<div class="login-box">
<h2>🔐 Login</h2>

<form method="POST" action="login_process.php">

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Role</label>
    <div class="role-box">
        <input type="radio" id="admin" name="role" value="admin" required>
        <label for="admin">Admin</label>

        <input type="radio" id="faculty" name="role" value="faculty">
        <label for="faculty">Faculty</label>

        <input type="radio" id="student" name="role" value="student">
        <label for="student">Student</label>
    </div>

    <button type="submit">🔓 Login</button>
</form>
</div>

</body>
</html>
