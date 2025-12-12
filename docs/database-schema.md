# Database Schema Documentation

## Overview

The Home Services Booking Platform uses MySQL as its database management system. The schema consists of 12 main tables that support user management, service catalog, booking system, and ratings.

## Entity Relationship Diagram

```
┌─────────────┐       ┌──────────────────┐       ┌──────────────────┐
│    users    │──────<│ customer_profiles │       │ provider_profiles│
└─────────────┘       └──────────────────┘       └──────────────────┘
      │                        │                          │
      │                        │                          │
      │                        ▼                          ▼
      │               ┌──────────────┐           ┌────────────────────┐
      │               │   bookings   │──────────>│  provider_services │
      │               └──────────────┘           └────────────────────┘
      │                      │                            │
      │                      │                            │
      │                      ▼                            ▼
      │               ┌──────────────┐           ┌──────────────┐
      │               │   ratings    │           │   services   │
      │               └──────────────┘           └──────────────┘
      │                                                   │
      │                                                   ▼
      │                                          ┌────────────────────┐
      │                                          │ service_categories │
      │                                          └────────────────────┘
      │
      ▼
┌──────────────────┐     ┌────────────────────┐
│  notifications   │     │ provider_time_slots│
└──────────────────┘     └────────────────────┘
      │
      │                  ┌──────────────────────┐
      │                  │  provider_locations  │ (pivot)
      ▼                  └──────────────────────┘
┌──────────────────┐              │
│  admin_actions   │     ┌────────────────┐
└──────────────────┘     │   locations    │
                         └────────────────┘
```

## Table Definitions

### 1. users

Primary user authentication table.

| Column            | Type         | Constraints                  | Description                           |
| ----------------- | ------------ | ---------------------------- | ------------------------------------- |
| id                | BIGINT       | PK, AUTO_INCREMENT           | Unique identifier                     |
| name              | VARCHAR(255) | NOT NULL                     | User's full name                      |
| email             | VARCHAR(255) | NOT NULL, UNIQUE             | Email address                         |
| email_verified_at | TIMESTAMP    | NULLABLE                     | Email verification timestamp          |
| password          | VARCHAR(255) | NOT NULL                     | Bcrypt hashed password                |
| role              | ENUM         | NOT NULL, DEFAULT 'customer' | User role (customer, provider, admin) |
| is_active         | BOOLEAN      | DEFAULT TRUE                 | Account active status                 |
| remember_token    | VARCHAR(100) | NULLABLE                     | Remember me token                     |
| created_at        | TIMESTAMP    |                              | Creation timestamp                    |
| updated_at        | TIMESTAMP    |                              | Last update timestamp                 |

**Indexes**: `email` (unique)

---

### 2. customer_profiles

Extended profile information for customers.

| Column     | Type        | Constraints        | Description              |
| ---------- | ----------- | ------------------ | ------------------------ |
| id         | BIGINT      | PK, AUTO_INCREMENT | Unique identifier        |
| user_id    | BIGINT      | FK, UNIQUE         | Reference to users table |
| phone      | VARCHAR(20) | NULLABLE           | Phone number             |
| address    | TEXT        | NULLABLE           | Default address          |
| created_at | TIMESTAMP   |                    | Creation timestamp       |
| updated_at | TIMESTAMP   |                    | Last update timestamp    |

**Foreign Keys**: `user_id` → `users.id` (CASCADE DELETE)

---

### 3. provider_profiles

Extended profile information for service providers.

| Column           | Type         | Constraints        | Description               |
| ---------------- | ------------ | ------------------ | ------------------------- |
| id               | BIGINT       | PK, AUTO_INCREMENT | Unique identifier         |
| user_id          | BIGINT       | FK, UNIQUE         | Reference to users table  |
| company_name     | VARCHAR(100) | NOT NULL           | Business name             |
| phone            | VARCHAR(20)  | NULLABLE           | Business phone            |
| bio              | TEXT         | NULLABLE           | Business description      |
| years_of_experience | INT          | DEFAULT 0          | Years of experience       |
| avg_rating       | DECIMAL(3,2) | DEFAULT 0.00       | Calculated average rating |
| created_at       | TIMESTAMP    |                    | Creation timestamp        |
| updated_at       | TIMESTAMP    |                    | Last update timestamp     |

**Foreign Keys**: `user_id` → `users.id` (CASCADE DELETE)

---

### 4. locations

Available service locations.

| Column     | Type         | Constraints        | Description           |
| ---------- | ------------ | ------------------ | --------------------- |
| id         | BIGINT       | PK, AUTO_INCREMENT | Unique identifier     |
| city       | VARCHAR(100) | NOT NULL           | City name             |
| state      | VARCHAR(100) | NOT NULL           | State/Province        |
| zip_code   | VARCHAR(20)  | NULLABLE           | Postal code           |
| country    | VARCHAR(100) | DEFAULT 'USA'      | Country               |
| created_at | TIMESTAMP    |                    | Creation timestamp    |
| updated_at | TIMESTAMP    |                    | Last update timestamp |

