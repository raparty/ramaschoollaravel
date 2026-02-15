# Transport Module Documentation

## Overview

The Transport Module is a comprehensive system for managing school transportation, including routes, vehicles, and student assignments. It is built using Laravel 10 and follows the existing codebase patterns.

## Features

### 1. Route Management
- **Create Routes**: Define transport routes with destinations and monthly costs
- **Edit Routes**: Update route information including name and cost
- **Delete Routes**: Remove routes (only if no students are assigned)
- **Search Routes**: Find routes by name
- **View All Routes**: List all available transport routes with pagination

### 2. Vehicle Management
- **Add Vehicles**: Register school buses and vans with vehicle numbers and seating capacity
- **Assign Routes**: Link vehicles to one or more routes they serve
- **Edit Vehicles**: Update vehicle details and route assignments
- **Delete Vehicles**: Remove vehicles (only if no students are assigned)
- **Seat Availability**: Track occupied and available seats in each vehicle
- **Search Vehicles**: Find vehicles by vehicle number

### 3. Student Transport Assignments
- **Assign Students**: Link students to specific routes and vehicles for academic sessions
- **Edit Assignments**: Update student route or vehicle assignments
- **Remove Assignments**: Unassign students from transport
- **Filter & Search**: Find assignments by student, route, class, or vehicle
- **Session-based**: Manage assignments for different academic sessions

### 4. Dashboard
- Interactive dashboard with navigation cards for easy access to all modules
- Quick statistics showing total routes, vehicles, and students using transport

## Database Structure

### Tables Created

#### 1. `transport_add_route`
Stores transport route information:
- `route_id` (PK): Auto-incrementing ID
- `route_name`: Name/destination of the route
- `cost`: Monthly transport fee (decimal)
- `created_at`: Timestamp of creation

#### 2. `transport_add_vechile`
Stores vehicle information (note: legacy spelling maintained):
- `vechile_id` (PK): Auto-incrementing ID
- `vechile_no`: Vehicle registration number
- `route_id`: Comma-separated route IDs (text field for legacy compatibility)
- `no_of_seats`: Total seating capacity

#### 3. `transport_student_detail`
Stores student transport assignments:
- `id` (PK): Auto-incrementing ID
- `registration_no`: Student registration number (FK to admissions)
- `route_id`: Assigned route (FK to transport_add_route)
- `vechile_id`: Assigned vehicle (FK to transport_add_vechile)
- `class_id`: Student's class (optional)
- `stream_id`: Student's stream (optional)
- `session`: Academic session year

## Models

### 1. TransportRoute
- Located: `app/Models/TransportRoute.php`
- Primary Key: `route_id`
- Relationships:
  - `studentAssignments()`: Has many student assignments
- Scopes:
  - `ordered()`: Order routes by name
  - `search($term)`: Search by route name
- Accessors:
  - `formatted_cost`: Returns formatted currency string

### 2. TransportVehicle
- Located: `app/Models/TransportVehicle.php`
- Primary Key: `vechile_id`
- Relationships:
  - `studentAssignments()`: Has many student assignments
  - `routes()`: Get associated route objects
- Scopes:
  - `ordered()`: Order by vehicle number
  - `search($term)`: Search by vehicle number
- Accessors:
  - `available_seats`: Calculate available seats
  - `route_names`: Get comma-separated route names

### 3. TransportStudentAssignment
- Located: `app/Models/TransportStudentAssignment.php`
- Primary Key: `id`
- Relationships:
  - `student()`: Belongs to Admission
  - `route()`: Belongs to TransportRoute
  - `vehicle()`: Belongs to TransportVehicle
  - `classModel()`: Belongs to ClassModel
- Scopes:
  - `forSession($session)`: Filter by academic session
  - `forStudent($regNo)`: Filter by student
  - `forRoute($routeId)`: Filter by route
  - `forVehicle($vehicleId)`: Filter by vehicle
  - `forClass($classId)`: Filter by class

## Controllers

### 1. TransportRouteController
- Located: `app/Http/Controllers/TransportRouteController.php`
- Methods:
  - `index()`: List all routes
  - `create()`: Show route creation form
  - `store()`: Save new route
  - `edit()`: Show route edit form
  - `update()`: Update existing route
  - `destroy()`: Delete route (with validation)

### 2. TransportVehicleController
- Located: `app/Http/Controllers/TransportVehicleController.php`
- Methods:
  - `index()`: List all vehicles
  - `create()`: Show vehicle creation form
  - `store()`: Save new vehicle
  - `edit()`: Show vehicle edit form
  - `update()`: Update existing vehicle
  - `destroy()`: Delete vehicle (with validation)

### 3. TransportStudentController
- Located: `app/Http/Controllers/TransportStudentController.php`
- Methods:
  - `index()`: List all student assignments with filters
  - `create()`: Show assignment creation form
  - `store()`: Save new assignment
  - `edit()`: Show assignment edit form
  - `update()`: Update existing assignment
  - `destroy()`: Remove assignment

