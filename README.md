# Module 2: Forum

## Setup

1. Create database `peaceconnect_forum` in phpMyAdmin
2. Import `database/schema.sql`
3. Update `config.php` with your database credentials
4. Access via `http://localhost/Git/2_Forum/`

## Structure

```
├── config.php              # Database configuration
├── index.php               # Entry point
├── Controller/
│   └── publicationController.php
├── Model/
│   └── Publication.php
├── View/
│   ├── frontoffice/        # Public pages
│   ├── backoffice/         # Admin pages
│   └── assets/             # CSS, JS, images
└── database/
    └── schema.sql          # Database tables
```

## Features

- Create forum posts
- View discussions
- Like posts
- Translate content
- Text-to-speech
- Moderate posts (admin)

