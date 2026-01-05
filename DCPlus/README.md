# DCPlus - Streaming Platform

A Laravel-based streaming platform application with user profiles, watchlists, ratings, and admin management features.

## Quick Start CLI Commands

Copy and paste these commands in order to set up the project:

```bash
# Navigate to project directory
cd DCPlus

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database file (if using SQLite)
touch database/database.sqlite

# Run database migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed

# Install frontend dependencies
npm install

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

**Or use the quick setup script:**
```bash
composer setup
```

**For development with hot reload:**
```bash
# Terminal 1: Start Laravel server and Vite dev server
composer dev

# Or separately:
php artisan serve    # Terminal 1
npm run dev          # Terminal 2
```

**Create admin user:**
```bash
php artisan user:make-admin your-email@example.com
```

## Requirements

Before you begin, ensure you have the following installed on your system:

- **PHP** >= 8.2
- **Composer** (PHP dependency manager)
- **Node.js** >= 18.x and **npm** (for frontend assets)
- **Database**: SQLite (default), MySQL, PostgreSQL, or MariaDB

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd DisneyTP/DCPlus
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the environment example file and configure your settings:

```bash
cp .env.example .env
```

Edit the `.env` file with your configuration:

```env
APP_NAME=DCPlus
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (SQLite is default)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# For MySQL/PostgreSQL, uncomment and configure:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=dcplus
# DB_USERNAME=root
# DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Database Setup

#### For SQLite (Default):

Create the database file:

```bash
touch database/database.sqlite
```

#### For MySQL/PostgreSQL:

Create a database named `dcplus` (or your preferred name) and update the `.env` file accordingly.

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Seed the Database (Optional)

Seed the database with sample movies and series:

```bash
php artisan db:seed
```

This will create:
- A test user (`test@example.com`)
- Sample movies and series data

### 8. Install Frontend Dependencies

```bash
npm install
```

### 9. Build Frontend Assets

For production:

```bash
npm run build
```

For development (with hot reload):

```bash
npm run dev
```

## Running the Application

### Development Server

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Using Composer Scripts

The project includes convenient composer scripts:

**Quick setup** (installs dependencies, generates key, runs migrations, builds assets):
```bash
composer setup
```

**Development mode** (runs server, queue, logs, and Vite concurrently):
```bash
composer dev
```

**Run tests**:
```bash
composer test
```

## Creating an Admin User

After creating a user account through the registration page, you can promote them to administrator using the artisan command:

```bash
php artisan user:make-admin {email}
```

Example:
```bash
php artisan user:make-admin test@example.com
```

## Default Test Account

After running the seeder, you can use:
- **Email**: `test@example.com`
- **Password**: Check the `UserFactory` or create a new password via password reset

## Project Structure

```
DCPlus/
├── app/
│   ├── Console/Commands/     # Artisan commands (e.g., MakeUserAdmin)
│   ├── Http/
│   │   ├── Controllers/       # Application controllers
│   │   │   ├── Admin/        # Admin controllers
│   │   │   └── Auth/         # Authentication controllers
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form request validation
│   └── Models/               # Eloquent models
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── views/                # Blade templates
│   ├── css/                  # Stylesheets
│   └── js/                   # JavaScript files
├── routes/
│   ├── web.php               # Web routes
│   └── auth.php              # Authentication routes
└── public/                   # Public assets
```

## Key Features

- **User Authentication**: Registration, login, password reset
- **User Profiles**: Multiple profiles per user account
- **Content Management**: Movies and series catalog
- **Watchlists**: Save content for later viewing
- **Ratings & Reviews**: Rate and review content
- **Admin Panel**: Manage movies, series, and content
- **Watch History**: Track viewing history

## Development Commands

### Artisan Commands

```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Run seeders
php artisan db:seed

# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear
```

### Frontend Commands

```bash
# Development server with hot reload
npm run dev

# Build for production
npm run build
```

## Testing

Run the test suite:

```bash
php artisan test
```

Or using composer:

```bash
composer test
```

## Troubleshooting

### Permission Issues

If you encounter permission issues on Linux/Mac:

```bash
chmod -R 775 storage bootstrap/cache
```

### Database Connection Issues

- Ensure your database file exists (for SQLite): `touch database/database.sqlite`
- Verify your `.env` file has correct database credentials
- Check that the database server is running (for MySQL/PostgreSQL)

#### SQLite "could not find driver" Error (Windows)

If you encounter the error `could not find driver (Connection: sqlite)`, the PHP SQLite extension is not enabled. To fix this:

1. **Find your php.ini file location:**
   ```powershell
   php --ini
   ```
   This will show the path to your `php.ini` file (e.g., `C:\php\php.ini`)

2. **Open the php.ini file** in a text editor (you may need administrator privileges)

3. **Find and uncomment** (remove the semicolon `;` from) these lines:
   ```ini
   extension=pdo_sqlite
   extension=sqlite3
   ```
   
   If the lines don't exist, add them to the extension section of your php.ini file.

4. **Save the file** and restart your terminal/PowerShell window

5. **Verify the extension is loaded:**
   ```powershell
   php -m | findstr sqlite
   ```
   You should see `pdo_sqlite` and `sqlite3` in the output.

6. **Try running migrations again:**
   ```powershell
   php artisan migrate
   ```

**Alternative:** If you prefer not to modify php.ini, you can switch to MySQL or PostgreSQL by updating your `.env` file with the appropriate database credentials.

### Frontend Assets Not Loading

- Ensure you've run `npm install`
- Run `npm run build` for production or `npm run dev` for development
- Clear the view cache: `php artisan view:clear`

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
