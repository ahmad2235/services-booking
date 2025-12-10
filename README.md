# Home Services Booking Platform

A comprehensive web-based platform for booking home services, built as a final project for Web Engineering course (2025-2026).

## 📋 Project Description

This platform connects customers with home service providers (electricians, plumbers, cleaners, painters, etc.). Customers can browse services, view provider profiles, check availability, and book appointments. Providers can manage their services, availability, and handle bookings. Administrators oversee the entire platform.

## 🛠 Tech Stack

- **Framework**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL 8.0+
- **Frontend**: Blade Templates + Bootstrap 5
- **Architecture**: Multi-layer MVC
  - Controllers → Services (Business Logic) → Repositories (Data Access) → Eloquent Models

## 📐 Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                      PRESENTATION LAYER                      │
│  ┌─────────────────────────────────────────────────────────┐│
│  │                    Blade Views                          ││
│  │  (layouts, auth, customer, provider, admin)             ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER                       │
│  ┌─────────────────────────────────────────────────────────┐│
│  │                    Controllers                          ││
│  │  (AuthController, CustomerController, ProviderController││
│  │   AdminController, BookingController, etc.)             ││
│  └─────────────────────────────────────────────────────────┘│
│  ┌─────────────────────────────────────────────────────────┐│
│  │                 Form Requests                           ││
│  │  (Validation layer for all incoming requests)           ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      BUSINESS LAYER                          │
│  ┌─────────────────────────────────────────────────────────┐│
│  │                     Services                            ││
│  │  (AuthService, BookingService, RatingService,           ││
│  │   ProviderService, NotificationService, etc.)           ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      DATA ACCESS LAYER                       │
│  ┌─────────────────────────────────────────────────────────┐│
│  │                   Repositories                          ││
│  │  (UserRepository, BookingRepository, RatingRepository,  ││
│  │   ProviderRepository, ServiceRepository, etc.)          ││
│  └─────────────────────────────────────────────────────────┘│
│  ┌─────────────────────────────────────────────────────────┐│
│  │                  Eloquent Models                        ││
│  │  (User, Booking, Rating, ProviderProfile, Service, etc.)││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      DATABASE LAYER                          │
│  ┌─────────────────────────────────────────────────────────┐│
│  │                       MySQL                             ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
```

## 📦 Main Modules

1. **User Management** - Registration, authentication, profile management for customers, providers, and admins
2. **Services Catalog** - Service categories and services management
3. **Booking System** - Create, manage, accept/reject bookings
4. **Scheduling & Calendar** - Provider availability and time slots
5. **Ratings & Reviews** - Customer feedback system
6. **Admin Dashboard** - Platform oversight and management

## 🚀 Installation & Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 8.0+
- Node.js (for asset compilation, optional)

### Installation Steps

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd finalproject
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Configure environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**

   Edit `.env` file with your database credentials:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=home_services_booking
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Create database**

   ```sql
   CREATE DATABASE home_services_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

6. **Run migrations**

   ```bash
   php artisan migrate
   ```

7. **Seed the database**

   ```bash
   php artisan db:seed
   ```

8. **Start the development server**

   ```bash
   php artisan serve
   ```

9. **Access the application**

   Open your browser and navigate to: `http://localhost:8000`

## 👤 Default User Credentials

After running the seeders, you can log in with these accounts:

| Role     | Email                  | Password |
| -------- | ---------------------- | -------- |
| Admin    | admin@homeservices.com | password |
| Customer | customer1@example.com  | password |
| Customer | customer2@example.com  | password |
| Provider | provider1@example.com  | password |
| Provider | provider2@example.com  | password |
| Provider | provider3@example.com  | password |

## 📁 Project Structure

```
finalproject/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── Customer/
│   │   │   ├── Provider/
│   │   │   └── Admin/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Services/
│   ├── Repositories/
│   └── ViewModels/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── auth/
│       ├── customer/
│       ├── provider/
│       ├── admin/
│       └── public/
├── routes/
│   ├── web.php
│   └── api.php
├── tests/
│   └── Feature/
├── docs/
│   ├── architecture.md
│   ├── database-schema.md
│   └── use-cases.md
└── README.md
```

## ✅ Implemented Features

### Customer Features

- [x] Registration and login
- [x] Browse service categories and services
- [x] Search providers by location, service, and price
- [x] View provider profiles and availability
- [x] Book services with selected time slots
- [x] View and manage bookings
- [x] Cancel bookings (within allowed timeframe)
- [x] Rate and review completed services

### Provider Features

- [x] Registration and login
- [x] Manage profile information
- [x] Define covered locations
- [x] Manage offered services with custom pricing
- [x] Set availability (time slots)
- [x] Accept or reject booking requests
- [x] Mark bookings as completed
- [x] View ratings and reviews

### Admin Features

- [x] Dashboard with platform statistics
- [x] Manage users (activate/deactivate)
- [x] Manage service categories (CRUD)
- [x] Manage services (CRUD)
- [x] Moderate ratings (hide inappropriate)
- [x] View audit logs

## ❌ Out of Scope (Not Implemented)

- Online payment processing
- Mobile application
- Paid map APIs / GPS tracking
- Real-time in-app chat
- Email/SMS notifications (logging only)

## 🧪 Running Tests

```bash
php artisan test
```

Or with verbose output:

```bash
php artisan test --verbose
```

## 📚 Documentation

- [Architecture Documentation](docs/architecture.md)
- [Database Schema](docs/database-schema.md)
- [Use Cases](docs/use-cases.md)

## 🔒 Security Features

- Password hashing with bcrypt
- CSRF protection on all forms
- Form validation with Laravel Form Requests
- Role-based access control middleware
- SQL injection prevention via Eloquent ORM
- XSS prevention via Blade escaping

## 📄 License

This project is created for educational purposes as part of the Web Engineering course at AIU (2025-2026).

---

**Course**: Web Engineering - Final Project  
**Academic Year**: 2025-2026  
**University**: Arab International University (AIU)