## Routes

All transport routes are prefixed with `/transport` and use the `transport.*` name prefix.

### Route Management
- `GET /transport/routes` - List routes
- `GET /transport/routes/create` - Create route form
- `POST /transport/routes` - Store new route
- `GET /transport/routes/{route}/edit` - Edit route form
- `PUT /transport/routes/{route}` - Update route
- `DELETE /transport/routes/{route}` - Delete route

### Vehicle Management
- `GET /transport/vehicles` - List vehicles
- `GET /transport/vehicles/create` - Create vehicle form
- `POST /transport/vehicles` - Store new vehicle
- `GET /transport/vehicles/{vehicle}/edit` - Edit vehicle form
- `PUT /transport/vehicles/{vehicle}` - Update vehicle
- `DELETE /transport/vehicles/{vehicle}` - Delete vehicle

### Student Assignment Management
- `GET /transport/students` - List assignments
- `GET /transport/students/create` - Create assignment form
- `POST /transport/students` - Store new assignment
- `GET /transport/students/{student}/edit` - Edit assignment form
- `PUT /transport/students/{student}` - Update assignment
- `DELETE /transport/students/{student}` - Remove assignment

## Views

All views are located in `resources/views/transport/`:

### Main Dashboard
- `index.blade.php`: Transport module dashboard with navigation cards and statistics

### Route Views
- `routes/index.blade.php`: List all routes with search
- `routes/create.blade.php`: Create new route form
- `routes/edit.blade.php`: Edit route form

### Vehicle Views
- `vehicles/index.blade.php`: List all vehicles with search
- `vehicles/create.blade.php`: Create new vehicle form with route selection
- `vehicles/edit.blade.php`: Edit vehicle form with route selection

### Student Assignment Views
- `students/index.blade.php`: List assignments with filters (route, class, search)
- `students/create.blade.php`: Create new assignment form
- `students/edit.blade.php`: Edit assignment form

## Installation & Setup

### 1. Run Migration
```bash
php artisan migrate
```

This will create the three transport tables with proper structure and indexes.

### 2. Access the Module
Navigate to `/transport` in your browser to access the transport dashboard.

### 3. Setup Workflow
1. **Create Routes First**: Add all transport routes with destinations and costs
2. **Add Vehicles**: Register school buses/vans and assign them to routes
3. **Assign Students**: Link students to routes and vehicles for the current session

## Key Features & Validations

### Route Management
- Route names must be unique
- Cost must be a positive decimal value
- Routes with assigned students cannot be deleted

### Vehicle Management
- Vehicle numbers must be unique
- Seating capacity must be between 1-100
- Vehicles can be assigned to multiple routes
- Vehicles with assigned students cannot be deleted
- Real-time seat availability tracking

### Student Assignments
- Students cannot have duplicate assignments in the same session
- Assignment requires valid student, route, and vehicle
- Route determines the monthly transport fee
- Session-based filtering for easy management

## Integration with Existing Systems

### Integration with Student Module
- Uses `Admission` model for student information
- Links via `registration_no` field
- Displays student names in assignment lists

### Integration with Class Module
- Optional class tracking for assignments
- Uses `ClassModel` for class information
- Helps in filtering and reporting

### Integration with Fee Module
- `StudentTransportFee` model exists for fee collection
- Can be linked with route costs for automated fee calculation

## UI/UX Features

### Bootstrap 5 Design
- Consistent with existing application design
- Responsive layout for mobile devices
- Professional card-based navigation
- Clear action buttons and icons

### User Experience
- Intuitive dashboard with clear navigation
- Search and filter capabilities on all list pages
- Pagination for large datasets
- Confirmation dialogs for delete operations
- Success/error messages for all actions
- Breadcrumb navigation for easy tracking
- Form validation with helpful error messages

## Future Enhancements

Potential areas for expansion:
1. **Driver Management**: Add driver information and assignments
2. **Fee Collection Integration**: Link with fee module for automated billing
3. **Attendance Tracking**: Track student boarding/deboarding
4. **GPS Tracking**: Real-time vehicle location tracking
5. **Parent Notifications**: SMS/email alerts for transport updates
6. **Reports**: Generate transport utilization and fee collection reports
7. **Route Optimization**: Suggest optimal routes based on student locations

## Legacy PHP Files

The module is based on legacy PHP files found in `legacy_php/`:
- `transport.php` - Original transport dashboard
- `transport_route_detail.php` - Route management
- `transport_vechile_detail.php` - Vehicle management
- `transport_student_detail.php` - Student assignment management
- `includes/transport_setting_sidebar.php` - Navigation sidebar

The new Laravel module maintains backward compatibility with the database structure used by these legacy files.

## Support & Maintenance

For questions or issues with the transport module:
1. Check this documentation first
2. Review the controller code for business logic
3. Check model relationships for data access patterns
4. Verify database connections and migrations

## Version History

- **v1.0.0** (2026-02-15): Initial release with complete CRUD functionality for routes, vehicles, and student assignments
