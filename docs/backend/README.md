# Media Manager Backend Documentation

## Overview
This document provides an overview of the backend API, database schema, authentication, and usage examples for developers integrating with the Media Manager backend.

---

## Table of Contents
- [API Endpoints](#api-endpoints)
- [Authentication](#authentication)
- [Database Schema](#database-schema)
- [Error Handling](#error-handling)
- [Setup & Initialization](#setup--initialization)
- [Example Requests](#example-requests)
- [Postman Collection](#postman-collection)

---

## API Endpoints

### Auth
- `POST /api/auth/login` — Login and receive JWT
- `POST /api/auth/logout` — Logout (destroy session)
- `GET /api/auth/user` — Get current user info

### Media
- `GET /api/media` — List all media
- `GET /api/media/{id}` — Get single media item
- `POST /api/media` — Add new media (admin)
- `PUT /api/media/{id}` — Update media (admin)
- `DELETE /api/media/{id}` — Delete media (admin)

### Ratings
- `POST /api/media/{id}/rate` — Rate a media item
- `GET /api/media/{id}/rating` — Get current user's rating for a media item

### Analytics
- `POST /api/analytics/start` — Track playback start
- `POST /api/analytics/end` — Track playback end
- `POST /api/analytics/skip` — Track skip event
- `GET /api/analytics` — Get analytics data

### Admin
- `GET /api/admin/users` — List users
- `POST /api/admin/users` — Create user
- `PUT /api/admin/users/{id}` — Update user
- `DELETE /api/admin/users/{id}` — Delete user
- `GET /api/admin/dashboard` — Get admin dashboard stats

---

## Authentication
- JWT-based authentication for API endpoints.
- Pass JWT in `Authorization: Bearer <token>` header for protected routes.

---

## Database Schema
- See `database/*.sql` files for table definitions.
- Tables: `users`, `media`, `ratings`, `analytics`.

---

## Error Handling
- All responses are JSON.
- On error: `{ "error": "Message" }` with appropriate HTTP status code.

---

## Setup & Initialization
1. Configure database in `config/database.php`.
2. Run `php database/init_db.php` to initialize tables and admin user.
3. Serve backend via Apache (see `.htaccess`).

---

## Example Requests

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "username": "admin",
  "password": "your_password"
}
```

### List Media
```http
GET /api/media
Authorization: Bearer <token>
```

### Add Media (Admin)
```http
POST /api/media
Authorization: Bearer <token>
Content-Type: application/json

{
  "title": "Sample Video",
  "type": "video",
  "file_path": "/uploads/media/video.mp4"
}
```

---

## Postman Collection
See `postman_collection.json` in this folder for ready-to-import API requests.
