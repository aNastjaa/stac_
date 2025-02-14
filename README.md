
# **STAC – The Ultimate Creative Platform for Artists** 🎨✨  

## **Introduction**  
**STAC** is an exclusive platform for artists, designed to foster creativity through themed challenges and sponsor collaborations.  
Similar to platforms like **Behance**, **Dribbble**, or **Pinterest**, STAC enables users to **post, share, and showcase their work** within a creative community.  

However, the core distinction lies in the unique **monthly theme requirement**:  
> Every month, a new **specific theme** is introduced, and users must submit artwork related to that theme to be featured and published.  

---

## Table of Contents
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Usage](#usage)
4. [Configuration](#configuration)
5. [Database Structure](#database-structure)
6. [API Endpoints](#api-endpoints)
7. [Role-Based Access](#role-based-access)
8. [Authentication Method](#authentication-method)
9. [CSRF Token Handling](#CSRF-token-handling)
10. [Testing with Postman](#testing-with-postman)
11. [Troubleshooting](#troubleshooting)
12. [Contribution](#contribution)
13. [License](#license)
14. [Contact & Support](#contact--support)

---

## Requirements

- PHP 8.3.11
- Laravel 11.23.5
- Composer
- MySQL

---

## Installation

1. Clone the repository:

   git clone https://github.com/aNastjaa/stac_.git

2. Install dependencies:
   Once you've cloned the repository, navigate to the project folder:

   cd stac-backend

   Install the necessary dependencies using Composer:

   composer install

***Potential Issues:***

If you encounter issues with Composer (e.g., missing php or composer commands), ensure that both PHP (8.3.11 or higher) and Composer are correctly installed.
Run composer diagnose to check for common setup issues.

Tip: If you're using Docker or have PHP in a custom location, you may need to update your environment paths.

3. Copy `.env.example` to `.env`:

   cp .env.example .env

   This file contains essential environment configurations. Make sure to update any necessary settings like database credentials or API keys.

***Potential Issues:***

Make sure your .env file is correctly configured for your environment. For example:
-DB_CONNECTION should be set to mysql.
-DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and DB_PASSWORD should reflect your local database settings.

If you're working on a different environment (e.g., Docker or remote servers), the .env file may need more adjustments.

4. Generate the application key:

   php artisan key:generate

***Potential Issues:***

If this command doesn't work, it could be due to missing or incorrect PHP installation or lack of necessary permissions on the project directory. Ensure your web server has access to write to the .env file.

5. Configure the Database: 

   Open your .env file and configure the database connection section. For example:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stac
DB_USERNAME=root
DB_PASSWORD=

6. Configure your database in `.env` and run migrations:

   php artisan migrate --seed
   
***Potential Issues:***

If you encounter issues with migrations, ensure that your database user has sufficient privileges to create and modify tables.

If the migration fails, check the migration files for any incorrect assumptions about the database schema.

You can also manually clear any existing migration cache with:

php artisan config:clear
php artisan cache:clear

7. Start the server:

   php artisan serve

This will start the server at http://localhost:8000. Visit that URL in your browser to confirm everything is working.

***Potential Issues:***

If php artisan serve fails due to permissions, ensure you have access to the ports or try specifying a different port:

php artisan serve --port=8080

Ensure your PHP version meets the minimum requirement (PHP 8.3.11) and is correctly installed by running:

php -v

---

## Usage

1. **Register and Login**: Register a new user or log in with existing credentials.
2. **User Profile**: Manage user profile details.
3. **Upload Avatars/Brand Logos**: Upload user avatars and sponsor brand logos.
4. **Artworks**: Users can create, view, edit, and delete their artwork submissions.
5. **Sponsor Challenges**: Access sponsor challenges, submit artwork, and vote on other submissions.
6. **Admin Dashboard**: Admins manage user roles, submissions, themes, and more.

---

## Configuration

### 1. **Database Configuration (MySQL)**

For local development, ensure the following environment settings are configured in your `.env` file:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stac
DB_USERNAME=root
DB_PASSWORD=6789032

### 2. **Database Migrations**

All required database migrations already exist in the Laravel project. To apply the migrations, run the following command:

php artisan migrate

## Database Structure

1. users
Description: Stores basic registration information.
Columns:
id: Primary Key, UUID
username: Unique, required
email: Unique, required
password: Required
role_id: Foreign Key to roles table, default: basic user
created_at: Timestamp
updated_at: Timestamp

2. user_profiles
Description: Stores additional profile information that users can add after registration.
Columns:
id: Primary Key, UUID
user_id: Foreign Key to users table
full_name: Optional
bio: Text
avatar_id: Foreign Key to uploads table, for profile image
external_links: JSON, for Pro users to add external portfolio links
created_at: Timestamp
updated_at: Timestamp

3. roles
Description: Stores different user roles (basic, pro, admin).
Columns:
id: Primary Key, UUID
name: e.g., "basic", "pro", "admin"
created_at: Timestamp
updated_at: Timestamp

4. posts
Description: Stores posts that users submit, containing images and descriptions.
Columns:
id: Primary Key, UUID
user_id: Foreign Key to users table
theme: Current theme associated with the artwork
image_file: String, for the post image
description: Text
created_at: Timestamp
updated_at: Timestamp

5. comments
Description: Stores comments that users write on posts, showing user avatars as thumbnails.
Columns:
id: Primary Key, UUID
user_id: Foreign Key to users table
post_id: Foreign Key to posts table
comment_text: Text
created_at: Timestamp
updated_at: Timestamp

6. likes
Description: Stores likes that users give to posts, showing user avatars as thumbnails.
Columns:
id: Primary Key, UUID
user_id: Foreign Key to users table
post_id: Foreign Key to posts table
created_at: Timestamp

7. sponsor_challenges
Description: Stores sponsor challenges created by the admin, which pro users can participate in.
Columns:
id: Primary Key, UUID
title: String
brief: Text, description of the challenge
brand_name: String, name of the sponsoring brand
brand_logo_id: Foreign Key to uploads table, for the brand logo added by admin
submission_deadline: Timestamp
created_at: Timestamp
updated_at: Timestamp

8. sponsor_submissions
Description: Stores submissions that pro users make to sponsor challenges.
Columns:
id: Primary Key, UUID
user_id: Foreign Key to users table
challenge_id: Foreign Key to sponsor_challenges table
image_file: String, the image of the submission
description: Text
created_at: Timestamp
updated_at: Timestamp

9. votes
Description: Stores votes from users for posts submitted to sponsor challenges.
Columns:
id: Primary Key, UUID
user_id: Foreign Key to users table
submission_id: Foreign Key to sponsor_submissions table
created_at: Timestamp

10. uploads
Description: Centralized table for managing all uploads (avatars, brand logos, etc.).
Columns:
id: Primary Key, UUID
file_url: String, the URL of the uploaded file
file_type: String, describing the type of file: 'avatar', 'brand_logo', etc.
created_at: Timestamp
updated_at: Timestamp

11. archive
Description: Stores archived posts (artworks).
Columns:
id: Primary Key, UUID
post_id: Foreign Key referencing posts (the artwork being archived)
moved_at: Timestamp when the artwork was archived
theme: Theme of the archived artwork
created_at: Timestamp
updated_at: Timestamp

12. themes
Description: Stores theme information for the admin to manage the current theme.
Columns:
id: Primary Key, UUID
theme_name: String, the name of the theme
start_date: Timestamp, indicating when the theme becomes active
created_at: Timestamp
updated_at: Timestamp


## API Endpoints

### Authentication Routes

- **POST** `/auth/register` - Register a new user.
- **POST** `/auth/login` - User login.

### Authenticated Routes (Require `auth:sanctum` middleware)

#### Authentication

- **POST** `/auth/logout` - Logout the user.

#### User Profile

- **POST** `/users/profile` - Create user profile.
- **GET** `/users/profile` - View user profile.
- **PUT** `/users/profile` - Update user profile.

#### Uploads

- **POST** `/uploads/avatar` - Upload a user's avatar.
- **POST** `/uploads/brand-logo` - Upload a brand logo.
- **GET** `/uploads` - View all uploads.
- **GET** `/uploads/{id}` - View a specific upload.
- **POST** `/uploads/{id}` - Update a specific upload.
- **DELETE** `/uploads/{id}` - Delete a specific upload.

#### Artworks (Posts)

- **POST** `/artworks` - Create a new artwork post.
- **PUT** `/artworks/{id}` - Update an artwork post.
- **GET** `/artworks` - View all artworks.
- **GET** `/artworks/{id}` - View a specific artwork.
- **DELETE** `/artworks/{id}` - Delete an artwork.

#### Theme

- **GET** `/themes` - View all themes.
- **GET** `/themes/{id}` - View a specific theme.

#### Comments

- **POST** `/artworks/{id}/comments` - Add a comment to an artwork.
- **PUT** `/artworks/{id}/comments/{commentId}` - Update a comment.
- **GET** `/artworks/{id}/comments` - View comments on an artwork.
- **DELETE** `/artworks/{id}/comments/{commentId}` - Delete a comment.

#### Likes

- **POST** `/artworks/{id}/likes` - Add a like to an artwork.
- **GET** `/artworks/{id}/likes` - View likes on an artwork.
- **DELETE** `/artworks/{id}/likes/{likeId}` - Remove a like.

#### Sponsor Challenges and Submissions

- **GET** `/sponsor-challenges` - View all sponsor challenges.
- **GET** `/sponsor-challenges/{id}` - View a specific sponsor challenge.

**Within Sponsor Challenges**

- **POST** `/sponsor-challenges/{challengeId}/submissions` - Submit artwork to a challenge (Pro users only).
- **GET** `/sponsor-challenges/{challengeId}/submissions` - View all submissions for a challenge.
- **GET** `/sponsor-challenges/{challengeId}/submissions/{submissionId}` - View a specific submission.
- **PUT** `/sponsor-challenges/{challengeId}/submissions/{submissionId}` - Update a submission (Pro users only).
- **DELETE** `/sponsor-challenges/{challengeId}/submissions/{submissionId}` - Delete a submission (Pro users only).

**Voting**

- **POST** `/sponsor-challenges/{challengeId}/submissions/{submissionId}/votes` - Vote on a submission.
- **GET** `/sponsor-challenges/{challengeId}/submissions/{submissionId}/votes` - View votes on a submission.
- **DELETE** `/sponsor-challenges/{challengeId}/submissions/{submissionId}/votes` - Remove a vote.

#### Archive

- **POST** `/archive/move` - Move artworks to the archive (Admin only).
- **GET** `/archive/posts` - View archived posts.

#### Admin Dashboard (Require `role:admin` middleware)

- **POST** `/admin/sponsor-challenges` - Create a new sponsor challenge.
- **PUT** `/admin/sponsor-challenges/{id}` - Update a sponsor challenge.
- **DELETE** `/admin/sponsor-challenges/{id}` - Delete a sponsor challenge.

**User Management**

- **POST** `/admin/users` - Create a new user.
- **GET** `/admin/users` - View all users.
- **PUT** `/admin/users/{id}/role` - Update user role.
- **DELETE** `/admin/users/{id}` - Delete a user.

**Approvals and Themes**

- **PUT** `/admin/posts/{id}/status` - Approve/reject a post.
- **PUT** `/admin/sponsor-submissions/{id}/status` - Approve/reject a submission.
- **GET** `/admin/pending-posts` - View pending posts.
- **GET** `/admin/pending-submissions` - View pending submissions.
- **POST** `/admin/themes` - Create a theme.
- **PUT** `/admin/themes/{id}` - Update a theme.
- **DELETE** `/admin/themes/{id}` - Delete a theme.

---

## Role-Based Access

The project defines three roles:

- **Basic Users**: Can view and interact with all artworks, vote in challenges, and submit artwork for monthly themes.
- **Pro Users**: Have all basic permissions and can submit to sponsor challenges, link external portfolios, and view voting results.
- **Admins**: Full access to all features, including user management, content approval, and theme management.

---

## Authentication Method

This project uses **Laravel Sanctum** for token-based authentication, ensuring secure communication between the frontend and backend. Sanctum is implemented to handle user authentication for API routes. Here's how it's set up:

- **Sanctum Configuration**: Sanctum is configured to use cookies for CSRF protection and token-based authentication. The token is stored in the `Authorization` header when making requests to the API endpoints.
- **CSRF Protection**: CSRF protection is implemented to secure the frontend from cross-site request forgery attacks. The CSRF token is retrieved and stored in the frontend, which is then passed in the header of API requests. The CSRF token is refreshed and validated with each request.
- **Login Process**: 
  - **Basic User**: Basic users log in using their email and password, and receive an authentication token upon successful authentication.
  - **Pro User**: Pro users log in similarly to basic users, with additional privileges granted after authentication.
  - **Admin User**: The admin login uses predefined credentials:
    - **Admin email**: `admin@gmail.com`
    - **Admin password**: `AdminPassword2024`

To log in, users send a `POST` request to `/login` with their email and password. If the credentials are correct, the API will return an authentication token and user details, including the role associated with the user.

## CSRF Token Handling

- **Frontend CSRF Token**: 
  - The frontend makes a GET request to `/sanctum/csrf-cookie` to retrieve the CSRF token and set the cookie.
  - The CSRF token is then passed along with every API request to prevent CSRF attacks. The backend validates the token on each request, ensuring that the request comes from a trusted source.
  
- **Sanctum Token**:
  - After successful authentication, the backend sends a **Bearer Token** to the frontend, which is stored in the client's `localStorage` or `sessionStorage` (for authentication). This token is used for all future authenticated requests.

---

## Testing with Postman

1. Open Postman and click on `Import`.
2. Choose `Export` in Postman and save the collection as a `.json` file.
3. Import the `.json` file into Postman to use pre-configured endpoints.

---

## Troubleshooting

### 1. Database Connection Issues
Problem: Error connecting to the database, such as SQLSTATE[HY000] [1049] Unknown database.

Solution: Double-check the .env file to ensure your database settings are correct. Ensure that the database name, username, and password match what's configured in MySQL.

Example .env settings:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stac
DB_USERNAME=root
DB_PASSWORD=6789032

If the database doesn't exist, create it manually using:
mysql -u root -p
CREATE DATABASE stac;

### 2. Laravel Migration Issues

Problem: Error during migration like SQLSTATE[42S02]: Base table or view not found: 1146 Table 'stac.users' doesn't exist.

Solution: Run the migrations to set up your database tables:
php artisan migrate

If you encounter issues with migrations, you may need to rollback and run them again:
php artisan migrate:rollback
php artisan migrate

### 3. CORS Issues

Problem: CORS errors when making API requests (e.g., No 'Access-Control-Allow-Origin' header is present on the requested resource).

Solution: Check your .env file and ensure the CORS_ALLOWED_ORIGINS is correctly set to the URL where the frontend is hosted. For local development, it should look like this:

CORS_ALLOWED_ORIGINS=http://localhost:5173

### 4. Sanctum Authentication Issues

Problem: Unable to authenticate or Unauthenticated error when accessing protected routes.
 
Solution:
Make sure you have run the CSRF cookie setup by visiting the /sanctum/csrf-cookie endpoint before making any API requests.
Ensure that the API token is being passed in the Authorization header of the requests:

Authorization: Bearer <your_token>

### 5. File Upload Errors

Problem: Errors while uploading files (e.g., file not found, file type not allowed).

Solution:
Check if the storage is properly configured in .env. Ensure that the FILESYSTEM_DISK and FILESYSTEM_DRIVER are set as needed:

FILESYSTEM_DISK=local
FILESYSTEM_DRIVER=public

Run the following command to link the storage folder for public access:

php artisan storage:link

### 6. Permissions or Role Issues

Problem: Certain users (like basic users) are unable to access certain features or endpoints.

Solution:
Double-check that roles are properly assigned in the database (using the roles table).
Ensure that the correct middleware is being used in routes to restrict access based on roles. For example, only Pro users should be able to access sponsor challenges.
Verify the role of the authenticated user through the role_name in the response payload.

### 7. Postman Authorization Errors

Problem: Authorization errors when testing with Postman.

Solution:
Ensure that the CSRF cookie is retrieved first by sending a GET request to /sanctum/csrf-cookie before sending any other authenticated requests.
Include the correct Bearer token in the Authorization header.

## Contribution

This is a study project created for learning purposes. It is not intended for live deployment and is running only on a local host. Contributions are not expected at this time, but feel free to fork the project if you'd like to explore or experiment with the code.

---

## License

This project is a study project and is not licensed for public use. All rights reserved. Please do not use or distribute the code for commercial purposes.

---

## Contact & Support

If you have any questions or need support, you can reach out through the following channels:

- **Email**: [caramelevaa@gmail.com](mailto:caramelevaa@gmail.com)
- **GitHub**: [https://github.com/aNastjaa](https://github.com/aNastjaa)

 I'm happy to assist you!
