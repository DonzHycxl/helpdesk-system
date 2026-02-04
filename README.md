# Helpdesk System

A robust, Filament-powered Helpdesk Ticketing System built with Laravel. This application allows organizations to manage support tickets, departments, priorities, and statuses efficiently, complete with PDF reporting and dashboard analytics.

## Features

-   **Ticket Management**: Create, view, update, and manage support tickets.
-   **Department Management**: Organize tickets by departments.
-   **Status & Priority Tracking**: Customizable statuses and priorities.
-   **User Management**: Admin and Staff roles with separate permissions.
-   **Dashboard Analytics**: Visual charts for:
    -   Tickets by Department
    -   Tickets by Month
    -   Tickets by Priority vs. Status
-   **PDF Exports**: Generate reports for tickets filtered by department or status.
-   **Responses**: Track communication within tickets.

## Tech Stack

-   **Framework**: [Laravel 12](https://laravel.com)
-   **Admin Panel**: [Filament 4](https://filamentphp.com)
-   **Frontend**: [Tailwind CSS 4](https://tailwindcss.com), [Livewire](https://livewire.laravel.com)
-   **Database**: MySQL (Default), SQLite/PostgreSQL compatible
-   **PDF Generation**: `barryvdh/laravel-dompdf`

## Prerequisites

Ensure you have the following installed on your local machine:

-   [XAMPP](https://www.apachefriends.org/) (includes PHP 8.2+ and MySQL)
-   [Composer](https://getcomposer.org/)
-   [Node.js](https://nodejs.org/) & NPM

## Installation

1.  **Clone the repository**
    ```bash
    git clone https://github.com/DniHaikaru/helpdesk-system.git
    cd helpdesk-system
    ```

2.  **Install Dependencies**
    ```bash
    composer run setup
    ```
    *This command installs PHP and Node.js dependencies, copies the environment file, and generates the application key.*

3.  **Configure Environment**
    Open the `.env` file and configure your database credentials.
    
    **For XAMPP Users:**
    -   **Database:** Create a new database named `helpdesk_system` via [phpMyAdmin](http://localhost/phpmyadmin).
    -   **Credentials:** Default XAMPP settings are usually `root` with no password.

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=helpdesk_system
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Run Migrations & Seeders**
    ```bash
    php artisan migrate --seed
    ```

## Usage

1.  **Start the development server**
    ```bash
    composer run dev
    ```
    Or run servers individually:
    ```bash
    php artisan serve
    npm run dev
    php artisan queue:work
    ```

2.  **Access the application**
    Open your browser and navigate to `http://localhost:8000`.

3.  **Login with default credentials**
    The database seeder creates the following users (password: `password`):

    | Role  | Email               | Password |
    | :---- | :------------------ | :------- |
    | Admin | `admin@example.com` | `password` |
    | Staff | `staff@example.com` | `password` |

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
