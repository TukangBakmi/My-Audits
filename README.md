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
- **PHPSpreadsheet** — Excel file processing
- **PHPMailer** — Email functionality for password reset
- **Composer** — Dependency management
- **XAMPP** — Local development environment

## 📦 Installation

### Prerequisites
- **XAMPP** (or similar local server with PHP 7.4+ and MySQL/MariaDB)
- **Composer** (for dependency management)
- **Web browser**

### Setup Steps

1. **Clone this repository**:
   ```bash
   git clone https://github.com/TukangBakmi/My-Audits.git
   ```

2. **Move the project** to your web server directory:
   ```
   C:\xampp\htdocs\My-Audits
   ```

3. **Install dependencies** using Composer:
   ```bash
   cd My-Audits
   composer install
   ```

4. **Setup the database**:
   - Start XAMPP (Apache + MySQL)
   - Open [phpMyAdmin](http://localhost/phpmyadmin)
   - Create a new database named `maybank`
   - Import the SQL file: `config/maybank.sql`

5. **Configure database connection**:
   - Edit `config/dbconn.php` if needed
   - Default settings: `localhost`, `root`, no password, database: `maybank`

6. **Set file permissions**:
   - Ensure `assets/uploads/` directory is writable

7. **Access the application**:
   ```
   http://localhost/My-Audits
   ```

### Default Login Credentials

**Admin:**
- NPK: `412020031`
- Password: `Albert12`

## 📁 Project Structure

```
My-Audits/
├── admin/              # Admin panel pages
├── assets/uploads/      # File storage directory
├── backend/            # PHP backend logic
│   ├── admin/          # Admin-specific functions
│   ├── func/           # Utility functions
│   └── user/           # User-specific functions
├── config/             # Database configuration
├── static/             # CSS, navigation, session management
├── views/              # Frontend pages
└── vendor/             # Composer dependencies
```

## 🔒 Security Features

- **Password Hashing**: Uses PHP's `password_hash()` with bcrypt
- **SQL Injection Prevention**: Prepared statements throughout
- **Session Management**: Secure session handling with timeout
- **Input Sanitization**: All user inputs are cleaned and validated
- **File Upload Security**: Restricted file types and size limits
- **Access Control**: Role-based permissions (Admin/User)
- **Password Reset**: Secure token-based password recovery

## 📸 Screenshots
![image](https://github.com/user-attachments/assets/1c5aa1b0-6607-4c94-9ad4-ebb433b86731)

## 🛠️ Troubleshooting

**Common Issues:**

1. **"Database connection failed"**
   - Check if XAMPP MySQL is running
   - Verify database credentials in `config/dbconn.php`
   - Ensure `maybank` database exists

2. **"File upload failed"**
   - Check `assets/uploads/` directory permissions
   - Verify PHP `upload_max_filesize` and `post_max_size` settings

3. **"Composer dependencies missing"**
   - Run `composer install` in project root
   - Ensure Composer is installed globally

4. **"Session expired quickly"**
   - Check session timeout settings in login files
   - Default timeout is 30 minutes (1800 seconds)

## 🙋‍♂️ Author

**Albert Ardiansyah**  
📫 [@TukangBakmi on GitHub](https://github.com/TukangBakmi)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📌 Changelog

- **v1.0** - Initial release with core functionality
- File upload/download tracking
- User and admin management
- Download logging and statistics

## 📃 License

This project is intended for educational and research purposes. For any use beyond that, please contact the author.
