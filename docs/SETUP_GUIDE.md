# Setup Guide - Guerre en Iran Project

## Prerequisites
- Docker Desktop installed
- Git (optional)

## Quick Start

### 1. Clone/Download Project
```bash
git clone <repository-url>
cd guerre_iran
```

### 2. Start Containers
```bash
docker-compose up --build -d
```

### 3. Access Applications
- **Front Office**: http://localhost:8090
- **Back Office**: http://localhost:8091/login.php

### 4. Login to Admin
- **Username**: admin
- **Password**: admin123

## Container Management

### Start Services
```bash
docker-compose up -d
```

### Stop Services
```bash
docker-compose down
```

### Rebuild Services
```bash
docker-compose up --build -d
```

### View Logs
```bash
docker-compose logs -f
docker-compose logs web-fo
docker-compose logs web-bo
docker-compose logs mysql
```

### View Container Status
```bash
docker-compose ps
```

## Database Setup

### Initialize Database
The database is automatically created when containers start. To manually reset:

```bash
# Stop containers
docker-compose down

# Remove MySQL volume (WARNING: This deletes all data)
docker volume rm guerre_iran_mysql_data

# Restart containers
docker-compose up --build -d
```

### Access Database
- **Host**: localhost
- **Port**: 3306
- **Database**: guerre_iran
- **Username**: root
- **Password**: password

### Import Schema (if needed)
```bash
docker exec -i guerre_iran-mysql-1 mysql -u root -ppassword < schema.sql
```

## Development Workflow

### Front Office Development
- Files located in `front/` directory
- Changes are automatically reflected (volume mount)
- URL: http://localhost:8090

### Back Office Development
- Files located in `admin/` directory
- Changes are automatically reflected (volume mount)
- URL: http://localhost:8091

### Adding New Features

#### Front Office
1. Edit files in `front/` directory
2. Add database methods to `front/includes/database.php` (read-only)
3. Add utility functions to `front/includes/fonctions.php`

#### Back Office
1. Edit files in `admin/` directory
2. Add database methods to `admin/includes/database.php` (full CRUD)
3. Add admin functions to `admin/includes/fonctions.php`

## Configuration

### Front Office Config (`front/includes/config.php`)
```php
define('SITE_URL', 'http://localhost:8090');
define('ADMIN_URL', 'http://localhost:8091');
```

### Back Office Config (`admin/includes/config.php`)
```php
define('SITE_URL', 'http://localhost:8091');
define('ADMIN_URL', 'http://localhost:8091');
```

### Database Config (Both)
```php
define('DB_HOST', 'mysql');
define('DB_NAME', 'guerre_iran');
define('DB_USER', 'root');
define('DB_PASS', 'password');
```

## Troubleshooting

### Port Conflicts
If ports 8090, 8091, or 3306 are already in use:
1. Stop other services using these ports
2. Or modify ports in `docker-compose.yml`

### Container Issues
```bash
# Check container status
docker-compose ps

# View logs for specific container
docker-compose logs web-fo
docker-compose logs web-bo
docker-compose logs mysql

# Restart specific container
docker-compose restart web-fo
docker-compose restart web-bo
```

### Database Connection Issues
1. Ensure MySQL container is running
2. Check database credentials in config files
3. Verify database exists: `docker exec guerre_iran-mysql-1 mysql -u root -ppassword -e "SHOW DATABASES;"`

### File Permission Issues
```bash
# Fix file permissions (run from project root)
sudo chown -R $USER:$USER front/ admin/
```

## Security Notes

### Production Deployment
1. Change default admin password
2. Update database credentials
3. Use HTTPS (SSL certificates)
4. Implement proper firewall rules
5. Regular security updates

### Password Hashing
To create new hashed passwords:
```bash
docker exec guerre_iran-web-fo-1 php create_admin.php
```

## Performance Optimization

### Caching
- Front Office: Implement browser caching via .htaccess
- Back Office: Consider Redis for session storage

### Database
- Add indexes for frequently queried columns
- Implement connection pooling if needed

### Container Resources
- Adjust memory limits in docker-compose.yml if needed
- Monitor container resource usage
