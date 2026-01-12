# 📊 Department Analytics Dashboard

### Academic Performance, Attendance & Resource Management System

The **Department Analytics Dashboard** is a web-based academic management system designed to help **institutions, faculty, and students** monitor academic performance, attendance trends, and learning resources in a centralized and secure manner.

This project focuses on **real-world institutional needs**, combining analytics, role-based access control, and a modern UI/UX to improve transparency and academic decision-making.

---

## 🎯 Purpose of the Project

Traditional academic tracking systems are often fragmented, manual, and lack meaningful analytics.
This system addresses those limitations by providing:

* Centralized academic and attendance data
* Role-based dashboards for different stakeholders
* Visual performance analytics for faster insights
* Secure access with accountability tracking
* Structured lab and resource management

The project is suitable for **colleges, departments, and academic institutions**.

---

## 👥 User Roles & Capabilities

### 👑 Admin (Principal / HOD)

* View department-wide academic performance
* Compare departments and semesters
* Analyze subject difficulty levels
* Monitor audit logs for actions taken
* Access all lab and resource notes

### 👨‍🏫 Faculty

* Add and manage students
* Enter and update marks & attendance
* Create and manage subjects
* Upload lab manuals and academic resources
* Maintain version history of resources

### 🎓 Student

* View personal academic dashboard
* Track marks and attendance visually
* Download detailed academic reports
* Access the latest lab manuals and resources

---

## ✨ Key Features

* 📊 Interactive dashboards with real-time charts
* 🧠 KPI-based academic performance evaluation
* 📅 Weekly / Monthly / Yearly data filters
* 🧪 Lab & resource management with version control
* 📄 Downloadable student performance reports
* 🔐 Secure role-based authentication
* 📝 Audit logs for transparency and accountability
* 🎨 Modern animated UI with glassmorphism design

---

## 🛠 Tech Stack

* **Frontend:** HTML, CSS (Animated UI), JavaScript
* **Backend:** PHP (Core PHP)
* **Database:** MySQL / MariaDB
* **Charts & Analytics:** Chart.js
* **Server Environment:** Apache (XAMPP / LAMP)
* **Version Control:** Git & GitHub

---

## 🔄 Application Workflow

1. User logs in with role-based authentication
2. System redirects to the respective dashboard
3. Data is fetched dynamically from the database
4. Charts, KPIs, and summaries are generated in real time
5. Reports and resources are available for viewing and download

---

## 🎥 Project Demonstration Videos

All videos below are recorded from the **actual running system**.

### 👑 Admin Dashboard

▶️ [Admin Dashboard Demo](Videos/admin_dashboard.mp4)
Shows department comparison, subject difficulty analysis, audit logs, and overall analytics.

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
Shows versioned lab manuals, resource filtering, and student access controls.

---

## 📥 Installation (Localhost Setup)

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/akshatcore/department-analytics-dashboard.git
```

### 2️⃣ Move Project to Server Directory

```text
htdocs/department-dashboard
```

### 3️⃣ Database Setup

* Import the provided `.sql` file into MySQL
* Configure database credentials in:

```text
config/db.php
```

### 4️⃣ Start Server

* Start **Apache** and **MySQL** using XAMPP
* Open the application in your browser:

```text
http://localhost/department-dashboard/auth/login.php
```

---

## 🚀 Future Enhancements

* Advanced analytics and trend prediction
* Exportable reports (PDF / Excel)
* Notification system for students and faculty
* Role-based permissions at subject level
* Deployment on a live server

---

## ⚠️ Disclaimer

This project is developed for **educational and demonstration purposes**.
For production use, additional security hardening, validation, and access controls are recommended.


