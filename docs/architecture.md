# Architecture Documentation

## Overview

The Home Services Booking Platform follows a **multi-layer architecture** pattern, implementing a clean separation of concerns through the following layers:

```
┌─────────────────────────────────────────────────────────────┐
│                      Presentation Layer                      │
│              (Blade Views, Bootstrap CSS/JS)                 │
├─────────────────────────────────────────────────────────────┤
│                      Controller Layer                        │
│         (HTTP Request/Response, Form Validation)             │
├─────────────────────────────────────────────────────────────┤
│                       Service Layer                          │
│              (Business Logic, Orchestration)                 │
├─────────────────────────────────────────────────────────────┤
│                     Repository Layer                         │
│              (Data Access, Query Abstraction)                │
├─────────────────────────────────────────────────────────────┤
│                        Model Layer                           │
│             (Eloquent Models, Relationships)                 │
├─────────────────────────────────────────────────────────────┤
│                       Database Layer                         │
│                         (MySQL)                              │
└─────────────────────────────────────────────────────────────┘
```

## Layer Responsibilities

### 1. Presentation Layer (`resources/views/`)

- **Purpose**: Render HTML for user interaction
- **Technology**: Blade templates with Bootstrap 5
- **Structure**:
  - `layouts/` - Base templates (app, customer, provider, admin)
  - `auth/` - Authentication views (login, register)
  - `public/` - Public pages (home, services, provider profile)
  - `customer/` - Customer dashboard and booking views
  - `provider/` - Provider management views
  - `admin/` - Admin panel views

### 2. Controller Layer (`app/Http/Controllers/`)

- **Purpose**: Handle HTTP requests and coordinate responses
- **Responsibilities**:
  - Receive and validate input (via Form Requests)
  - Call appropriate service methods
  - Return views or redirects
  - Handle authentication/authorization via middleware

**Controller Structure**:

```
Controllers/
├── Controller.php (Base)
├── HomeController.php
├── Auth/
│   └── AuthController.php
├── Customer/
│   ├── DashboardController.php
│   ├── BookingController.php
│   └── RatingController.php
├── Provider/
│   ├── DashboardController.php
│   ├── ProfileController.php
│   ├── ServiceController.php
│   ├── TimeSlotController.php
│   └── BookingController.php
└── Admin/
    ├── DashboardController.php
    ├── UserController.php
    ├── ServiceCategoryController.php
    ├── ServiceController.php
    └── RatingController.php
```

### 3. Service Layer (`app/Services/`)

- **Purpose**: Encapsulate business logic
- **Responsibilities**:
  - Implement domain-specific operations
  - Coordinate multiple repository calls
  - Enforce business rules (e.g., booking overlap prevention)
  - Handle transactions when needed

**Services**:

- `AuthService` - User registration and authentication
- `BookingService` - Booking lifecycle management
- `RatingService` - Rating creation and moderation
- `ProviderManagementService` - Provider profile, services, time slots
- `AdminService` - Admin operations and statistics
- `NotificationService` - User notification management
- `CatalogService` - Public catalog browsing

### 4. Repository Layer (`app/Repositories/`)

- **Purpose**: Abstract data access from business logic
- **Responsibilities**:
  - CRUD operations
  - Complex queries
  - Data filtering and pagination

**Repository Pattern**:

```php
abstract class BaseRepository {
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 15);
}
```

### 5. Model Layer (`app/Models/`)

- **Purpose**: Define data structure and relationships
- **Responsibilities**:
  - Define table structure via `$fillable`, `$casts`
  - Define Eloquent relationships
  - Implement model-level business logic (accessors, mutators)

## Design Patterns Used

### 1. Repository Pattern

Abstracts data access, making the application database-agnostic and improving testability.

### 2. Service Pattern

Centralizes business logic, keeping controllers thin and focused on HTTP concerns.

### 3. Dependency Injection

Services and repositories are injected via constructor, managed by Laravel's service container.

### 4. Form Request Validation

Separates validation logic into dedicated request classes.

### 5. Middleware Pattern

Cross-cutting concerns (auth, role checking, active user) handled via middleware.

## Authentication & Authorization

### Authentication

- Laravel's built-in authentication with bcrypt password hashing
- Session-based authentication with remember me functionality
- Guards: `web` for all users

### Authorization

- **Role-based access control** via `RoleMiddleware`
- Three roles: `customer`, `provider`, `admin`
- **Active user check** via `ActiveUserMiddleware`

### Route Protection

```php
Route::middleware(['auth', 'active', 'role:customer'])->group(function () {
    // Customer routes
});

Route::middleware(['auth', 'active', 'role:provider'])->group(function () {
    // Provider routes
});

Route::middleware(['auth', 'active', 'role:admin'])->group(function () {
    // Admin routes
});
```

## Data Flow Example: Create Booking

```
1. User submits booking form
   ↓
2. CreateBookingRequest validates input
   ↓
3. BookingController@store receives request
   ↓
4. BookingService->createBooking() called
   │
   ├── Validates time slot availability
   ├── Checks for scheduling conflicts
   ├── Creates booking via BookingRepository
   ├── Updates time slot status
   └── Creates notification for provider
   ↓
5. Controller redirects with success message
   ↓
6. Blade view renders booking confirmation
```

## Error Handling

- **Validation Errors**: Returned via Form Request, displayed in views
- **Business Logic Errors**: Thrown as exceptions, caught in controllers
- **Database Errors**: Handled via try-catch with transaction rollback
- **Authorization Errors**: 403 responses via middleware

## Security Considerations

1. **CSRF Protection**: All forms include `@csrf` token
2. **Mass Assignment**: Protected via `$fillable` in models
3. **SQL Injection**: Prevented via Eloquent ORM
4. **XSS Prevention**: Blade's `{{ }}` auto-escapes output
5. **Authentication**: Secure session management
6. **Password Security**: bcrypt hashing
