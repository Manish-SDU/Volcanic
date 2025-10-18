# Volcanic Laravel Project Setup 

## Database Configuration

This project uses **MySQL** as the database. Follow these steps to set up your local environment:

### 1. Environment Setup

```bash
# Copy the example environment file
cp .env.example .env

# Install dependencies
composer install

# Generate application key
php artisan key:generate
```

### 2. Database Setup

Make sure you have MySQL running locally, then or have XAMPP running.

```bash
# Create the database (you can do this in phpMyAdmin or MySQL command line)
CREATE DATABASE volcanic; # mysql -u root -e "CREATE DATABASE volcanic;" #  to run in a MySQL environment
```

### 3. Run Migrations and Seeders

```bash

# Run migrations to create tables
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### 4. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

## Database Configuration in .env

Make sure your `.env` file has these database settings:
```

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=volcanic
DB_USERNAME=root
DB_PASSWORD=
```

U can adjust the `DB_USERNAME` and `DB_PASSWORD` according to your local MySQL setup, but I normally leave it as root and password empty (Manish) because we are not hosting it and just running locally.
