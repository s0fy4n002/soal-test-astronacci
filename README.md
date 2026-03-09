## Prerequisites
PHP 8.2+, Composer, Node.js, and npm installed.

## Installation Steps

### 1. Install Dependencies

Backend:
cd backend
composer install

Frontend:
cd frontend
npm install

### 2. Configure Environment

Go to the backend folder:

cd backend

Copy the environment file:

cp .env.example .env

Set the database connection to SQLite in the `.env` file:

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

Create the SQLite database file:

touch database/database.sqlite

Generate the application key:

php artisan key:generate

### 3. Run Database Migrations

php artisan migrate

### 4. Run the Application

Start the backend:

cd backend
php artisan serve

Start the frontend:

cd frontend
npm run dev