# My Audits

A file download tracking system designed to monitor and log access to sensitive files. This application helps administrators identify who downloaded specific files, when the download occurred, how often a file has been downloaded, and how large the downloaded files were — providing traceability in case of file leaks or misuse.

## 🔐 Purpose

In environments where file confidentiality is critical (e.g., audits, internal reports, legal documents), it's important to track **who accessed what**. If a file is leaked or misused, this system helps identify the last known access and responsible user.

## ✅ Key Features

- 📁 Upload and manage downloadable files
- 👥 User and Admin roles with login system
- ⬇️ Track every file download (who, when, how many times, file size)
- 📊 Daily download statistics
- 🧾 Access history log for audit purposes
- 🔍 Identify suspicious download behavior

## 🛠 Technologies Used

- **PHP** — Backend logic
- **MySQL** — Relational database to store user and download logs
- **HTML/CSS/JavaScript** — Frontend
- **Bootstrap** — UI design
- **jQuery & AJAX** — Interactivity and background requests
- **XAMPP** — Local development environment

## 📦 Installation

**Clone this repo** to your local GitHub folder:
   ```bash
   git clone https://github.com/TukangBakmi/My-Audits.git
