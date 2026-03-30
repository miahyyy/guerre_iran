# Development Notes - Guerre en Iran Project

## Project Evolution

### Initial State
- Single monolithic application
- Shared includes folder
- Mixed FO/BO code in same directory
- Single Docker container

### Final Architecture
- Complete FO/BO separation
- Independent containers
- Separate includes folders
- Shared database

## Key Technical Decisions

### 1. Container Separation
**Decision**: Separate containers for FO and BO
**Reasoning**: 
- Better security isolation
- Independent scaling
- Clear separation of concerns
- Different configurations per environment

### 2. Database Strategy
**Decision**: Shared database with different access levels
**Reasoning**:
- Single source of truth
- FO read-only access
- BO full CRUD access
- Data consistency

### 3. File Structure
**Decision**: Separate includes folders
**Reasoning**:
- Independent configurations
- Different database methods
- FO-specific vs BO-specific functions
- Clear code ownership

## Implementation Details

### Docker Configuration

#### Front Office Container
```dockerfile
FROM php:8.3-apache
# PHP extensions for FO (GD, PDO, MySQL)
# Copy only front files
# FO-specific Apache config
```

#### Back Office Container
```dockerfile
FROM php:8.3-apache
# PHP extensions for BO (same as FO)
# Copy only admin files
# BO-specific Apache config
```

### Database Access Patterns

#### Front Office (Read-Only)
```php
// Only SELECT operations
public function getArticles($limit = null, $status = 'published')
public function getArticleBySlug($slug)
public function getCategories()
public function incrementViews($id) // Limited write for analytics
```

#### Back Office (Full CRUD)
```php
// Complete CRUD operations
public function createArticle($data)
public function updateArticle($id, $data)
public function deleteArticle($id)
public function getArticles($status = null) // Can get drafts
```

### Configuration Separation

#### Front Office Config
- SITE_URL: http://localhost:8090
- ADMIN_URL: http://localhost:8091
- Public-facing settings

#### Back Office Config
- SITE_URL: http://localhost:8091
- ADMIN_URL: http://localhost:8091
- Admin-specific settings

### Security Considerations

#### Authentication
- Session management in config.php
- Password hashing with bcrypt
- CSRF protection in BO functions
- Role-based access control

#### Container Security
- FO container: No admin files
- BO container: No public files
- Network isolation via Docker
- Minimal exposure surface

## Common Issues & Solutions

### 1. Session Conflicts
**Problem**: Duplicate session_start() calls
**Solution**: Single session_start() in config.php

### 2. File Path Issues
**Problem**: Wrong include paths after separation
**Solution**: Relative paths from each container root

### 3. Database Connection
**Problem**: Containers can't connect to MySQL
**Solution**: Use service name 'mysql' as host

### 4. Port Conflicts
**Problem**: Ports already in use
**Solution**: Stop existing containers or change ports

## Future Enhancements

### 1. API Layer
- REST API for FO
- Admin API for BO
- JWT authentication

### 2. Caching Strategy
- Redis for sessions
- File caching for FO
- Database query caching

### 3. CI/CD Pipeline
- Automated testing
- Container image building
- Deployment automation

### 4. Monitoring
- Application monitoring
- Database performance
- Container health checks

## Code Standards

### PHP Standards
- PSR-4 autoloading
- PSR-12 coding style
- Type hints where possible
- Error handling with exceptions

### Database Standards
- Prepared statements only
- Transaction support for BO
- Proper indexing
- Foreign key constraints

### Security Standards
- Input validation
- Output escaping
- SQL injection prevention
- XSS protection

## Testing Strategy

### Unit Testing
- Database methods
- Utility functions
- Configuration validation

### Integration Testing
- Container communication
- Database connectivity
- Authentication flow

### End-to-End Testing
- User workflows
- Admin operations
- SEO functionality

## Performance Metrics

### Front Office
- Page load time < 2s
- SEO score > 90
- Mobile optimization

### Back Office
- Admin response time < 1s
- Database query optimization
- Efficient CRUD operations

## Documentation Standards

### Code Documentation
- PHPDoc blocks
- Inline comments for complex logic
- README files in each module

### API Documentation
- Endpoint documentation
- Request/response examples
- Authentication requirements

## Backup & Recovery

### Database Backup
```bash
# Manual backup
docker exec guerre_iran-mysql-1 mysqldump -u root -ppassword guerre_iran > backup.sql

# Automated backup (cron job)
0 2 * * * docker exec guerre_iran-mysql-1 mysqldump -u root -ppassword guerre_iran > /backup/guerre_iran_$(date +\%Y\%m\%d).sql
```

### File Backup
- Code repository (Git)
- Asset backups
- Configuration backups

## Deployment Checklist

### Pre-deployment
- [ ] Update passwords
- [ ] Check configurations
- [ ] Test database connection
- [ ] Verify SSL certificates
- [ ] Performance testing

### Post-deployment
- [ ] Monitor containers
- [ ] Check application logs
- [ ] Verify user access
- [ ] Test admin functions
- [ ] SEO validation
