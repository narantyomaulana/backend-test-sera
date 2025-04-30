# User Address API - Laravel 11 with Vertical Slice Architecture

This project demonstrates a Laravel 11 application implementing User and Address models with a one-to-many relationship, following the Vertical Slice Architecture pattern. The application is dockerized and includes comprehensive unit tests and Swagger API documentation.

## Features

- User CRUD operations (Create, Read, Update, Delete)
- Address management (Create, List, View) with one-to-many relationship to Users
- Implementation of Vertical Slice Architecture
- Docker support via Laravel Sail
- Unit tests for all features
- Swagger API documentation

## Setup Instructions

### Prerequisites

- Docker and Docker Compose
- Git

### Installation

1. Clone the repository:
   ```bash
      git clone <repository-url>
   ```
2. Copy environment file:

   ```bash
      cp .env.example .
   ```

3. Install PHP Dependencies:

   ```bash
      docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v "$(pwd):/var/www/html" \
      -w /var/www/html \
      laravelsail/php82-composer:latest \
      composer install --ignore-platform-reqs

      # Option 2 - Using Local Composer (if installed):

      composer install
   ```

4. Start Laravel Sail:

   ```bash
   ./vendor/bin/sail up -d
   ```

5. Initialize application:

```bash
# Generate app key

./vendor/bin/sail artisan key:generate

# Run migrations

./vendor/bin/sail artisan migrate

# Generate API documentation

./vendor/bin/sail artisan l5-swagger:generate

```

6. Verify Installation:

```bash
# Run tests
./vendor/bin/sail test

# Check API endpoint
curl http://localhost:8080/api/documentation
```