**Indexes**: `city`, `state`

---

### 5. provider_locations (Pivot Table)

Many-to-many relationship between providers and locations.

| Column      | Type      | Constraints        | Description                    |
| ----------- | --------- | ------------------ | ------------------------------ |
| id          | BIGINT    | PK, AUTO_INCREMENT | Unique identifier              |
| provider_id | BIGINT    | FK                 | Reference to provider_profiles |
| location_id | BIGINT    | FK                 | Reference to locations         |
| created_at  | TIMESTAMP |                    | Creation timestamp             |
| updated_at  | TIMESTAMP |                    | Last update timestamp          |

**Foreign Keys**:

- `provider_id` → `provider_profiles.id` (CASCADE DELETE)
- `location_id` → `locations.id` (CASCADE DELETE)

**Unique Constraint**: `(provider_id, location_id)`

---

### 6. service_categories

Categories for grouping services.

| Column      | Type         | Constraints        | Description           |
| ----------- | ------------ | ------------------ | --------------------- |
| id          | BIGINT       | PK, AUTO_INCREMENT | Unique identifier     |
| name        | VARCHAR(100) | NOT NULL, UNIQUE   | Category name         |
| description | TEXT         | NULLABLE           | Category description  |
| created_at  | TIMESTAMP    |                    | Creation timestamp    |
| updated_at  | TIMESTAMP    |                    | Last update timestamp |

---

### 7. services

Service definitions (master data).

| Column           | Type          | Constraints          | Description                     |
| ---------------- | ------------- | -------------------- | ------------------------------- |
| id               | BIGINT        | PK, AUTO_INCREMENT   | Unique identifier               |
| category_id      | BIGINT        | FK                   | Reference to service_categories |
| name             | VARCHAR(100)  | NOT NULL             | Service name                    |
| description      | TEXT          | NULLABLE             | Service description             |
| base_price       | DECIMAL(10,2) | NOT NULL             | Suggested base price            |
| duration_minutes | INT           | NOT NULL, DEFAULT 60 | Expected duration               |
| created_at       | TIMESTAMP     |                      | Creation timestamp              |
| updated_at       | TIMESTAMP     |                      | Last update timestamp           |

**Foreign Keys**: `category_id` → `service_categories.id` (CASCADE DELETE)

---

### 8. provider_services

Services offered by specific providers.

| Column             | Type          | Constraints        | Description                    |
| ------------------ | ------------- | ------------------ | ------------------------------ |
| id                 | BIGINT        | PK, AUTO_INCREMENT | Unique identifier              |
| provider_id        | BIGINT        | FK                 | Reference to provider_profiles |
| service_id         | BIGINT        | FK                 | Reference to services          |
| price              | DECIMAL(10,2) | NOT NULL           | Provider's price               |
| custom_description | TEXT          | NULLABLE           | Provider's custom description  |
| is_active          | BOOLEAN       | DEFAULT TRUE       | Service availability           |
| created_at         | TIMESTAMP     |                    | Creation timestamp             |
| updated_at         | TIMESTAMP     |                    | Last update timestamp          |

**Foreign Keys**:

- `provider_id` → `provider_profiles.id` (CASCADE DELETE)
- `service_id` → `services.id` (CASCADE DELETE)

**Unique Constraint**: `(provider_id, service_id)`

---

### 9. provider_time_slots

Available time slots for providers.

| Column         | Type      | Constraints         | Description                    |
| -------------- | --------- | ------------------- | ------------------------------ |
| id             | BIGINT    | PK, AUTO_INCREMENT  | Unique identifier              |
| provider_id    | BIGINT    | FK                  | Reference to provider_profiles |
| start_datetime | DATETIME  | NOT NULL            | Slot start time                |
| end_datetime   | DATETIME  | NOT NULL            | Slot end time                  |
| status         | ENUM      | DEFAULT 'available' | Slot status                    |
| created_at     | TIMESTAMP |                     | Creation timestamp             |
| updated_at     | TIMESTAMP |                     | Last update timestamp          |

**Status Values**: `available`, `reserved`, `blocked`

**Foreign Keys**: `provider_id` → `provider_profiles.id` (CASCADE DELETE)

**Indexes**: `(provider_id, start_datetime)`

---

### 10. bookings

Customer booking records.

