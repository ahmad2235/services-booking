# Use Cases Documentation

## Overview

This document describes the main use cases of the Home Services Booking Platform, organized by user role.

---

## 1. Public (Guest) Use Cases

### UC-1.1: Browse Service Categories

**Actor**: Guest User
**Description**: View available service categories on the platform.
**Preconditions**: None
**Flow**:

1. User navigates to the home page
2. System displays featured service categories
3. User can click on a category to view services

### UC-1.2: Search Providers

**Actor**: Guest User
**Description**: Search for service providers based on various criteria.
**Preconditions**: None
**Flow**:

1. User navigates to the Services page
2. User selects filters (category, location, search term)
3. System displays matching providers with ratings
4. User can click on a provider to view details

### UC-1.3: View Provider Profile

**Actor**: Guest User
**Description**: View detailed information about a service provider.
**Preconditions**: None
**Flow**:

1. User clicks on a provider card
2. System displays provider details including:
   - Company information
   - Services offered with prices
   - Customer ratings and reviews
   - Service areas

### UC-1.4: Register as Customer

**Actor**: Guest User
**Description**: Create a new customer account.
**Preconditions**: User does not have an existing account
**Flow**:

1. User clicks "Register as Customer"
2. User fills in registration form (name, email, phone, address, password)
3. System validates input
4. System creates user account and customer profile
5. User is logged in and redirected to dashboard

### UC-1.5: Register as Provider

**Actor**: Guest User
**Description**: Create a new service provider account.
**Preconditions**: User does not have an existing account
**Flow**:

1. User clicks "Register as Provider"
2. User fills in registration form (name, email, company name, phone, bio, experience, password)
3. System validates input
4. System creates user account and provider profile
5. User is logged in and redirected to provider dashboard

---

## 2. Customer Use Cases

### UC-2.1: View Dashboard

**Actor**: Customer
**Description**: View personalized dashboard with booking statistics.
**Preconditions**: User is logged in as customer
**Flow**:

1. Customer navigates to dashboard
2. System displays:
   - Booking statistics (total, pending, completed)
   - Recent bookings list

### UC-2.2: Create Booking

**Actor**: Customer
**Description**: Book a service from a provider.
**Preconditions**:

- User is logged in as customer
- Provider has available time slots
  **Flow**:

1. Customer views provider profile
2. Customer clicks "Book Now" on a service
3. System displays available time slots
4. Customer selects time slot and enters address
5. Customer optionally adds notes
6. Customer submits booking
7. System creates booking with "pending" status
8. System notifies provider
   **Alternative Flow**:

- If no time slots available, system displays message

### UC-2.3: View Booking Details

**Actor**: Customer
**Description**: View detailed information about a booking.
**Preconditions**: User owns the booking
**Flow**:

1. Customer navigates to bookings list
2. Customer clicks on a booking
3. System displays booking details including:
   - Service and provider information
   - Scheduled date/time
   - Status
   - Price
   - Rating (if completed)

### UC-2.4: Cancel Booking

**Actor**: Customer
**Description**: Cancel a pending or confirmed booking.
**Preconditions**:

- User owns the booking
- Booking status is "pending" or "confirmed"
- Cancellation is allowed (based on time policy)
  **Flow**:

1. Customer views booking details
2. Customer clicks "Cancel Booking"
3. Customer optionally enters cancellation reason
4. Customer confirms cancellation
5. System updates booking status to "cancelled"
6. System releases time slot
7. System notifies provider

### UC-2.5: Rate Completed Booking

**Actor**: Customer
**Description**: Submit a rating for a completed service.
**Preconditions**:

- Booking status is "completed"
- No rating exists for this booking
  **Flow**:

1. Customer views completed booking
2. Customer clicks "Rate Service"
3. Customer selects star rating (1-5)
4. Customer optionally enters review comment
5. Customer submits rating
6. System saves rating
7. System recalculates provider's average rating

---

## 3. Provider Use Cases

### UC-3.1: View Dashboard

**Actor**: Provider
**Description**: View personalized dashboard with business statistics.
**Preconditions**: User is logged in as provider
**Flow**:

1. Provider navigates to dashboard
2. System displays:
   - Booking statistics
   - Average rating
   - Total earnings
   - Pending bookings requiring action
   - Upcoming confirmed bookings

### UC-3.2: Update Profile

**Actor**: Provider
**Description**: Update business profile information.
**Preconditions**: User is logged in as provider
**Flow**:

1. Provider navigates to Profile page
2. Provider updates company name, phone, bio, experience
3. Provider submits changes
4. System validates and saves updates

### UC-3.3: Manage Service Areas

**Actor**: Provider
**Description**: Select locations where services are offered.
**Preconditions**: User is logged in as provider
**Flow**:

1. Provider navigates to Locations page
2. System displays available locations
3. Provider checks/unchecks locations
4. Provider saves changes
5. System updates provider-location associations

### UC-3.4: Add Service

**Actor**: Provider
**Description**: Add a new service to offerings.
**Preconditions**:

- User is logged in as provider
- Service not already offered by this provider
  **Flow**:

1. Provider navigates to Services page
2. Provider clicks "Add Service"
3. Provider selects service from catalog
4. Provider sets custom price
5. Provider optionally adds custom description
6. Provider submits
7. System creates provider_service record

