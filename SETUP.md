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

```env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=volcanic
DB_USERNAME=root
DB_PASSWORD=
```

U can adjust the `DB_USERNAME` and `DB_PASSWORD` according to your local MySQL setup, but I normally leave it as root and password empty (Manish) because we are not hosting it and just running locally.

## Gemini Key for Configuration in .env

FIrst of all, run this command in the terminal: npm install @google/genai. (I did it and it modified the package.json and the package-lock.json files, but I think you should do it as well)

Then, search on Google https://aistudio.google.com/app/api-keys. In this page there will be a button with `Create API Key`. Click it, create a key (the name doesn't matter) and copy it.

After doing this, go in the `.env` file, not the example one, your personal, and add that key: GEMINI_API_KEY=your_key_name. I put it at the end of the page, but it shouldn't matter.

Now you should be good to go.

## Ambee API Key for Configuration in `.env`

To enable the Ambee Natural Disasters API in the project, you need to obtain an API key from Ambee and expose it to Laravel via your `.env` file.

1. **Create or log into your Ambee account**

   - Go to the Ambee dashboard: https://api-dashboard.getambee.com  
   - Register for a new account or sign in with an existing one.
   - Verify your email address.

2. **Locate your Ambee API key**

   - Once logged in, navigate to the **Dashboard** section and you will see the **API key** section with your key.
   - Copy the API key value (a long alphanumeric string).

3. **Add the key to your local `.env` file**

   Open `.env` and add the following line (the position in the file does not matter):

   ```env
   AMBEE_API_KEY=your_ambee_api_key_here
