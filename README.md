# Module 5: Reports & Sessions

## Setup

1. Create database `peaceconnect_reports` in phpMyAdmin
2. Import `database/schema.sql`
3. Update `config.php` with your database credentials
4. Update `ai_config.php` with your xAI API key
5. Access via `http://localhost/Git/5_Reports_Sessions/`

## Structure

```
├── config.php              # Database configuration
├── ai_config.php           # AI API configuration
├── index.php               # Entry point
├── Controller/
│   ├── reportController.php
│   ├── sessionController.php
│   ├── mediatorController.php
│   └── aiController.php
├── Model/
│   ├── Report.php
│   ├── MediationSession.php
│   ├── Mediator.php
│   └── AIFlag.php
├── View/
│   ├── frontoffice/        # Public pages
│   ├── backoffice/         # Admin pages
│   └── assets/             # CSS, JS, images
└── database/
    └── schema.sql          # Database tables
```

## Features

- Submit discrimination reports
- AI analysis of reports
- Assign mediators
- Schedule mediation sessions
- Track report status
- Manage mediators (admin)
- Manage sessions (admin)

