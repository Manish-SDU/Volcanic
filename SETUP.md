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

Make sure MySQL (or XAMPP) is running locally.

```bash
# Create the database (phpMyAdmin or MySQL CLI)
CREATE DATABASE volcanic;
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

## .env Configuration

### Database

Ensure your `.env` contains the database settings below (adjust for your local MySQL user/password):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=volcanic
DB_USERNAME=root
DB_PASSWORD=
```

### API Keys (Chatbot + Real-Time Activity)

Install the Gemini SDK:

```bash
npm install @google/genai
```

Create your API keys and add them to `.env` (location in the file does not matter):

- Gemini: https://aistudio.google.com/app/api-keys
- Ambee: https://api-dashboard.getambee.com

```env
GEMINI_API_KEY=your_gemini_api_key_here
AMBEE_API_KEY=your_ambee_api_key_here
```
