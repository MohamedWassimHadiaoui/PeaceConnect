# PeaceConnect

## Project Description

**PeaceConnect** is a Web Technologies Integrated Project. It is a web platform that helps people in need connect with organizations that can provide support. The platform makes it easier to ask for help and receive assistance in an organized and efficient way.

**Objective:**
The main objective of PeaceConnect is to create a simple and centralized system where users can submit help requests and organizations can respond quickly. The goal is to reduce delays and make the help process more effective.

**Problem Solved:**
Many people use phone calls or social media to ask for help, but these methods are often unorganized and difficult to follow. PeaceConnect solves this problem by offering a structured platform where requests and organizations are clearly managed. This improves communication and ensures better use of resources.

**Relation to Social Impact and Peace:**
PeaceConnect supports social stability and peace by helping communities support each other. By connecting people with the right organizations, the platform encourages solidarity and helps reduce social difficulties.

### Main Features (Focus Modules)

This project includes several modules. The main focus is on the following ones:

1.  **Help Requests (Demande d'aide)**:
    *   **Role:** This module acts as the "voice" of the user in need. It allows individuals to formally submit a request for assistance.
    *   **Purpose:** To capture critical details such as the type of help needed (medical, food, shelter), urgency level, specific location (via integrated maps), and current situation.
    *   **Contribution to Platform:** Starts the support process and allows tracking of request status (pending, in progress, resolved).

2.  **Organizations**:
    *   **Role:** Displays a list of organizations that can provide help.
    *   **Purpose:** Stores information such as organization name, contact details, and areas of activity.
    *   **Contribution to Platform:** Helps users find reliable organizations and improves coordination between people and aid providers.

## Table of Contents

*   Project Description
*   Main Features
*   Installation
*   Usage
*   Contribution
*   License

## Installation

Follow these steps to set up the project locally for development and testing.

### Prerequisites
*   **XAMPP** 
*   **Git** for version control.
*   **Composer** (optional, if external PHP packages are added later).

### Steps

1.  **Clone the Repository**
    Open your terminal and run:
    ```bash
    git clone https://github.com/your-username/PeaceConnect.git
    ```

2.  **Setup Directory**
    Move the project folder to your server's root directory (e.g., `htdocs` in XAMPP).
    *   Path: `C:\xampp\htdocs\Demande_aide` .

3.  **Database Configuration**
    *   Start **Apache** and **MySQL** from the XAMPP Control Panel.
    *   Open phpMyAdmin (`http://localhost/phpmyadmin`).
    *   Create a new database named `peaceconnect`.
    *   Import the SQL file located at `database/peaceconnect_complete.sql` to set up the tables and seed data.
    *   *(Optional)* If the database usually creates itself, ensure your `Model/db.php` credentials match your local setup:
        *   Host: `localhost`
        *   User: `Projet2A` 
        *   Password: `123` 

4.  **Run the Application**
    Open your browser and navigate to:
    ```
    http://localhost/Demande_aide/index.php
    ```

## Usage

PeaceConnect is designed for two main types of users: **General Users** (Beneficiaries) and **Administrators** (Organizations/Platform Managers).

### For Users
*   **Submitting a Help Request:**
    *   Navigate to the "Demande d'aide" section.
    *   Fill out the form specifying the type of emergency and your situation.
    *   Use the integrated map to pin your exact location.
    *   Submit the form to alert registered organizations.
*   **Finding Organizations:**
    *   Browse the "Organizations" list to find NGOs that specialize in your specific need (e.g., "Medical Aid", "Food Supply").
    *   Use filters to search by city or category.

### For Organizations / Administrators (Back Office)
*   **Dashboard Access:**
    *   Log in to the Back Office via `index.php?action=login`.
*   **Managing Requests:**
    *   View a list of all incoming help requests.
    *   Filter by urgency (Low, Medium, High) or status.
    *   Update the status of a request (e.g., mark as "Resolved" when aid is delivered).
*   **Managing Organizations:**
    *   Add new organizations to the directory.
    *   Edit details of existing organizations to keep contact info up to date.
    *   Visualize organization distribution on the map.

## Contribution

We welcome contributions to improve PeaceConnect!

1.  **Fork the repository.**
2.  **Create a feature branch:** `git checkout -b feature/NewFeature`
3.  **Commit your changes:** `git commit -m 'Add specific feature'`
4.  **Push to the branch:** `git push origin feature/NewFeature`
5.  **Open a Pull Request** describing your changes.

Please ensure your code follows the project's MVC structure and coding standards.

## License

This project is developed for academic purposes as part of a Web Technologies Integrated Project.

It is distributed under the MIT License.