### UC-3.5: Manage Time Slots

**Actor**: Provider
**Description**: Create and manage availability time slots.
**Preconditions**: User is logged in as provider
**Flow**:

1. Provider navigates to Time Slots page
2. Provider clicks "Add Time Slot"
3. Provider enters start and end date/time
4. Provider selects status (available/blocked)
5. Provider submits
6. System creates time slot

### UC-3.6: Accept Booking

**Actor**: Provider
**Description**: Accept a pending booking request.
**Preconditions**:

- Booking belongs to provider's service
- Booking status is "pending"
  **Flow**:

1. Provider views pending booking
2. Provider clicks "Accept"
3. System updates booking to "confirmed"
4. System updates time slot to "reserved"
5. System notifies customer

### UC-3.7: Reject Booking

**Actor**: Provider
**Description**: Reject a pending booking request.
**Preconditions**:

- Booking belongs to provider's service
- Booking status is "pending"
  **Flow**:

1. Provider views pending booking
2. Provider clicks "Reject"
3. Provider optionally enters rejection reason
4. System updates booking to "rejected"
5. System releases time slot
6. System notifies customer

### UC-3.8: Complete Booking

**Actor**: Provider
**Description**: Mark a confirmed booking as completed.
**Preconditions**:

- Booking belongs to provider's service
- Booking status is "confirmed"
  **Flow**:

1. Provider views confirmed booking
2. Provider clicks "Mark as Completed"
3. System updates booking to "completed"
4. System notifies customer to rate

---

## 4. Admin Use Cases

### UC-4.1: View Dashboard

**Actor**: Admin
**Description**: View platform-wide statistics.
**Preconditions**: User is logged in as admin
**Flow**:

1. Admin navigates to dashboard
2. System displays:
   - Total users, providers, customers
   - Total bookings
   - Category and service counts
   - Recent admin actions

### UC-4.2: Manage Users

**Actor**: Admin
**Description**: View and manage user accounts.
**Preconditions**: User is logged in as admin
**Flow**:

1. Admin navigates to Users page
2. System displays user list with filters
3. Admin can:
   - View user details
   - Activate/deactivate accounts

### UC-4.3: Toggle User Active Status

**Actor**: Admin
**Description**: Enable or disable a user account.
**Preconditions**:

- User is logged in as admin
- Target user is not the current admin
  **Flow**:

1. Admin clicks activate/deactivate button
2. System toggles user's is_active status
3. System logs admin action
4. If deactivated, user is logged out on next request

### UC-4.4: Manage Service Categories

**Actor**: Admin
**Description**: CRUD operations on service categories.
**Preconditions**: User is logged in as admin
**Flows**:

- **Create**: Admin adds new category with name and description
- **Update**: Admin modifies existing category
- **Delete**: Admin removes category (cascades to services)

### UC-4.5: Manage Services

**Actor**: Admin
**Description**: CRUD operations on services.
**Preconditions**: User is logged in as admin
**Flows**:

- **Create**: Admin adds new service with category, name, price, duration
- **Update**: Admin modifies existing service
- **Delete**: Admin removes service

### UC-4.6: Moderate Ratings

**Actor**: Admin
**Description**: Review and hide inappropriate ratings.
**Preconditions**: User is logged in as admin
**Flow**:

1. Admin navigates to Ratings page
2. System displays all ratings
3. Admin reviews rating content
4. If inappropriate, admin clicks "Hide"
5. System sets is_visible = false (admin hides rating)
6. Rating no longer affects provider's average

---

## Use Case Diagram Summary

```
                    ┌─────────────────────────────────────────┐
                    │         Home Services Platform          │
                    └─────────────────────────────────────────┘
                                       │
        ┌──────────────────────────────┼──────────────────────────────┐
        │                              │                              │
   ┌────▼────┐                   ┌─────▼─────┐                  ┌─────▼─────┐
   │  Guest  │                   │ Customer  │                  │ Provider  │
   └────┬────┘                   └─────┬─────┘                  └─────┬─────┘
        │                              │                              │
   ┌────┴────────┐              ┌──────┴──────┐              ┌────────┴────────┐
   │ Browse      │              │ Dashboard   │              │ Dashboard       │
   │ Search      │              │ Book        │              │ Profile         │
   │ View Profile│              │ Cancel      │              │ Services        │
   │ Register    │              │ Rate        │              │ Time Slots      │
   └─────────────┘              │ View History│              │ Accept/Reject   │
                                └─────────────┘              │ Complete        │
                                                             └─────────────────┘
                                       │
                                 ┌─────▼─────┐
                                 │   Admin   │
                                 └─────┬─────┘
                                       │
                               ┌───────┴───────┐
                               │ Dashboard     │
                               │ Manage Users  │
                               │ Categories    │
                               │ Services      │
                               │ Moderate      │
                               └───────────────┘
```

---

## Non-Functional Requirements

### Security

- All passwords hashed with bcrypt
- CSRF protection on all forms
- Role-based access control
- Active user verification

### Performance

- Pagination on all list views (15 items default)
- Indexed database queries
- Eager loading of relationships

### Usability

- Responsive design (Bootstrap 5)
- Consistent navigation patterns
- Clear error messages
- Flash messages for feedback

### Maintainability

- Separation of concerns (MVC + Service + Repository)
- Consistent code style
- Documented architecture
