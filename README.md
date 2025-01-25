# Car-Rental

## Overview
This is a web-based car rental application built using PHP. It allows users to browse available cars, filter them based on various criteria, view detailed information about each car, and book cars for specific dates. The application also includes an admin interface for managing the car database.

## Features

### User Features
- **Registration and Login**: Users can register and log in to the application.
- **Car Browsing**: Users can browse available cars and filter them based on brand, transmission type, price range, and number of passengers.
- **Car Details**: Users can view detailed information about each car.
- **Booking**: Logged-in users can book cars for specific dates.
- **Booking Confirmation**: Users receive confirmation of their bookings, including the total price.

### Admin Features
- **Car Management**: Admins can add, modify, and delete cars from the database.
- **Booking Management**: Admins can view all bookings and delete them if necessary.

## File Descriptions
- **README.md**: Contains the project overview, features, and file descriptions.
- **cars.json**: Stores the details of all available cars, including brand, model, year, transmission type, fuel type, number of passengers, daily price, and image URL.
- **users.json**: Stores user information, including full name, email, hashed password, and admin status.
- **bookings.json**: Stores booking information, including car ID, user ID, start date, end date, and total price.
- **authorisation.php**: Handles user registration and login. Validates user input, checks for existing users, and manages user sessions.
- **book.php**: Handles car bookings. Validates booking dates, checks for availability, calculates the total price, and stores the booking information.
- **admin.php**: Provides an interface for admins to manage the car database. Admins can add new cars, modify existing cars, and delete cars.
- **index.php**: The homepage of the application. Displays available cars and provides filtering options. Also includes links for user login and admin car management.
- **details.php**: Displays detailed information about a specific car. Allows logged-in users to book the car.
- **logout.php**: Logs out the user by destroying the session and redirects to the homepage.

## Installation
1. Clone the repository to your local machine.
2. Ensure you have a web server with PHP support (e.g., XAMPP, WAMP).
3. Place the project files in the web server's root directory.
4. Start the web server and navigate to the project URL (e.g., `http://localhost/iKarRental`).

## Usage
1. **Register**: Navigate to the authorization page and register a new account.
2. **Login**: Log in with your registered credentials.
3. **Browse Cars**: Use the homepage to browse and filter available cars.
4. **View Details**: Click on a car to view its detailed information.
5. **Book a Car**: If logged in, book a car for specific dates.
6. **Admin Management**: If logged in as an admin, manage the car database through the admin interface.

## Future Features
- Homepage: filters include date ranges for availability 
- Profile Page: displays the user’s past bookings 
- Admin: logged-in admin’s profile page shows all bookings, with the option to delete them
- Admin: able to modify car data with error handling 


## Dependencies
- PHP
- JSON files for data storage
- Bootstrap for styling
