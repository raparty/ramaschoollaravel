# Quick Start Guide - Transport Driver & Vehicle Management

## 🎯 Overview
This guide helps you quickly get started with the new driver and vehicle management features in the transport module.

## 🚀 Getting Started

### 1. Run Database Migrations
Before using the new features, run the migrations:
```bash
php artisan migrate
```

This will create the `transport_drivers` table and add new columns to the `transport_add_vechile` table.

## 📋 Common Tasks

### Adding a New Driver

1. **Navigate to Drivers**
   - Go to "Transport" in the main menu
   - Click on "Drivers" card, OR
   - Go directly to `/transport/drivers`

2. **Create Driver**
   - Click "Add New Driver" button
   - Fill in the form:
     * **Driver Name** (Required)
     * **License Number** (Optional)
     * **Aadhar Number** (Optional)
     * **Contact Number** (Optional)
     * **Address** (Optional)
     * **Status** (Required: Active/Inactive)
   - Click "Save Driver"

3. **Success!**
   - Driver is added and appears in the drivers list
   - Ready to be assigned to vehicles

### Assigning a Driver to a Vehicle

#### Method 1: When Creating a New Vehicle
1. Go to Transport → Vehicles → "Add New Vehicle"
2. Fill in basic vehicle details (number, seats)
3. In "Assign Driver" dropdown, select the driver
4. Optionally fill in insurance and permit details
5. Click "Save Vehicle"

#### Method 2: When Editing an Existing Vehicle
1. Go to Transport → Vehicles
2. Click "Edit" on any vehicle
3. Select driver from "Assign Driver" dropdown
4. Update other details if needed
5. Click "Update Vehicle"

### Adding Vehicle Documentation (Insurance & Permit)

1. **While Creating/Editing a Vehicle**
   - Scroll to "Vehicle Documentation" section
   - Fill in:
     * **Insurance Number** (e.g., POL-123456789)
     * **Insurance Expiry Date**
     * **Permit Number** (e.g., PER-987654321)
     * **Permit Expiry Date**
   - All fields are optional
   - Click Save/Update

### Viewing Vehicle-Driver Assignments

#### From Vehicles Page
1. Go to Transport → Vehicles
2. The list shows:
   - Vehicle number
   - **Driver name and contact** (or "No driver assigned")
   - Routes, seats, availability
3. Filter using search box

#### From Drivers Page
1. Go to Transport → Drivers
2. The list shows:
   - Driver details
   - **Number of assigned vehicles**
   - **Vehicle numbers** (listed below the count)
3. Filter by:
   - Search (name, license, contact)
   - Status (Active/Inactive)

### Unassigning a Driver from a Vehicle

1. Go to Transport → Vehicles
2. Click "Edit" on the vehicle
3. In "Assign Driver" dropdown, select "-- Select Driver --"
4. Click "Update Vehicle"
5. Vehicle is now unassigned

### Editing Driver Details

1. Go to Transport → Drivers
2. Click "Edit" on the driver you want to update
3. Modify any fields
4. Click "Update Driver"
5. **Note**: If vehicles are assigned, they remain assigned after update

### Deleting a Driver

1. Go to Transport → Drivers
2. Click "Delete" on the driver
3. Confirm deletion
4. **Important**: Cannot delete if vehicles are assigned
   - First unassign all vehicles
   - Then delete the driver

### Setting a Driver as Inactive

If you want to keep driver records but mark them as unavailable:
1. Edit the driver
2. Change "Status" to "Inactive"
3. Save changes
4. Inactive drivers still appear in listings but can be filtered out
5. Inactive drivers don't appear in vehicle assignment dropdown

## 🔍 Searching and Filtering

### Drivers
- **Search**: Enter name, license number, or contact number
- **Status Filter**: Select "Active" or "Inactive"
- Click "Search" to apply filters
- Click "Clear" to reset

### Vehicles
- **Search**: Enter vehicle number
- Click "Search" to apply
- Click "Clear" to reset

## 📊 Dashboard Overview

The Transport Dashboard shows:
- **Routes** count
- **Drivers** count (new!)
- **Vehicles** count
- **Students** count

Click on any card to go to that section.

## ⚠️ Important Notes

### Data Validation
- Driver name is required when creating a driver
- Vehicle number is required and must be unique
- All other fields are optional
- Date fields must be valid dates if provided

### Relationships
- One driver can be assigned to multiple vehicles
- One vehicle can have only one driver at a time
- Students are assigned to vehicles (not drivers directly)

### Deletion Protection
- Cannot delete a driver with assigned vehicles
  * Solution: First unassign all vehicles, then delete
- Cannot delete a vehicle with assigned students
  * Solution: First remove student assignments, then delete

### Legacy Compatibility
- Table name `transport_add_vechile` maintains legacy spelling
- Field name `vechile_id` maintains legacy spelling
- This ensures compatibility with existing legacy code

## 🎨 UI Features

### Icons
- 👨‍✈️ Drivers section icon
- 🚌 Vehicles section icon
- ✅ Active status badge (green)
- ⚪ Inactive status badge (gray)
- 🔵 Vehicle count badges (blue)

### Color Coding
- **Green**: Active drivers, available seats
- **Blue**: Informational badges
- **Red**: Delete buttons, full capacity
- **Yellow**: Low availability warnings

## 🔐 Permissions
Standard Laravel authentication and authorization applies. Ensure users have appropriate permissions to:
- View transport module
- Create/edit drivers
- Create/edit vehicles
- Delete records

## 📱 Responsive Design
All views are responsive and work on:
- Desktop browsers
- Tablets
- Mobile devices (with appropriate layout adjustments)

## 🆘 Troubleshooting

### "Driver not showing in dropdown"
- Check if driver status is "Active"
- Only active drivers appear in vehicle assignment dropdown

### "Cannot delete driver"
- Check if driver has vehicles assigned
- Unassign all vehicles first

### "Date not saving properly"
- Ensure date format is YYYY-MM-DD
- Use the date picker in the form

### "Vehicle not showing driver"
- Refresh the page
- Check if driver_id is properly saved in database
- Ensure driver still exists and is not deleted

## 💡 Tips

1. **Keep Records Updated**: Regularly update insurance and permit expiry dates
2. **Use Status Wisely**: Mark drivers as inactive instead of deleting to maintain history
3. **Search Efficiently**: Use the search features to quickly find drivers or vehicles
4. **Regular Audits**: Review vehicle-driver assignments periodically
5. **Documentation**: Keep insurance and permit numbers accurate for compliance

## 📞 Support

For additional help or to report issues:
1. Check the detailed documentation in `TRANSPORT_DRIVER_VEHICLE_ENHANCEMENT.md`
2. Review database schema in `TRANSPORT_DATABASE_SCHEMA.md`
3. Contact your system administrator
