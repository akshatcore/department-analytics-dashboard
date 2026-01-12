# 📊 Department Analytics Dashboard  
### Academic Performance, Attendance & Resource Management System

The **Department Analytics Dashboard** is a web-based academic management system designed to help **institutions, faculty, and students** monitor academic performance, attendance trends, and learning resources in a centralized and secure manner.

This project focuses on **real-world institutional needs**, combining analytics, role-based access, and clean UI/UX to improve transparency and academic decision-making.

---

## 🎯 Purpose of the Project

Traditional academic tracking systems are fragmented and lack meaningful analytics.  
This system solves that by providing:

- Centralized academic data
- Role-based dashboards (Admin / Faculty / Student)
- Performance analytics with visual insights
- Secure access and audit tracking
- Lab & resource version management

The project is designed for **colleges, departments, and academic institutions**.

---

## 👥 User Roles

### 👑 Admin (Principal / HOD)
- View department-wide performance
- Compare departments and semesters
- Monitor audit logs
- Access lab & resource notes
- Analyze subject difficulty

### 👨‍🏫 Faculty
- Add and manage students
- Upload marks and attendance
- Manage subjects
- Upload lab manuals and resources with versioning

### 🎓 Student
- View personal dashboard
- Track marks and attendance visually
- Download academic reports
- Access latest lab and resource notes

---

## ✨ Key Features

- 📊 Interactive dashboards with charts
- 🧠 KPI-based academic evaluation
- 📅 Weekly / Monthly / Yearly filters
- 🧪 Lab & resource management with version control
- 📄 Downloadable student reports
- 🔐 Role-based authentication
- 📝 Audit logs for accountability
- 🎨 Modern animated UI (glassmorphism)

---

## 🛠 Tech Stack

- **Frontend:** HTML, CSS (Animated UI), JavaScript
- **Backend:** PHP (Core PHP)
- **Database:** MySQL / MariaDB
- **Charts:** Chart.js
- **Server:** Apache (XAMPP / LAMP)
- **Version Control:** Git & GitHub

---

## 🔄 Application Workflow

1. Secure login based on role  
2. Redirect to role-specific dashboard  
3. Data fetched dynamically from database  
4. Charts and KPIs generated in real time  
5. Reports and resources available for download  

---

## 🎥 Project Demonstration Videos

All videos below are recorded from the actual running system.

### 👑 Admin Dashboard
▶️ [Admin Dashboard Demo](Videos/admin_dashboard.mp4)  
Shows department comparison, subject difficulty analysis, audit logs, and analytics.

---

### 👨‍🏫 Faculty Panel
▶️ [Faculty Panel Demo](Videos/faculty_panel.mp4)  
Demonstrates student management, marks entry, subject handling, and resource uploads.

---

### 🎓 Student Dashboard
▶️ [Student Dashboard Demo](Videos/student_dashboard.mp4)  
Displays marks and attendance using interactive pie charts and downloadable reports.

---

### 🧪 Lab & Resource Management
▶️ [Lab & Resources Demo](Videos/lab_resources.mp4)  
Shows versioned lab manuals, resource filtering, and student access.


📥 Installation (Localhost)
1️⃣ Clone Repository
git clone https://github.com/akshatcore/department-analytics-dashboard.git

2️⃣ Move to Server Directory
htdocs/department-dashboard

3️⃣ Database Setup

Import the provided .sql file into MySQL

Configure credentials in:

config/db.php

4️⃣ Start Server

Start Apache & MySQL via XAMPP

Open in browser:

http://localhost/department-dashboard/login.php
