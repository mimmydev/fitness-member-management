# Fitness Centre Member Management System

![FitnessCentre Fullstack](./fitnesscentre.png)

A production-grade member management system built with Laravel 12.x + Vue 3.5 demonstrating Clean Code principles, SOLID design patterns, and modern full-stack development practices.

## Tech Stack

### Backend
- **Laravel 12.x** (PHP 8.2+)
- **MySQL 8.4.7 LTS** - Long-term support track
- **Laravel Sanctum 4.x** - SPA token authentication
- **Composer** - Dependency management

### Frontend
- **Vue 3.5.x** (stable) - Composition API
- **TypeScript** - Type safety
- **Vite 7.x** - Build tool
- **shadcn-vue 2.4.3** - UI components (Radix UI/radix-vue)
- **Tailwind CSS 4.x** - Styling
- **Vue Router 4.x** - Client-side routing
- **Axios** - HTTP client

## Architecture Highlights

### Clean Code Principles Demonstrated

1. **Meaningful Names**: All variables, methods, and classes reveal intent
2. **Single Responsibility**: Each class/function has one reason to change
3. **Small Functions**: Most functions under 20 lines
4. **DRY**: Shared logic in services, composables, utilities
5. **Separation of Concerns**: Controllers route, Services contain logic, Models represent data

### Backend Architecture

**MVC + Service Layer Pattern:**
- **Controllers** (Traffic Cops): Route HTTP requests, delegate to services
- **Services** (Business Logic): Transactions, validation, business rules
- **Models** (Data Representation): Relationships, accessors, scopes
- **Form Requests**: Validation logic separation
- **API Resources**: Data transformation layer
- **Normalized Database**: `users` (auth) separated from `member_profiles` (business data)

**Key Files:**
```
backend/
├── app/
│   ├── Models/
│   │   ├── User.php              # Auth model with HasApiTokens
│   │   └── MemberProfile.php      # Business model with SoftDeletes
│   ├── Services/
│   │   ├── AuthService.php        # Authentication business logic
│   │   └── MemberService.php      # Member CRUD business logic
│   ├── Policies/
│   │   └── MemberProfilePolicy.php  # Authorization rules
│   ├── Http/
│   │   ├── Controllers/Api/       # Thin controllers
│   │   ├── Requests/              # Form validation
│   │   └── Resources/             # API transformation
│   └── database/migrations/        # Schema with indexed FKs
```

### Frontend Architecture

**Composition API + Composables Pattern:**
- **Composables** (State Management): Reactive state without Pinia
- **Components**: Feature-based organization (auth/, members/, layout/)
- **Router**: Navigation guards for authentication
- **API Client**: Axios with interceptors for token injection
- **Type Safety**: TypeScript interfaces throughout

**Key Files:**
```
frontend/
├── src/
│   ├── composables/
│   │   ├── useAuth.ts            # Auth state management
│   │   └── useMembers.ts         # Member state management
│   ├── lib/
│   │   └── api.ts                # Axios client with interceptors
│   ├── types/
│   │   ├── auth.ts               # Auth type definitions
│   │   ├── member.ts             # Member type definitions
│   │   └── api.ts                # API response types
│   └── router/
│       └── index.ts              # Routes with guards
```

## Security Considerations

### Authentication Strategy

**Current Implementation (MVP):**
- Authentication tokens stored in `localStorage`
- Token injected via Axios request interceptor
- Automatic redirect on 401 errors
- Simpler implementation for technical assessment

**Security Implications:**
- **Vulnerability**: Tokens in localStorage are accessible to JavaScript, making them vulnerable to XSS (Cross-Site Scripting) attacks
- **Acceptable for MVP**: This approach is acceptable for development and demonstration purposes
- **Not Production-Ready**: Should be replaced with httpOnly cookies before production deployment

**Recommended Production Approach:**
```typescript
// Use Laravel Sanctum's httpOnly cookie-based authentication
// Remove token storage from localStorage
// Configure Axios with withCredentials: true (already implemented)
// Tokens managed securely by browser, not accessible to JavaScript
```

**Implementation Priority:**
- **MVP/Assessment**: Current approach demonstrates SPA authentication concepts
- **Production**: Switch to httpOnly cookies for enhanced security
- **Documented Decision**: This limitation is intentional for MVP scope, not an oversight

**Reference:**
- Backend: `backend/config/cors.php` - `supports_credentials: true` already configured
- Frontend: `frontend/src/lib/api.ts` - `withCredentials: true` already configured
- Both sides are prepared for httpOnly cookie authentication

## Project Setup

### Prerequisites
- PHP 8.2+
- Composer 2.x
- Node.js 18+ / npm 9+
- MySQL 8.4.7

### Backend Setup

1. Navigate to backend directory:
```bash
cd backend
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitness_member_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate
```

6. Start development server:
```bash
php artisan serve
```

Backend will be available at: `http://localhost:8000`

### Frontend Setup

1. Navigate to frontend directory:
```bash
cd frontend
```

2. Install dependencies:
```bash
npm install
```

3. Configure environment:
```bash
cp .env.example .env
```

4. Update `.env` if needed:
```env
VITE_API_BASE_URL=http://localhost:8000/api
```

5. Start development server:
```bash
npm run dev
```

Frontend will be available at: `http://localhost:5173`

## Database Management

### Bazzite/Linux (Podman)

This project uses MariaDB 10.11 running in a rootless podman container.

#### Start Database
```bash
# Start MySQL container
podman start fitness-mysql

# Verify it's running
podman ps
```

#### Stop Database
```bash
# Stop container (preserves data)
podman stop fitness-mysql

# Check status
podman ps -a
```

