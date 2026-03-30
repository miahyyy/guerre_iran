# Project Structure - Guerre en Iran

## Overview
Complete separation of Front Office (FO) and Back Office (BO) with independent containers, configurations, and includes folders while sharing a single database.

## Directory Structure

```
guerre_iran/
├── front/                          # Front Office (Public Website)
│   ├── index.php                   # Main homepage
│   ├── article.php                 # Article display page
│   ├── robots.txt                  # SEO robots file
│   ├── sitemap.xml                 # SEO sitemap
│   ├── css/                        # Frontend stylesheets
│   ├── images/                     # Frontend images
│   ├── js/                         # Frontend JavaScript
│   ├── .htaccess                   # FO URL rewriting
│   └── includes/
│       ├── config.php              # FO-specific configuration
│       ├── database.php            # FO read-only database methods
│       └── fonctions.php           # FO utility functions
│
├── admin/                          # Back Office (Admin Interface)
│   ├── login.php                   # Admin login page
│   ├── dashboard.php               # Admin dashboard
│   ├── articles/                   # Article management
│   │   ├── liste.php               # Articles list
│   │   ├── ajouter.php             # Add article form
│   │   └── modifier.php            # Edit article form
│   └── includes/
│       ├── config.php              # BO-specific configuration
│       ├── database.php            # BO full CRUD database methods
│       └── fonctions.php           # BO admin functions
│
├── docker/                         # Docker configurations
│   ├── Dockerfile-FO               # Front Office container build
│   ├── Dockerfile-BO               # Back Office container build
│   ├── vhost-fo.conf               # FO Apache configuration
│   └── vhost-bo.conf               # BO Apache configuration
│
├── docker-compose.yml              # Multi-container orchestration
├── schema.sql                      # Database schema
└── create_admin.php                # Password hashing utility
```

## Container Configuration

### Front Office Container (web-fo)
- **Port**: 8090
- **DocumentRoot**: /var/www/html (./front)
- **Purpose**: Public website access
- **Database Access**: Read-only

### Back Office Container (web-bo)
- **Port**: 8091
- **DocumentRoot**: /var/www/html (./admin)
- **Purpose**: Admin interface
- **Database Access**: Full CRUD

### Database Container (mysql)
- **Port**: 3306
- **Database**: guerre_iran
- **Purpose**: Shared data storage

## Access URLs

- **Front Office**: http://localhost:8090
- **Back Office**: http://localhost:8091/login.php
- **Database**: localhost:3306

## Key Features

1. **Complete Separation**: FO and BO have independent codebases
2. **Separate Includes**: Each has its own includes folder
3. **Different Configurations**: Separate config.php files
4. **Database Separation**: FO read-only, BO full CRUD
5. **Container Isolation**: Separate Docker containers
6. **Shared Database**: Single MySQL instance
7. **SEO Files**: robots.txt and sitemap.xml in FO

## Authentication

- **Username**: admin
- **Password**: admin123
- **Hash**: $2y$10$g0T9W6WpVx6xKk0DADvpZecyuMpDRjc2DPwL7//3IAZfVjRJ0PgfK

## Database Schema

### Tables
- `categories` - Article categories
- `articles` - Blog/news articles
- `users` - Admin users

### Default Admin User
- Username: admin
- Password: admin123 (hashed)
- Role: admin
