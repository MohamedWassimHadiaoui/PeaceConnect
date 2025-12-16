# 🕊️ PeaceConnect
peaceconnect.org/

**A Conflict Resolution & Mediation Platform**

[![GitHub](https://img.shields.io/badge/GitHub-Repository-181717?style=flat&logo=github)](https://github.com/MohamedWassimHadiaoui/PeaceConnect)
![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

PeaceConnect is a comprehensive web-based conflict resolution and mediation platform. It provides a safe space for users to report conflicts, request help, connect with mediators, participate in community forums, and access educational resources.

🔗 **GitHub Repository**: [https://github.com/MohamedWassimHadiaoui/PeaceConnect](https://github.com/MohamedWassimHadiaoui/PeaceConnect)

---

## 📑 Table of Contents

- [Features](#-features)
- [Installation](#-installation)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [Technologies Used](#-technologies-used)
- [Contributions](#-contributions)
- [License](#-license)

---

## ✨ Features

### 👥 User Management
- Registration with validation (Math CAPTCHA)
- Secure authentication with bcrypt hashing
- Two-Factor Authentication (2FA) via TOTP
- Password reset via email
- Profile management with avatar upload

### 📝 Conflict Reporting
- Create incident reports (harassment, discrimination, etc.)
- Track report status
- File attachments (images, PDF)
- Priority levels (low, medium, high)

### 🤖 Artificial Intelligence
- **AI Report Analysis** - Automatic detection of violence, urgency, harassment
- **Peace Chatbot** - Intelligent assistant for navigation and emotional support
- **AI Forum Moderation** - Automatic analysis of inappropriate content

### 🤝 Mediation System
- Mediator management (profiles, expertise, availability)
- Mediation session scheduling (online/in-person)
- Session tracking and notes

### 💬 Community Forum
- Post publications with categories and tags
- Likes and comments system
- Moderation with approval/rejection

### 📅 Events
- Create community events
- Event registration
- Types: workshops, seminars, online, offline

### 📚 Educational Resources
- Educational articles and content
- Likes and views system

### 🏢 Organizations Directory
- Partners: NGOs, legal aid, mental health, etc.
- Search by category and city

---

## 🔧 Installation

### Prerequisites

Before you begin, make sure you have installed:

- **PHP 7.4+** 
- **MySQL** 
- **XAMPP** or **WAMP** (recommended for Windows)

### PHP Installation

1. Download PHP from the official website: [PHP - Downloads](https://www.php.net/downloads.php)

2. Install PHP according to your operating system:

   - **Windows**: Use [XAMPP](https://www.apachefriends.org/index.html) or [WampServer](http://www.wampserver.com/)
   
   - **macOS**: Use [Homebrew](https://brew.sh/), then run:
     ```bash
     brew install php
     ```
   
   - **Linux** (Ubuntu):
     ```bash
     sudo apt update
     sudo apt install php php-mysql php-mbstring
     ```

3. Verify the installation:
   ```bash
   php -v
   ```

### Clone the Repository

```bash
git clone https://github.com/MohamedWassimHadiaoui/PeaceConnect.git
cd PeaceConnect
```

### Configuration with XAMPP/WAMP

1. Place the project in the `htdocs` folder (XAMPP) or `www` folder (WAMP)

2. Start Apache and MySQL from the XAMPP/WAMP interface

3. Create the database:
   - Access [phpMyAdmin](http://localhost/phpmyadmin)
   - Create a database named `peaceconnect`
   - Import the `database/full_schema.sql` file
   - (Optional) Import `database/sample_content.sql` for test data

4. Configure the database connection in `config.php`:
   ```php
   $servername = "localhost";
   $username   = "root";
   $password   = "";
   $dbname     = "peaceconnect";
   ```

5. Configure email settings in `mail_config.php` (for password reset)

6. (Optional) Configure xAI API in `ai_config.php` for AI features

7. Access the project via [http://localhost/peaceconnect](http://localhost/peaceconnect)

---

## 🚀 Usage

### Public Access (without login)
- View approved forum posts
- See approved events
- Access published resources
- Browse organizations directory
- Use the Peace chatbot

### User Access (with login)
- Submit conflict reports
- Request help/support
- Create forum posts
- Like and comment
- Register for events
- Manage profile and enable 2FA

### Administrator Access
- Dashboard with statistics
- Full CRUD management for all entities
- Content moderation
- Assign mediators to reports
- Schedule mediation sessions

---

## 📁 Project Structure

```
peaceconnect/
├── Controller/           # PHP controllers (business logic)
│   ├── userController.php
│   ├── reportController.php
│   ├── publicationController.php
│   ├── eventController.php
│   ├── aiController.php
│   └── ...
├── Model/                # PHP model classes
│   ├── User.php
│   ├── Report.php
│   ├── Publication.php
│   └── ...
├── View/                 # Frontend views
│   ├── frontoffice/      # Public user interface
│   ├── backoffice/       # Admin dashboard
│   ├── partials/         # Reusable components
│   └── assets/           # CSS, JS, images
├── lib/                  # External libraries
│   ├── PHPMailer/
│   └── TwoFactorAuth.php
├── api/                  # API endpoints (chatbot)
├── database/             # SQL schema and sample data
├── uploads/              # User uploaded files
├── config.php            # Database configuration
├── ai_config.php         # xAI API configuration
└── mail_config.php       # SMTP configuration
```

---

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+** - Server-side language
- **MySQL** - Database (PDO with prepared statements)
- **MVC Architecture** - Model-View-Controller
- **PHPMailer** - Email sending

### Frontend
- **HTML5 / CSS3** - Structure and styles
- **JavaScript** (Vanilla) - Interactivity
- **Responsive Design** - Mobile compatible
- **Dark/Light Theme** - Theme switching support

### Security
- Bcrypt password hashing
- CAPTCHA protection
- 2FA Authentication (TOTP)
- Server-side validation
- Prepared statements (SQL injection prevention)
- htmlspecialchars (XSS prevention)

### API Integrations
- **xAI Grok API** - Chatbot, AI moderation, report analysis

---

## 🤝 Contributions

We thank everyone who contributed to this project!

### Contributors

The following people contributed to this project:

| Contributor | Module | Description |
|-------------|--------|-------------|
| [@MohamedWassimHadiaoui](https://github.com/MohamedWassimHadiaoui) | **Reports** | Conflict reporting system, AI analysis, mediation |
| [@mohamedhabiblamouchi](https://github.com/mohamedhabiblamouchi) | **User** | User management, authentication, 2FA, profiles |
| [@Tasnim1919](https://github.com/Tasnim1919) | **Events** | Community events, registrations, management |
| [@99-ui](https://github.com/99-ui) | **Forum** | Publications, comments, likes, AI moderation |
| [@sarraboulifi](https://github.com/sarraboulifi) | **Organisation & Help** | Organizations directory, help requests |

### How to Contribute?

1. **Fork the project**: Go to the GitHub project page and click the **Fork** button in the top right corner

2. **Clone your fork**:
   ```bash
   git clone https://github.com/your-username/PeaceConnect.git
   cd PeaceConnect
   ```

3. **Create a branch** for your feature:
   ```bash
   git checkout -b feature/my-new-feature
   ```

4. **Commit your changes**:
   ```bash
   git add .
   git commit -m "Add my new feature"
   ```

5. **Push to your fork**:
   ```bash
   git push origin feature/my-new-feature
   ```

6. **Create a Pull Request** on the main repository

---

## 📄 License

This project is licensed under the **MIT License**. For more details, see the [LICENSE](./LICENSE) file.

### MIT License Details

The MIT License is a permissive software license that allows:
- ✅ Commercial use
- ✅ Modification
- ✅ Distribution
- ✅ Private use

With the only condition being to retain the copyright notice.

---

## 📞 Contact

For any questions or suggestions, feel free to:
- Open an **[Issue](https://github.com/MohamedWassimHadiaoui/PeaceConnect/issues)** on GitHub
- Submit a **[Pull Request](https://github.com/MohamedWassimHadiaoui/PeaceConnect/pulls)**
- Contact the team through the platform forum

---

<p align="center">
  <strong>🕊️ PeaceConnect - For a more peaceful world</strong>
</p>