| Column              | Type          | Constraints        | Description                      |
| ------------------- | ------------- | ------------------ | -------------------------------- |
| id                  | BIGINT        | PK, AUTO_INCREMENT | Unique identifier                |
| customer_id         | BIGINT        | FK                 | Reference to customer_profiles   |
| provider_service_id | BIGINT        | FK                 | Reference to provider_services   |
| time_slot_id        | BIGINT        | FK                 | Reference to provider_time_slots |
| scheduled_at        | DATETIME      | NOT NULL           | Scheduled date/time              |
| status              | ENUM          | DEFAULT 'pending'  | Booking status                   |
| total_price         | DECIMAL(10,2) | NOT NULL           | Final price                      |
| address             | TEXT          | NOT NULL           | Service address                  |
| notes               | TEXT          | NULLABLE           | Customer notes                   |
| cancellation_reason | TEXT          | NULLABLE           | Reason if cancelled              |
| rejection_reason    | TEXT          | NULLABLE           | Reason if rejected               |
| created_at          | TIMESTAMP     |                    | Creation timestamp               |
| updated_at          | TIMESTAMP     |                    | Last update timestamp            |

**Status Values**: `pending`, `confirmed`, `rejected`, `cancelled`, `completed`

**Foreign Keys**:

- `customer_id` → `customer_profiles.id` (CASCADE DELETE)
- `provider_service_id` → `provider_services.id` (CASCADE DELETE)
- `time_slot_id` → `provider_time_slots.id` (SET NULL)

**Indexes**: `(customer_id, status)`, `(provider_service_id, status)`

---

### 11. ratings

Customer ratings for completed bookings.

| Column       | Type      | Constraints        | Description                               |
| ------------ | --------- | ------------------ | ----------------------------------------- |
| id           | BIGINT    | PK, AUTO_INCREMENT | Unique identifier                         |
| booking_id   | BIGINT    | FK, UNIQUE         | Reference to bookings                     |
| rating_value | TINYINT   | NOT NULL           | Rating value (1-5)                        |
| comment      | TEXT      | NULLABLE           | Review text                               |
| is_visible   | BOOLEAN   | DEFAULT TRUE       | Admin moderation flag (visible to public) |
| created_at   | TIMESTAMP |                    | Creation timestamp                        |
| updated_at   | TIMESTAMP |                    | Last update timestamp                     |

**Foreign Keys**: `booking_id` → `bookings.id` (CASCADE DELETE)

---

### 12. notifications

User notification system.

| Column     | Type         | Constraints        | Description           |
| ---------- | ------------ | ------------------ | --------------------- |
| id         | BIGINT       | PK, AUTO_INCREMENT | Unique identifier     |
| user_id    | BIGINT       | FK                 | Reference to users    |
| type       | VARCHAR(50)  | NOT NULL           | Notification type     |
| title      | VARCHAR(255) | NOT NULL           | Notification title    |
| message    | TEXT         | NOT NULL           | Notification content  |
| is_read    | BOOLEAN      | DEFAULT FALSE      | Read status           |
| created_at | TIMESTAMP    |                    | Creation timestamp    |
| updated_at | TIMESTAMP    |                    | Last update timestamp |

**Foreign Keys**: `user_id` → `users.id` (CASCADE DELETE)

**Indexes**: `(user_id, is_read)`

---

### 13. admin_actions

Audit log for admin operations.

| Column      | Type        | Constraints        | Description                |
| ----------- | ----------- | ------------------ | -------------------------- |
| id          | BIGINT      | PK, AUTO_INCREMENT | Unique identifier          |
| admin_id    | BIGINT      | FK                 | Reference to users (admin) |
| action_type | VARCHAR(50) | NOT NULL           | Type of action             |
| target_type | VARCHAR(50) | NULLABLE           | Target entity type         |
| target_id   | BIGINT      | NULLABLE           | Target entity ID           |
| description | TEXT        | NULLABLE           | Action description         |
| created_at  | TIMESTAMP   |                    | Creation timestamp         |
| updated_at  | TIMESTAMP   |                    | Last update timestamp      |

**Foreign Keys**: `admin_id` → `users.id` (CASCADE DELETE)

---

## Relationships Summary

| Relationship                        | Type         | Description               |
| ----------------------------------- | ------------ | ------------------------- |
| User → CustomerProfile              | One-to-One   | Customer extended profile |
| User → ProviderProfile              | One-to-One   | Provider extended profile |
| User → Notifications                | One-to-Many  | User's notifications      |
| User → AdminActions                 | One-to-Many  | Admin's audit trail       |
| ProviderProfile → Locations         | Many-to-Many | Service areas             |
| ProviderProfile → ProviderServices  | One-to-Many  | Offered services          |
| ProviderProfile → ProviderTimeSlots | One-to-Many  | Available slots           |
| ServiceCategory → Services          | One-to-Many  | Category services         |
| Service → ProviderServices          | One-to-Many  | Provider offerings        |
| CustomerProfile → Bookings          | One-to-Many  | Customer's bookings       |
| ProviderService → Bookings          | One-to-Many  | Service bookings          |
| ProviderTimeSlot → Bookings         | One-to-Many  | Slot bookings             |
| Booking → Rating                    | One-to-One   | Booking rating            |

## Indexes Strategy

- Primary keys on all `id` columns (auto-indexed)
- Unique indexes on email, category names
- Foreign key indexes (auto-created)
- Composite indexes on frequently queried combinations
- Status columns indexed for filtering
