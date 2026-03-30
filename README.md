# Guerre en Iran - News Website

A Docker-based news website about the Iran conflict with complete Front Office (FO) and Back Office (BO) separation.

## Quick Start

```bash
# Clone and start
git clone <repository-url>
cd guerre_iran
docker-compose up --build -d

# Access applications
# Front Office: http://localhost:8090
# Back Office: http://localhost:8091/login.php
# Username: admin, Password: admin123
```

## Architecture

### 🏗️ Container-Based Architecture
- **Front Office**: Port 8090 (Public website)
- **Back Office**: Port 8091 (Admin interface)
- **Database**: Port 3306 (Shared MySQL)

### 📁 Project Structure
```
front/     # Public website (FO)
admin/     # Admin interface (BO)
docker/    # Container configurations
```

## Features

### Front Office
- 📰 News articles display
- 🔍 SEO optimized (robots.txt, sitemap.xml)
- 📱 Mobile responsive
- ⚡ Fast loading

### Back Office
- 🔐 Secure admin login
- 📝 Article management (CRUD)
- 👥 User management
- 📊 Analytics dashboard

### Technical Features
- 🐳 Docker containerization
- 🗄️ MySQL database
- 🔗 Complete FO/BO separation
- 🛡️ Security best practices

## Documentation

- [**Project Structure**](PROJECT_STRUCTURE.md) - Complete directory overview
- [**Setup Guide**](SETUP_GUIDE.md) - Installation and configuration
- [**Development Notes**](DEVELOPMENT_NOTES.md) - Technical details and decisions

## Requirements

- Docker Desktop
- Git (optional)

## Development

### Front Office Development
```bash
# Edit files in front/ directory
# Changes auto-reload
# URL: http://localhost:8090
```

### Back Office Development
```bash
# Edit files in admin/ directory  
# Changes auto-reload
# URL: http://localhost:8091
```

## Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Rebuild containers
docker-compose up --build -d
```

## Security

- 🔐 Password hashing with bcrypt
- 🛡️ SQL injection prevention
- 🔒 XSS protection
- 🚫 CSRF protection
- 👤 Role-based access control

## Database

- **Host**: localhost:3306
- **Database**: guerre_iran
- **User**: root
- **Password**: password

## Contributing

1. Fork the repository
2. Create feature branch
3. Make changes
4. Test thoroughly
5. Submit pull request

## License

[Your License Here]

---

**Built with ❤️ using Docker, PHP, and MySQL**
