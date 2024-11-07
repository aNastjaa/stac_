
# STAC

**STAC** is a creative platform that provides artists with a unique space to showcase their artwork through monthly themes and sponsor collaborations. Inspired by platforms like **Behance**, **Dribbble**, and **Pinterest**, STAC emphasizes monthly thematic challenges to inspire creativity and engage users in sponsor-sponsored challenges.

---

## Table of Contents
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Usage](#usage)
4. [API Endpoints](#api-endpoints)
5. [Role-Based Access](#role-based-access)
6. [Testing with Postman](#testing-with-postman)

---

## Requirements

- PHP 8.3.11
- Laravel 11.23.5
- Composer
- MySQL

---

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/aNastjaa/stac.git
   ```

2. Install dependencies:

   ```bash
   cd stac
   composer install
   ```

3. Copy `.env.example` to `.env`:

   ```bash
   cp .env.example .env
   ```

4. Generate the application key:

   ```bash
   php artisan key:generate
   ```

5. Configure your database in `.env` and run migrations:

   ```bash
   php artisan migrate --seed
   ```

6. Start the server:

   ```bash
   php artisan serve
   ```

---

## Usage

1. **Register and Login**: Register a new user or log in with existing credentials.
2. **User Profile**: Manage user profile details.
3. **Upload Avatars/Brand Logos**: Upload user avatars and sponsor brand logos.
4. **Artworks**: Users can create, view, edit, and delete their artwork submissions.
5. **Sponsor Challenges**: Access sponsor challenges, submit artwork, and vote on other submissions.
6. **Admin Dashboard**: Admins manage user roles, submissions, themes, and more.

---

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

## Testing with Postman

1. Open Postman and click on `Import`.
2. Choose `Export` in Postman and save the collection as a `.json` file.
3. Import the `.json` file into Postman to use pre-configured endpoints.

---

## License

This project is licensed under the MIT License.
