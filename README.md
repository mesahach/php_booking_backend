# Booking API

This is a robust and scalable REST API for a booking system, built with PHP. It's designed to showcase modern PHP development practices, including a clean architecture, secure authentication, and a comprehensive set of features.

## Features

- **User Management:** Secure user registration, login, profile updates, and password management.
- **Authentication:** JWT-based authentication to protect routes and manage user sessions.
- **Booking Management:** Create, view, update, and cancel bookings.
- **Review System:** Users can leave reviews for services or products.
- **Admin Functionality:** Separate endpoints for administrative tasks.
- **And more:** The API also includes endpoints for managing celebrities, food items, products, and transactions.

## Technologies Used

This project leverages a variety of modern PHP libraries and tools to ensure high quality and reliability:

- **Core:** PHP 8.2
- **Dependency Management:** Composer
- **Authentication:** `firebase/php-jwt` for JSON Web Tokens.
- **Database:** `gabordemooij/redbean` for easy ORM.
- **Validation:** `respect/validation` for robust data validation.
- **HTTP Client:** `guzzlehttp/guzzle` for making HTTP requests.
- **Email:** `phpmailer/phpmailer` for sending emails.
- **Environment Variables:** `vlucas/phpdotenv` to manage environment variables.
- **Error Handling:** `filp/whoops` for pretty error reporting.
- **2FA:** `vectorface/googleauthenticator` for two-factor authentication.
- **And many more...**

## API Endpoints

The API is organized into resources, with each resource having a set of actions. Here's a high-level overview of the available endpoints:

- `api/user/...`
- `api/admin/...`
- `api/booking/...`
- `api/celebrity/...`
- `api/fooditem/...`
- `api/product/...`
- `api/review/...`
- `api/transaction/...`

Each endpoint supports `GET`, `POST`, `PUT`, and `DELETE` requests for various actions like creating, reading, updating, and deleting resources.

## Getting Started

1.  **Clone the repository:**
    ```bash
    git clone <your-repo-url>
    ```
2.  **Install dependencies:**
    ```bash
    composer install
    ```
3.  **Set up your environment variables:**
    - Create a `.env` file by copying `.env.dist`.
    - Fill in the necessary database and other configuration details.
4.  **Set up your web server:**
    - Point your web server's document root to the `api` directory.

## Showcase of Skills

This project demonstrates a strong understanding of:

- **Object-Oriented PHP:** The codebase is well-structured with classes, namespaces, and a clear separation of concerns.
- **RESTful API Design:** The API follows REST principles for creating a clean and predictable interface.
- **Modern PHP Practices:** Use of modern PHP features, autoloading, and dependency management.
- **Security:** Implementation of JWT-based authentication and secure password handling.
- **Database Management:** Use of an ORM for efficient database interactions.

This project is a testament to my ability to build high-quality, scalable, and maintainable PHP applications.
