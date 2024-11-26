# News Aggregator API # 

## Overview

The News Aggregator API is a Laravel-based application that fetches and consolidates news articles from multiple external APIs. It provides endpoints for:
- User authentication and preference management.
- Fetching personalized news feeds.
- Filtering articles by keyword, source, category, and more.

This API is built to demonstrate best practices in RESTful API design, efficient data storage, and personalized content delivery.

## Prerequisites

Ensure the following are installed on your system:
- Docker and Docker Compose
- PHP 8.2 or higher (for local testing)
- Composer

## Setup Instructions

#### Clone the repository using git bash
  ```
  git clone ssh://git@ssh.github.com:443/pavankm327/news-aggregator-system.git
  cd news-aggregator-api
  ```
#### Copy the .env.example file and configure it:
  ```
  cp .env.example .env
  ```
Update the following environment variables in .env:

- `MAIL_USERNAME=YOUR-EMAIL-ID`
- `DB_PASSWORD=YOUR-APP-PASSWORD`

Also, add API keys for external news APIs:

- `NEWS_API_KEY=YOUR_NEWS_API_KEY`
- `GUARDIAN_API_KEY=YOUR_GUARDIAN_API_KEY`
- `NYT_API_KEY=YOUR_NYT_API_KEY`

#### Start the Docker environment:
```
docker-compose up --build
```
#### Run the seeders (Optional)
```
docker exec -it news-aggregator-system-app-1 php artisan db:seed
```

#### Access the application
```
API Base URL:  http://localhost:8000/api
Swagger Documentation: - http://localhost:8000/api/documentation
```

#### Stop the Docker environment:
```
docker-compose down
```

### Additional Notes
#### Architecture
- **Framework**: Laravel 11
- **Database**: MySQL
- **Caching**: Redis (Docker) & database (xampp)
- **API Documentation**: Swagger (via L5-Swagger)
- **Scheduler**: Laravel Task Scheduling with Supervisord
- **External APIs**: NewsAPI, The Guardian API, New York Times API
- **Testing**: NOT COVERED

### Implementation Highlights

- 1. **Personalized News Feed**:
   - Users can set preferences for sources, categories, and authors.
   - Personalized feeds are generated using stored preferences.

- 2. **Data Aggregation**:
   - Articles are fetched from external APIs at regular intervals using a Laravel service.
   - Duplicate articles are avoided using the `updateOrCreate` method.

- 3. **Efficient Querying**:
   - Articles are filtered locally based on user inputs and preferences.
   - Query caching is implemented for frequently accessed filters.

- 4. **Robust Error Handling**:
   - User inputs are validated using Laravel's validation rules.
   - All endpoints are protected using Laravel Sanctum for token-based authentication.

- 5. **Deployment**:
   - Dockerized setup ensures a consistent development and production environment.