#### Initial Setup (First Time Only)
```bash
# Create and run MySQL container
podman run -d \
  --name fitness-mysql \
  -p 3306:3306 \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=fitness_member_management \
  mariadb:10.11

# Wait a few seconds for MySQL to start, then run migrations
cd backend
php artisan migrate
```

#### Connect with DBeaver
- **Server Host:** localhost
- **Port:** 3306
- **Database:** fitness_member_management
- **Username:** root
- **Password:** root

#### Remove Database (If Needed)
```bash
# Stop and remove container (data will be lost)
podman stop fitness-mysql
podman rm fitness-mysql

# Optional: Remove image
podman rmi mariadb:10.11
```

### macOS

#### Option 1: Docker Desktop
```bash
# Install Docker Desktop from https://www.docker.com/products/docker-desktop

# Start MySQL container
docker run -d \
  --name fitness-mysql \
  -p 3306:3306 \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=fitness_member_management \
  mysql:8.4.7

# Stop container
docker stop fitness-mysql

# Start container
docker start fitness-mysql
```

#### Option 2: Homebrew MySQL
```bash
# Install MySQL
brew install mysql

# Start MySQL service
brew services start mysql

# Create database
mysql -u root -p -e "CREATE DATABASE fitness_member_management;"

# Stop MySQL service
brew services stop mysql
```

**DBeaver Connection:**
- Same credentials as Bazzite setup above

### Windows

#### Option 1: Docker Desktop
```bash
# Install Docker Desktop from https://www.docker.com/products/docker-desktop

# Start MySQL container (PowerShell)
docker run -d `
  --name fitness-mysql `
  -p 3306:3306 `
  -e MYSQL_ROOT_PASSWORD=root `
  -e MYSQL_DATABASE=fitness_member_management `
  mysql:8.4.7

# Stop container
docker stop fitness-mysql

# Start container
docker start fitness-mysql
```

#### Option 2: XAMPP
```bash
# Download and install XAMPP from https://www.apachefriends.org

# Start XAMPP Control Panel
# Click "Start" for MySQL module

# Create database using phpMyAdmin (http://localhost/phpmyadmin)
# Database name: fitness_member_management
# Username: root
# Password: (leave empty by default)
```

#### Option 3: WSL2 + Docker
```bash
# Install WSL2 and Docker Desktop with WSL2 backend
# Follow Linux/Bazzite instructions above using docker command instead of podman
```

**DBeaver Connection (XAMPP):**
- **Server Host:** localhost
- **Port:** 3306
- **Database:** fitness_member_management
- **Username:** root
- **Password:** (leave empty for XAMPP default)

### Test Credentials

After running migrations, you can log in with:
- **Email:** admin@test.com
- **Password:** password

## Laravel Maintenance Commands

Common commands for cache management and maintenance during development.

### Clear All Caches
```bash
cd backend

# Clear everything at once
php artisan optimize:clear
```

### Individual Cache Commands

#### Application Cache
```bash
# Clear application cache
php artisan cache:clear

# Cache configuration
php artisan config:cache
```

#### Route Cache
```bash
# Cache routes (for production - speeds up routing)
php artisan route:cache

# Clear route cache (required when adding/modifying routes)
php artisan route:clear

# View all routes
php artisan route:list
```

#### View Cache
```bash
# Clear compiled view files
php artisan view:clear

# Pre-compile views
php artisan view:cache
```

#### Event & Discovery Cache
```bash
# Clear cached events
php artisan event:clear

# Cache events
php artisan event:cache
```

### Database Commands
```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh migration (drops all tables and re-migrates)
php artisan migrate:fresh

# Fresh migration with seeders
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

### Storage & Links
```bash
# Create symbolic link from public/storage to storage/app/public
php artisan storage:link
```

### Queue Management
```bash
# Run queue worker
php artisan queue:work

# Run queue worker for specific connection
php artisan queue:work database

# Clear all jobs from queue
php artisan queue:clear

# Restart queue workers
php artisan queue:restart
```

### Development Helpers
```bash
# List all artisan commands
php artisan list

# Get help for specific command
php artisan help migrate

# Interactive shell (Tinker)
php artisan tinker

# Generate application key
php artisan key:generate

# Run Laravel development server
php artisan serve

# Run on specific port
php artisan serve --port=8080
```

### Common Development Workflow
```bash
# After pulling code changes
composer install
php artisan migrate
php artisan optimize:clear

# After modifying routes
php artisan route:clear

# After modifying config files
php artisan config:clear

# Before deploying to production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Troubleshooting
```bash
# If you get "Class not found" errors
composer dump-autoload

# If routes aren't working
php artisan route:clear

# If config changes aren't applying
php artisan config:clear

# If views aren't updating
php artisan view:clear

# Nuclear option - clear everything
php artisan optimize:clear
composer dump-autoload
```

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user (throttle: 3/10min)
- `POST /api/auth/login` - Login, returns token (throttle: 5/min)
- `POST /api/auth/logout` - Logout current session
- `POST /api/auth/logout-all` - Logout all devices (account compromise recovery)
- `GET /api/auth/me` - Get authenticated user

### Members (Protected - requires auth)
- `GET /api/members` - List all members (paginated, with server-side search)
- `POST /api/members` - Create member profile
- `GET /api/members/{id}` - Get single member
- `PUT /api/members/{id}` - Update member
- `DELETE /api/members/{id}` - Delete member (soft delete)

## Database Schema

### Users Table
- Authentication credentials only
- Managed by Laravel Auth

### Member Profiles Table
- Business-specific member data
- 1:1 relationship with users
- Indexed foreign keys for performance
- Soft deletes for audit trail

**Indexes:**
- `user_id` (FK) - automatic via foreignId()
- `membership_status` - filtering
- `membership_type` - filtering
- `['last_name', 'first_name']` - name search
- `membership_end_date` - expiry queries

## License

MIT License