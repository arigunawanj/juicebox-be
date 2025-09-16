# Laravel API Project - Code Test Juicebox

A comprehensive RESTful API built with Laravel 12, featuring authentication, CRUD operations, weather integration, and background job processing.

## Features

- **Authentication**: User registration, login, and logout using Laravel Sanctum
- **Posts Management**: Full CRUD operations for posts with user ownership
- **User Management**: User profile retrieval with posts relationship
- **Weather API**: Real-time weather data for Perth, Australia with caching
- **Background Jobs**: Automated weather updates and welcome email dispatch
- **Comprehensive Testing**: Full test coverage for all endpoints
- **Custom Response Format**: Consistent API responses using custom macros

## API Endpoints

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires authentication)
- `GET /api/user` - Get current authenticated user (requires authentication)

### Posts
- `GET /api/posts` - List all posts (paginated)
- `GET /api/posts/{id}` - Get specific post
- `POST /api/posts` - Create new post (requires authentication)
- `PATCH /api/posts/{id}` - Update post (requires authentication, owner only)
- `DELETE /api/posts/{id}` - Delete post (requires authentication, owner only)

### Users
- `GET /api/users/{id}` - Get specific user with posts (requires authentication)

### Weather
- `GET /api/weather` - Get current weather data for Perth, Australia

## Setup Instructions

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL
- Node.js and NPM (for frontend assets)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd juicebox-be
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=juicebox_api
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Weather API setup**
   Get a free API key from [OpenWeatherMap](https://openweathermap.org/api):
   - Go to https://openweathermap.org/api
   - Sign up for a free account
   - Go to "My API Keys" section
   - Copy your API key
   - Add it to your `.env` file:
   ```env
   OPENWEATHER_API_KEY=your_actual_api_key_here
   ```
   
   **Note**: Make sure to use a valid API key. The default key in `.env` is just a placeholder and will not work.

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

### Queue Setup

The application uses background jobs for weather updates and welcome emails. To process these jobs:

1. **Start the queue worker**
   ```bash
   php artisan queue:work
   ```

2. **For weather updates (scheduled task)**
   Add this to your crontab to run every minute:
   ```bash
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

### Testing

Run the test suite:
```bash
php artisan test
```

### Manual Job Testing

To manually dispatch a welcome email job:
```bash
php artisan email:send-welcome {user_id}
```

## API Response Format

All API responses follow a consistent format:

### Success Response
```json
{
  "status_code": 200,
  "data": {...},
  "message": "Success message",
  "settings": {}
}
```

### Error Response
```json
{
  "status_code": 422,
  "errors": ["Error message 1", "Error message 2"],
  "settings": {}
}
```

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - User's email (unique)
- `password` - Hashed password
- `created_at` - Timestamp
- `updated_at` - Timestamp

### Posts Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `title` - Post title
- `content` - Post content
- `created_at` - Timestamp
- `updated_at` - Timestamp

## Security Features

- Password hashing using Laravel's built-in hashing
- API token authentication via Laravel Sanctum
- Input validation on all endpoints
- Authorization checks for resource ownership
- Rate limiting and CORS protection

## Performance Optimizations

- Database query optimization with eager loading
- Response caching for Perth weather data (15 minutes)
- Pagination for list endpoints
- Background job processing for heavy operations

## Testing Coverage

The project includes comprehensive tests covering:
- Authentication flows
- CRUD operations for posts
- User management
- Weather API integration with mocking
- Error handling scenarios
- Authorization checks
