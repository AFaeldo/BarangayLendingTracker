# Barangay Lending Tracker

## Project Overview

**Barangay Lending Tracker** is a web-based application built with **Laravel** designed to manage the lending of barangay property and equipment to residents. It streamlines the process of tracking borrowers, items, and transactions (borrowing/returning), ensuring efficient inventory management and accountability.

### Key Technologies
*   **Backend:** PHP (Laravel Framework)
*   **Frontend:** Blade Templates, JavaScript, Tailwind CSS (via Vite)
*   **Database:** Relational Database (MySQL/SQLite compatible)
*   **Build Tools:** Composer (PHP), NPM/Vite (Assets)

## Architecture & Structure

The project follows the standard Laravel MVC (Model-View-Controller) architecture.

### Directory Highlights
*   `app/Http/Controllers`: Contains the application logic.
    *   `BorrowingController.php`: Handles borrowing and returning logic, including stock adjustments.
    *   `ResidentController.php`: Manages resident records.
    *   `ItemController.php`: Manages inventory items.
*   `app/Models`: Eloquent ORM models (`Borrowing`, `Item`, `Resident`, `User`).
*   `database/migrations`: Database schema definitions.
*   `resources/views`: UI templates.
    *   `LendingTracker/`: Main application views (Dashboard, Borrowing, Residents, Items).
    *   `Account/`: Authentication views (Login, Register, Password Reset).
*   `routes/web.php`: Defines all web routes, including authentication and protected resource routes.

## Building and Running

### Prerequisites
*   PHP >= 8.2
*   Composer
*   Node.js & NPM

### Setup Steps
1.  **Install PHP Dependencies:**
    ```bash
    composer install
    ```
2.  **Environment Setup:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
3.  **Database Setup:**
    Configure your database credentials in `.env`, then run migrations:
    ```bash
    php artisan migrate
    ```
    *(Optional) Seed the database:*
    ```bash
    php artisan db:seed
    ```
4.  **Install Frontend Dependencies:**
    ```bash
    npm install
    ```

### Running the Application
To run the application in a local development environment, you need to run both the Laravel server and the Vite development server.

1.  **Start Laravel Server:**
    ```bash
    php artisan serve
    ```
2.  **Start Vite Server (Assets):**
    ```bash
    npm run dev
    ```

The application will typically be accessible at `http://localhost:8000`.

### Building for Production
To compile frontend assets for production:
```bash
npm run build
```

## Development Conventions

*   **Routing:** All web routes are defined in `routes/web.php`. Protected routes are grouped under the `auth` middleware.
*   **Authentication:** Custom authentication implementation (Login, Register, Password Reset) using Laravel Facades (`Auth`, `Hash`, `Mail`).
*   **Business Logic:** Complex logic (like stock reduction during borrowing) is handled within Controllers, often wrapped in `DB::transaction` to ensure data integrity.
*   **Styling:** Utility-first CSS using Tailwind CSS is expected.
*   **Validation:** Input validation is performed directly in controller methods using `$request->validate()`.

## Key Workflows

### Borrowing Process
1.  **Creation:** When a borrowing record is created (`store`), the system checks item availability. If sufficient stock exists, it creates the record and decrements the `available_quantity` of the item.
2.  **Return/Lost:** When an item is marked returned or lost:
    *   If **Returned**: The `available_quantity` of the item is incremented.
    *   If **Lost**: The stock is **not** replenished.
    *   Status is updated to 'Returned' or 'Lost'.
