# MaskanLeb API Documentation

**Base URL:** `http://localhost:8000/api`  
**Auth:** Bearer Token (Laravel Sanctum)  
**Content-Type:** `application/json`  
**Accept:** `application/json`

---

## 📋 Table of Contents

1. [Authentication](#1-authentication)
2. [Profile](#2-profile)
3. [Judiciaries](#3-judiciaries)
4. [Apartments](#4-apartments)
5. [Favorites](#5-favorites)
6. [Messages](#6-messages)
7. [Reviews](#7-reviews)
8. [Reports](#8-reports)
9. [Owner Analytics](#9-owner-analytics)
10. [Admin](#10-admin)
11. [Data Schemas](#11-data-schemas)
12. [Error Responses](#12-error-responses)
13. [Enums & Constants](#13-enums--constants)

---

## 1. Authentication

### 1.1 Register

**POST** `/api/auth/register`  
🔓 Public

**Request Body:**
```json
{
  "first_name": "Ahmad",
  "last_name": "Khalil",
  "email": "ahmad@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "phone": "+961 70 123456",
  "role": "renter"
}
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `first_name` | string | ✅ | max:100 |
| `last_name` | string | ✅ | max:100 |
| `email` | string | ✅ | valid email, unique |
| `password` | string | ✅ | min:8, confirmed |
| `password_confirmation` | string | ✅ | must match password |
| `phone` | string | ❌ | max:20 |
| `role` | string | ❌ | `renter` or `owner` (default: `renter`) |

**Response `201`:**
```json
{
  "message": "Registration successful. Please verify your email.",
  "user": {
    "id": 1,
    "first_name": "Ahmad",
    "last_name": "Khalil",
    "full_name": "Ahmad Khalil",
    "email": "ahmad@example.com",
    "phone": "+961 70 123456",
    "role": "renter",
    "is_verified": false,
    "is_banned": false,
    "profile_image": null,
    "email_verified_at": null,
    "created_at": "2026-03-03T10:00:00.000000Z"
  },
  "token": "1|abcXYZ123tokenstring..."
}
```

---

### 1.2 Login

**POST** `/api/auth/login`  
🔓 Public

**Request Body:**
```json
{
  "email": "ahmad@example.com",
  "password": "Password123!",
  "device_name": "React Web App"
}
```

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `email` | string | ✅ | |
| `password` | string | ✅ | |
| `device_name` | string | ❌ | Identifies the token (default: `api-token`) |

**Response `200`:**
```json
{
  "message": "Login successful.",
  "user": {
    "id": 1,
    "first_name": "Ahmad",
    "last_name": "Khalil",
    "full_name": "Ahmad Khalil",
    "email": "ahmad@example.com",
    "phone": "+961 70 123456",
    "role": "renter",
    "is_verified": false,
    "is_banned": false,
    "profile_image": null,
    "email_verified_at": "2026-03-03T10:05:00.000000Z",
    "created_at": "2026-03-03T10:00:00.000000Z"
  },
  "token": "2|newTokenString..."
}
```

**Error `422` (wrong credentials):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

**Error `403` (banned):**
```json
{
  "message": "Your account has been banned. Please contact support."
}
```

---

### 1.3 Logout

**POST** `/api/auth/logout`  
🔒 Requires Auth

**Headers:**
```
Authorization: Bearer {token}
```

**Response `200`:**
```json
{
  "message": "Logged out successfully."
}
```

---

### 1.4 Get Authenticated User

**GET** `/api/auth/user`  
🔒 Requires Auth

**Response `200`:**
```json
{
  "user": {
    "id": 1,
    "first_name": "Ahmad",
    "last_name": "Khalil",
    "full_name": "Ahmad Khalil",
    "email": "ahmad@example.com",
    "phone": "+961 70 123456",
    "role": "owner",
    "is_verified": true,
    "is_banned": false,
    "profile_image": "http://localhost:8000/storage/profile-images/abc123.jpg",
    "email_verified_at": "2026-03-03T10:05:00.000000Z",
    "created_at": "2026-03-03T10:00:00.000000Z"
  }
}
```

---

## 2. Profile

### 2.1 Get Profile

**GET** `/api/profile`  
🔒 Requires Auth

**Response `200`:**
```json
{
  "user": {
    "id": 1,
    "first_name": "Ahmad",
    "last_name": "Khalil",
    "full_name": "Ahmad Khalil",
    "email": "ahmad@example.com",
    "phone": "+961 70 123456",
    "role": "owner",
    "is_verified": true,
    "is_banned": false,
    "profile_image": "http://localhost:8000/storage/profile-images/abc123.jpg",
    "email_verified_at": "2026-03-03T10:05:00.000000Z",
    "created_at": "2026-03-03T10:00:00.000000Z"
  }
}
```

---

### 2.2 Update Profile

**POST** `/api/profile`  
🔒 Requires Auth  
**Content-Type:** `multipart/form-data` (when uploading image)

**Request Body:**
```json
{
  "first_name": "Ahmad",
  "last_name": "Khalil",
  "email": "newemail@example.com",
  "phone": "+961 71 999999"
}
```

> When uploading profile image, send as `multipart/form-data` with `profile_image` file field.

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `first_name` | string | ❌ | max:100 |
| `last_name` | string | ❌ | max:100 |
| `email` | string | ❌ | valid email, unique (excluding self) |
| `phone` | string | ❌ | max:20 |
| `profile_image` | file | ❌ | jpeg/png/jpg/webp, max 2MB |

**Response `200`:**
```json
{
  "message": "Profile updated successfully.",
  "user": {
    "id": 1,
    "first_name": "Ahmad",
    "last_name": "Khalil",
    "full_name": "Ahmad Khalil",
    "email": "newemail@example.com",
    "phone": "+961 71 999999",
    "role": "owner",
    "is_verified": true,
    "is_banned": false,
    "profile_image": "http://localhost:8000/storage/profile-images/new123.jpg",
    "email_verified_at": "2026-03-03T10:05:00.000000Z",
    "created_at": "2026-03-03T10:00:00.000000Z"
  }
}
```

---

### 2.3 Change Password

**PUT** `/api/profile/password`  
🔒 Requires Auth

**Request Body:**
```json
{
  "current_password": "OldPassword123!",
  "password": "NewPassword456!",
  "password_confirmation": "NewPassword456!"
}
```

**Response `200`:**
```json
{
  "message": "Password changed successfully."
}
```

---

### 2.4 Delete Account

**DELETE** `/api/profile`  
🔒 Requires Auth

**Request Body:**
```json
{
  "password": "Password123!"
}
```

**Response `200`:**
```json
{
  "message": "Account deleted successfully."
}
```

---

## 3. Judiciaries

### 3.1 List All Judiciaries

**GET** `/api/judiciaries`  
🔓 Public

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Baabda",
      "name_ar": "بعبدا",
      "apartments_count": 12
    },
    {
      "id": 2,
      "name": "Metn",
      "name_ar": "المتن",
      "apartments_count": 25
    },
    {
      "id": 3,
      "name": "Keserwan",
      "name_ar": "كسروان",
      "apartments_count": 8
    },
    {
      "id": 4,
      "name": "Beirut",
      "name_ar": "بيروت",
      "apartments_count": 47
    }
  ]
}
```

---

### 3.2 Get Apartments by Judiciary

**GET** `/api/judiciaries/{judiciary}/apartments`  
🔓 Public

**URL Params:** `judiciary` = judiciary ID

**Response `200`:**
```json
{
  "judiciary": {
    "id": 2,
    "name": "Metn",
    "name_ar": "المتن",
    "apartments_count": 25
  },
  "data": [ /* array of Apartment objects — see Apartment schema */ ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 25
  }
}
```

---

## 4. Apartments

### 4.1 List Apartments (Search & Filter)

**GET** `/api/apartments`  
🔓 Public

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `judiciary_id` | integer | Filter by judiciary |
| `min_price` | number | Minimum price in USD |
| `max_price` | number | Maximum price in USD |
| `rooms` | integer | Exact number of rooms |
| `furnished` | boolean | `true` for furnished only |
| `parking` | boolean | `true` for parking only |
| `status` | string | `available`, `rented`, `pending` (default: `available`) |
| `search` | string | Search in title and description |
| `min_size` | integer | Minimum size in m² |
| `max_size` | integer | Maximum size in m² |
| `sort_by` | string | `newest` (default), `lowest_price`, `highest_price`, `most_viewed` |
| `per_page` | integer | Items per page (default: 15) |
| `page` | integer | Page number |

**Example Request:**
```
GET /api/apartments?judiciary_id=2&min_price=300&max_price=1500&rooms=2&furnished=true&sort_by=lowest_price&per_page=10
```

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Modern 2 Bedroom Apartment in Metn",
      "description": "Beautiful fully furnished apartment with mountain view...",
      "price_usd": 650.00,
      "number_of_rooms": 2,
      "number_of_bathrooms": 1,
      "size_m2": 110,
      "furnished": true,
      "parking": true,
      "minimum_months": 6,
      "latitude": 33.8938,
      "longitude": 35.5018,
      "status": "available",
      "is_verified": true,
      "views_count": 142,
      "created_at": "2026-02-15T08:30:00.000000Z",
      "updated_at": "2026-02-20T14:00:00.000000Z",
      "favorites_count": 18,
      "reviews_count": 5,
      "average_rating": 4.2,
      "is_favorited": false,
      "owner": {
        "id": 3,
        "first_name": "Georges",
        "last_name": "Nassar",
        "full_name": "Georges Nassar",
        "email": "georges@example.com",
        "phone": "+961 3 456789",
        "role": "owner",
        "is_verified": true,
        "is_banned": false,
        "profile_image": "http://localhost:8000/storage/profile-images/georges.jpg",
        "email_verified_at": "2026-01-01T00:00:00.000000Z",
        "created_at": "2026-01-01T00:00:00.000000Z"
      },
      "judiciary": {
        "id": 2,
        "name": "Metn",
        "name_ar": "المتن",
        "apartments_count": null
      },
      "images": [
        {
          "id": 1,
          "image_url": "http://localhost:8000/storage/apartments/1/photo1.jpg",
          "sort_order": 0
        },
        {
          "id": 2,
          "image_url": "http://localhost:8000/storage/apartments/1/photo2.jpg",
          "sort_order": 1
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 48
  }
}
```

> **Note:** `is_favorited` is only present when the request is authenticated.

---

### 4.2 Show Single Apartment

**GET** `/api/apartments/{apartment}`  
🔓 Public

> Records a view on each call. Increments `views_count`.

**Response `200`:**
```json
{
  "data": {
    "id": 1,
    "title": "Modern 2 Bedroom Apartment in Metn",
    "description": "Beautiful fully furnished apartment with mountain view. The apartment features an open kitchen, spacious living room, and two large bedrooms.",
    "price_usd": 650.00,
    "number_of_rooms": 2,
    "number_of_bathrooms": 1,
    "size_m2": 110,
    "furnished": true,
    "parking": true,
    "minimum_months": 6,
    "latitude": 33.8938,
    "longitude": 35.5018,
    "status": "available",
    "is_verified": true,
    "views_count": 143,
    "created_at": "2026-02-15T08:30:00.000000Z",
    "updated_at": "2026-02-20T14:00:00.000000Z",
    "favorites_count": 18,
    "reviews_count": 5,
    "average_rating": 4.2,
    "is_favorited": null,
    "owner": {
      "id": 3,
      "first_name": "Georges",
      "last_name": "Nassar",
      "full_name": "Georges Nassar",
      "email": "georges@example.com",
      "phone": "+961 3 456789",
      "role": "owner",
      "is_verified": true,
      "is_banned": false,
      "profile_image": "http://localhost:8000/storage/profile-images/georges.jpg",
      "email_verified_at": "2026-01-01T00:00:00.000000Z",
      "created_at": "2026-01-01T00:00:00.000000Z"
    },
    "judiciary": {
      "id": 2,
      "name": "Metn",
      "name_ar": "المتن",
      "apartments_count": null
    },
    "images": [
      {
        "id": 1,
        "image_url": "http://localhost:8000/storage/apartments/1/photo1.jpg",
        "sort_order": 0
      },
      {
        "id": 2,
        "image_url": "http://localhost:8000/storage/apartments/1/photo2.jpg",
        "sort_order": 1
      }
    ]
  }
}
```

---

### 4.3 Create Apartment Listing

**POST** `/api/apartments`  
🔒 Requires Auth (owner or admin)  
**Content-Type:** `multipart/form-data`

**Request Fields:**

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `judiciary_id` | integer | ✅ | must exist |
| `title` | string | ✅ | max:255 |
| `description` | string | ✅ | max:5000 |
| `price_usd` | number | ✅ | min:0, decimal |
| `number_of_rooms` | integer | ✅ | min:1, max:50 |
| `number_of_bathrooms` | integer | ✅ | min:1, max:20 |
| `size_m2` | integer | ✅ | min:10, max:10000 |
| `furnished` | boolean | ❌ | default: false |
| `parking` | boolean | ❌ | default: false |
| `minimum_months` | integer | ❌ | min:1, max:120, default: 1 |
| `latitude` | number | ❌ | between -90 and 90 |
| `longitude` | number | ❌ | between -180 and 180 |
| `images[]` | file(s) | ❌ | jpeg/png/jpg/webp, max 5MB each, max 10 files |

**Example (JSON representation of form data):**
```json
{
  "judiciary_id": 2,
  "title": "Spacious 3 Bedroom in Metn",
  "description": "Beautiful apartment with full mountain view...",
  "price_usd": 900,
  "number_of_rooms": 3,
  "number_of_bathrooms": 2,
  "size_m2": 160,
  "furnished": true,
  "parking": true,
  "minimum_months": 6,
  "latitude": 33.8938,
  "longitude": 35.5018
}
```

**Response `201`:**
```json
{
  "message": "Apartment listed successfully.",
  "data": {
    "id": 10,
    "title": "Spacious 3 Bedroom in Metn",
    "description": "Beautiful apartment with full mountain view...",
    "price_usd": 900.00,
    "number_of_rooms": 3,
    "number_of_bathrooms": 2,
    "size_m2": 160,
    "furnished": true,
    "parking": true,
    "minimum_months": 6,
    "latitude": 33.8938,
    "longitude": 35.5018,
    "status": "available",
    "is_verified": false,
    "views_count": 0,
    "created_at": "2026-03-03T10:00:00.000000Z",
    "updated_at": "2026-03-03T10:00:00.000000Z",
    "favorites_count": null,
    "reviews_count": null,
    "average_rating": null,
    "is_favorited": null,
    "owner": { /* User object */ },
    "judiciary": { /* Judiciary object */ },
    "images": []
  }
}
```

---

### 4.4 Update Apartment

**PUT** `/api/apartments/{apartment}`  
🔒 Requires Auth (owner of listing or admin)  
**Content-Type:** `multipart/form-data` (if uploading new images)

> All fields are optional. Only send what you want to change.

| Field | Type | Notes |
|-------|------|-------|
| `judiciary_id` | integer | |
| `title` | string | max:255 |
| `description` | string | max:5000 |
| `price_usd` | number | |
| `number_of_rooms` | integer | |
| `number_of_bathrooms` | integer | |
| `size_m2` | integer | |
| `furnished` | boolean | |
| `parking` | boolean | |
| `minimum_months` | integer | |
| `latitude` | number | nullable |
| `longitude` | number | nullable |
| `status` | string | `available`, `rented`, `pending` |
| `images[]` | file(s) | New images to append |

**Response `200`:**
```json
{
  "message": "Apartment updated successfully.",
  "data": { /* Full Apartment object */ }
}
```

---

### 4.5 Delete Apartment

**DELETE** `/api/apartments/{apartment}`  
🔒 Requires Auth (owner or admin)

**Response `200`:**
```json
{
  "message": "Apartment deleted successfully."
}
```

---

### 4.6 Delete Apartment Image

**DELETE** `/api/apartments/{apartment}/images/{image}`  
🔒 Requires Auth (owner or admin)

**Response `200`:**
```json
{
  "message": "Image deleted successfully."
}
```

---

### 4.7 My Listings (Owner Dashboard)

**GET** `/api/my-listings`  
🔒 Requires Auth (owner)

**Query Parameters:** `per_page`, `page`

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Modern 2 Bedroom in Metn",
      "price_usd": 650.00,
      "status": "available",
      "is_verified": true,
      "views_count": 142,
      "created_at": "2026-02-15T08:30:00.000000Z",
      "updated_at": "2026-02-20T14:00:00.000000Z",
      "favorites_count": 18,
      "reviews_count": 5,
      "average_rating": 4.2,
      "is_favorited": null,
      "owner": null,
      "judiciary": {
        "id": 2,
        "name": "Metn",
        "name_ar": "المتن",
        "apartments_count": null
      },
      "images": [
        {
          "id": 1,
          "image_url": "http://localhost:8000/storage/apartments/1/photo1.jpg",
          "sort_order": 0
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 20
  }
}
```

---

## 5. Favorites

### 5.1 List My Favorites

**GET** `/api/favorites`  
🔒 Requires Auth

**Query Parameters:** `per_page`, `page`

**Response `200`:**
```json
{
  "data": [
    {
      "id": 5,
      "created_at": "2026-03-01T14:00:00.000000Z",
      "apartment": {
        "id": 1,
        "title": "Modern 2 Bedroom Apartment in Metn",
        "price_usd": 650.00,
        "number_of_rooms": 2,
        "number_of_bathrooms": 1,
        "size_m2": 110,
        "furnished": true,
        "parking": true,
        "minimum_months": 6,
        "latitude": 33.8938,
        "longitude": 35.5018,
        "status": "available",
        "is_verified": true,
        "views_count": 142,
        "created_at": "2026-02-15T08:30:00.000000Z",
        "updated_at": "2026-02-20T14:00:00.000000Z",
        "owner": { /* User object */ },
        "judiciary": { /* Judiciary object */ },
        "images": [ /* Image objects */ ]
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 3
  }
}
```

---

### 5.2 Toggle Favorite

**POST** `/api/apartments/{apartment}/favorite`  
🔒 Requires Auth

> If apartment is not favorited → adds it. If already favorited → removes it.

**Response `201` (added):**
```json
{
  "message": "Apartment added to favorites.",
  "is_favorited": true
}
```

**Response `200` (removed):**
```json
{
  "message": "Apartment removed from favorites.",
  "is_favorited": false
}
```

---

## 6. Messages

### 6.1 Get My Conversations

**GET** `/api/messages/conversations`  
🔒 Requires Auth

**Response `200`:**
```json
{
  "data": [
    {
      "apartment_id": 1,
      "apartment": {
        "id": 1,
        "title": "Modern 2 Bedroom Apartment in Metn",
        "price_usd": 650.00,
        "images": [
          {
            "id": 1,
            "image_url": "http://localhost:8000/storage/apartments/1/photo1.jpg",
            "sort_order": 0
          }
        ]
      },
      "other_user": {
        "id": 3,
        "first_name": "Georges",
        "last_name": "Nassar",
        "full_name": "Georges Nassar",
        "email": "georges@example.com",
        "phone": "+961 3 456789",
        "role": "owner",
        "is_verified": true,
        "is_banned": false,
        "profile_image": "http://localhost:8000/storage/profile-images/georges.jpg",
        "email_verified_at": "2026-01-01T00:00:00.000000Z",
        "created_at": "2026-01-01T00:00:00.000000Z"
      },
      "last_message": {
        "id": 42,
        "message": "Is the apartment still available?",
        "is_read": true,
        "created_at": "2026-03-02T16:00:00.000000Z",
        "sender": { /* User object */ },
        "receiver": { /* User object */ },
        "apartment_id": 1
      },
      "unread_count": 2
    }
  ]
}
```

---

### 6.2 Get Conversation Messages

**GET** `/api/messages/{apartmentId}/{userId}`  
🔒 Requires Auth

> Marks all messages from `userId` to you as `is_read = true`.

**URL Params:**
- `apartmentId` — apartment ID
- `userId` — the other user's ID

**Query Parameters:** `per_page` (default: 50), `page`

**Response `200`:**
```json
{
  "data": [
    {
      "id": 40,
      "message": "Hello, is this apartment available?",
      "is_read": true,
      "created_at": "2026-03-02T15:00:00.000000Z",
      "sender": {
        "id": 1,
        "first_name": "Ahmad",
        "last_name": "Khalil",
        "full_name": "Ahmad Khalil",
        "email": "ahmad@example.com",
        "phone": null,
        "role": "renter",
        "is_verified": false,
        "is_banned": false,
        "profile_image": null,
        "email_verified_at": "2026-03-01T00:00:00.000000Z",
        "created_at": "2026-03-01T00:00:00.000000Z"
      },
      "receiver": { /* User object */ },
      "apartment_id": 1
    },
    {
      "id": 41,
      "message": "Yes it is, would you like to schedule a visit?",
      "is_read": true,
      "created_at": "2026-03-02T15:30:00.000000Z",
      "sender": { /* Owner User object */ },
      "receiver": { /* Renter User object */ },
      "apartment_id": 1
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 50,
    "total": 2
  }
}
```

---

### 6.3 Send Message

**POST** `/api/messages`  
🔒 Requires Auth

**Request Body:**
```json
{
  "receiver_id": 3,
  "apartment_id": 1,
  "message": "Is the apartment still available for June?"
}
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `receiver_id` | integer | ✅ | must exist in users |
| `apartment_id` | integer | ✅ | must exist in apartments |
| `message` | string | ✅ | max:2000 |

**Response `201`:**
```json
{
  "message": "Message sent successfully.",
  "data": {
    "id": 43,
    "message": "Is the apartment still available for June?",
    "is_read": false,
    "created_at": "2026-03-03T10:00:00.000000Z",
    "sender": { /* Authenticated User object */ },
    "receiver": { /* Receiver User object */ },
    "apartment_id": 1
  }
}
```

---

### 6.4 Unread Message Count

**GET** `/api/messages/unread-count`  
🔒 Requires Auth

**Response `200`:**
```json
{
  "unread_count": 5
}
```

---

## 7. Reviews

### 7.1 Get Apartment Reviews

**GET** `/api/apartments/{apartment}/reviews`  
🔓 Public

**Query Parameters:** `per_page`, `page`

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "rating": 5,
      "comment": "Excellent apartment, owner was very responsive and the place was exactly as described.",
      "created_at": "2026-02-20T10:00:00.000000Z",
      "reviewer": {
        "id": 7,
        "first_name": "Sara",
        "last_name": "Hassan",
        "full_name": "Sara Hassan",
        "email": "sara@example.com",
        "phone": null,
        "role": "renter",
        "is_verified": false,
        "is_banned": false,
        "profile_image": null,
        "email_verified_at": "2026-01-15T00:00:00.000000Z",
        "created_at": "2026-01-15T00:00:00.000000Z"
      },
      "owner": null,
      "apartment_id": 1
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 5,
    "average_rating": 4.2
  }
}
```

---

### 7.2 Get Owner Reviews

**GET** `/api/owners/{owner}/reviews`  
🔓 Public

**URL Params:** `owner` = owner's user ID

**Response `200`:**
```json
{
  "data": [ /* Review objects */ ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 20,
    "average_rating": 4.5
  }
}
```

---

### 7.3 Submit Review

**POST** `/api/reviews`  
🔒 Requires Auth

**Request Body:**
```json
{
  "apartment_id": 1,
  "rating": 5,
  "comment": "Amazing apartment, very clean and well maintained!"
}
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `apartment_id` | integer | ✅ | must exist |
| `rating` | integer | ✅ | between 1 and 5 |
| `comment` | string | ❌ | max:2000 |

**Response `201`:**
```json
{
  "message": "Review submitted successfully.",
  "data": {
    "id": 10,
    "rating": 5,
    "comment": "Amazing apartment, very clean and well maintained!",
    "created_at": "2026-03-03T10:00:00.000000Z",
    "reviewer": { /* Authenticated User object */ },
    "owner": null,
    "apartment_id": 1
  }
}
```

**Error `422` (already reviewed):**
```json
{
  "message": "You have already reviewed this apartment."
}
```

**Error `422` (reviewing own apartment):**
```json
{
  "message": "You cannot review your own apartment."
}
```

---

### 7.4 Delete Review

**DELETE** `/api/reviews/{review}`  
🔒 Requires Auth (reviewer or admin)

**Response `200`:**
```json
{
  "message": "Review deleted successfully."
}
```

---

## 8. Reports

### 8.1 Report an Apartment

**POST** `/api/reports`  
🔒 Requires Auth

**Request Body:**
```json
{
  "apartment_id": 5,
  "reason": "This listing contains fake photos. The actual apartment is in much worse condition."
}
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `apartment_id` | integer | ✅ | must exist |
| `reason` | string | ✅ | |

**Response `201`:**
```json
{
  "message": "Report submitted successfully.",
  "data": {
    "id": 3,
    "reason": "This listing contains fake photos...",
    "status": "pending",
    "created_at": "2026-03-03T10:00:00.000000Z",
    "reporter": { /* Authenticated User object */ },
    "apartment": null
  }
}
```

**Error `422` (duplicate report):**
```json
{
  "message": "You have already reported this apartment."
}
```

---

### 8.2 My Reports

**GET** `/api/my-reports`  
🔒 Requires Auth

**Response `200`:**
```json
{
  "data": [
    {
      "id": 3,
      "reason": "This listing contains fake photos...",
      "status": "pending",
      "created_at": "2026-03-03T10:00:00.000000Z",
      "reporter": null,
      "apartment": {
        "id": 5,
        "title": "Studio in Tripoli",
        "price_usd": 250.00,
        "status": "available",
        "is_verified": false,
        "views_count": 15,
        "created_at": "2026-03-01T00:00:00.000000Z"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

---

## 9. Owner Analytics

### 9.1 Analytics Overview

**GET** `/api/analytics/overview`  
🔒 Requires Auth (owner)

**Response `200`:**
```json
{
  "data": {
    "total_listings": 5,
    "total_views": 892,
    "unique_views": 654,
    "total_favorites": 112,
    "total_inquiries": 47,
    "apartments": [
      {
        "id": 1,
        "title": "Modern 2 Bedroom in Metn",
        "status": "available",
        "views_count": 142,
        "favorites_count": 18,
        "inquiry_count": 12
      },
      {
        "id": 2,
        "title": "Studio in Baabda",
        "status": "rented",
        "views_count": 210,
        "favorites_count": 25,
        "inquiry_count": 19
      }
    ]
  }
}
```

---

### 9.2 Apartment Daily Views (Last 30 Days)

**GET** `/api/analytics/apartments/{apartment}/views`  
🔒 Requires Auth (owner of apartment or admin)

**Response `200`:**
```json
{
  "data": {
    "apartment_id": 1,
    "title": "Modern 2 Bedroom in Metn",
    "daily_views": [
      {
        "date": "2026-02-01",
        "total_views": 8,
        "unique_views": 7
      },
      {
        "date": "2026-02-02",
        "total_views": 14,
        "unique_views": 11
      },
      {
        "date": "2026-03-03",
        "total_views": 22,
        "unique_views": 18
      }
    ]
  }
}
```

---

## 10. Admin

> All admin routes require `Authorization: Bearer {admin_token}`

### 10.1 Dashboard Stats

**GET** `/api/admin/dashboard`  
🔒 Admin Only

**Response `200`:**
```json
{
  "data": {
    "total_users": 250,
    "total_owners": 80,
    "total_renters": 165,
    "banned_users": 5,
    "total_apartments": 320,
    "available_apartments": 210,
    "rented_apartments": 85,
    "pending_reports": 12,
    "total_judiciaries": 26
  }
}
```

---

### 10.2 List Users (Admin)

**GET** `/api/admin/users`  
🔒 Admin Only

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `role` | string | `renter`, `owner`, `admin` |
| `search` | string | Search by name or email |
| `banned_only` | boolean | Show only banned users |
| `per_page` | integer | default: 20 |
| `page` | integer | |

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "first_name": "Ahmad",
      "last_name": "Khalil",
      "full_name": "Ahmad Khalil",
      "email": "ahmad@example.com",
      "phone": "+961 70 123456",
      "role": "owner",
      "is_verified": true,
      "is_banned": false,
      "profile_image": null,
      "email_verified_at": "2026-03-01T00:00:00.000000Z",
      "created_at": "2026-01-01T00:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 13,
    "per_page": 20,
    "total": 250
  }
}
```

---

### 10.3 Ban User

**POST** `/api/admin/users/{user}/ban`  
🔒 Admin Only

**Response `200`:**
```json
{
  "message": "User banned successfully.",
  "user": { /* User object with is_banned: true */ }
}
```

**Error `422` (ban admin):**
```json
{
  "message": "Cannot ban an admin."
}
```

---

### 10.4 Unban User

**POST** `/api/admin/users/{user}/unban`  
🔒 Admin Only

**Response `200`:**
```json
{
  "message": "User unbanned successfully.",
  "user": { /* User object with is_banned: false */ }
}
```

---

### 10.5 Verify User (Badge)

**POST** `/api/admin/users/{user}/verify`  
🔒 Admin Only

**Response `200`:**
```json
{
  "message": "User verified successfully.",
  "user": { /* User object with is_verified: true */ }
}
```

---

### 10.6 Remove User Verification

**POST** `/api/admin/users/{user}/unverify`  
🔒 Admin Only

**Response `200`:**
```json
{
  "message": "User verification removed.",
  "user": { /* User object with is_verified: false */ }
}
```

---

### 10.7 Create Judiciary

**POST** `/api/admin/judiciaries`  
🔒 Admin Only

**Request Body:**
```json
{
  "name": "Jezzine",
  "name_ar": "جزين"
}
```

**Response `201`:**
```json
{
  "message": "Judiciary created successfully.",
  "data": {
    "id": 27,
    "name": "Jezzine",
    "name_ar": "جزين",
    "apartments_count": null
  }
}
```

---

### 10.8 Update Judiciary

**PUT** `/api/admin/judiciaries/{judiciary}`  
🔒 Admin Only

**Request Body:**
```json
{
  "name": "Jezzine",
  "name_ar": "جزين"
}
```

**Response `200`:**
```json
{
  "message": "Judiciary updated successfully.",
  "data": { /* Judiciary object */ }
}
```

---

### 10.9 Delete Judiciary

**DELETE** `/api/admin/judiciaries/{judiciary}`  
🔒 Admin Only

> Only deletes if no apartments exist in this judiciary.

**Response `200`:**
```json
{
  "message": "Judiciary deleted successfully."
}
```

**Error `422`:**
```json
{
  "message": "Cannot delete judiciary with existing apartments."
}
```

---

### 10.10 List Apartments (Admin)

**GET** `/api/admin/apartments`  
🔒 Admin Only

**Query Parameters:** `status`, `judiciary_id`, `per_page`, `page`

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Modern 2 Bedroom in Metn",
      "price_usd": 650.00,
      "status": "available",
      "is_verified": false,
      "views_count": 142,
      "created_at": "2026-02-15T08:30:00.000000Z",
      "favorites_count": 18,
      "reviews_count": 5,
      "reports_count": 1,
      "owner": { /* User object */ },
      "judiciary": { /* Judiciary object */ },
      "images": [ /* Image objects */ ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 22,
    "per_page": 20,
    "total": 320
  }
}
```

---

### 10.11 Verify Apartment

**POST** `/api/admin/apartments/{apartment}/verify`  
🔒 Admin Only

**Response `200`:**
```json
{
  "message": "Apartment verified successfully.",
  "data": { /* Apartment object with is_verified: true */ }
}
```

---

### 10.12 Remove Apartment (Admin Force Delete)

**DELETE** `/api/admin/apartments/{apartment}`  
🔒 Admin Only

**Response `200`:**
```json
{
  "message": "Apartment removed successfully."
}
```

---

### 10.13 List Reports (Admin)

**GET** `/api/admin/reports`  
🔒 Admin Only

**Query Parameters:** `status` (`pending`, `reviewed`, `rejected`), `per_page`, `page`

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "reason": "Fake photos used in this listing.",
      "status": "pending",
      "created_at": "2026-03-01T10:00:00.000000Z",
      "reporter": {
        "id": 5,
        "first_name": "Lara",
        "last_name": "Abi Saab",
        "full_name": "Lara Abi Saab",
        "email": "lara@example.com",
        "phone": null,
        "role": "renter",
        "is_verified": false,
        "is_banned": false,
        "profile_image": null,
        "email_verified_at": "2026-02-01T00:00:00.000000Z",
        "created_at": "2026-02-01T00:00:00.000000Z"
      },
      "apartment": {
        "id": 5,
        "title": "Studio in Tripoli",
        "price_usd": 250.00,
        "status": "available",
        "is_verified": false,
        "views_count": 15,
        "owner": { /* Owner User object */ },
        "judiciary": { /* Judiciary object */ },
        "images": []
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 20,
    "total": 25
  }
}
```

---

### 10.14 Update Report Status

**PUT** `/api/admin/reports/{report}`  
🔒 Admin Only

**Request Body:**
```json
{
  "status": "reviewed"
}
```

| Value | Meaning |
|-------|---------|
| `reviewed` | Report has been reviewed and action taken |
| `rejected` | Report was dismissed |

**Response `200`:**
```json
{
  "message": "Report status updated.",
  "data": {
    "id": 1,
    "reason": "Fake photos used in this listing.",
    "status": "reviewed",
    "created_at": "2026-03-01T10:00:00.000000Z",
    "reporter": { /* User object */ },
    "apartment": { /* Apartment object */ }
  }
}
```

---

## 11. Data Schemas

### User Object
```json
{
  "id": 1,
  "first_name": "Ahmad",
  "last_name": "Khalil",
  "full_name": "Ahmad Khalil",
  "email": "ahmad@example.com",
  "phone": "+961 70 123456",
  "role": "owner",
  "is_verified": true,
  "is_banned": false,
  "profile_image": "http://localhost:8000/storage/profile-images/abc.jpg",
  "email_verified_at": "2026-03-01T00:00:00.000000Z",
  "created_at": "2026-01-01T00:00:00.000000Z"
}
```

| Field | Type | Notes |
|-------|------|-------|
| `id` | integer | |
| `first_name` | string | |
| `last_name` | string | |
| `full_name` | string | Computed: `first_name + last_name` |
| `email` | string | |
| `phone` | string\|null | |
| `role` | string | `renter`, `owner`, `admin` |
| `is_verified` | boolean | Verified owner badge |
| `is_banned` | boolean | |
| `profile_image` | string\|null | Full URL to image |
| `email_verified_at` | datetime\|null | null = not verified |
| `created_at` | datetime | |

---

### Apartment Object
```json
{
  "id": 1,
  "title": "Modern 2 Bedroom Apartment in Metn",
  "description": "Beautiful fully furnished apartment...",
  "price_usd": 650.00,
  "number_of_rooms": 2,
  "number_of_bathrooms": 1,
  "size_m2": 110,
  "furnished": true,
  "parking": true,
  "minimum_months": 6,
  "latitude": 33.8938,
  "longitude": 35.5018,
  "status": "available",
  "is_verified": true,
  "views_count": 142,
  "created_at": "2026-02-15T08:30:00.000000Z",
  "updated_at": "2026-02-20T14:00:00.000000Z",
  "favorites_count": 18,
  "reviews_count": 5,
  "average_rating": 4.2,
  "is_favorited": false,
  "owner": { /* User object */ },
  "judiciary": { /* Judiciary object */ },
  "images": [ /* ApartmentImage objects */ ]
}
```

| Field | Type | Notes |
|-------|------|-------|
| `id` | integer | |
| `title` | string | |
| `description` | string | |
| `price_usd` | float | USD per month |
| `number_of_rooms` | integer | |
| `number_of_bathrooms` | integer | |
| `size_m2` | integer | Size in square meters |
| `furnished` | boolean | |
| `parking` | boolean | Parking included |
| `minimum_months` | integer | Minimum rental period |
| `latitude` | float\|null | Map coordinate |
| `longitude` | float\|null | Map coordinate |
| `status` | string | `available`, `rented`, `pending` |
| `is_verified` | boolean | Admin-verified listing |
| `views_count` | integer | Total page views |
| `favorites_count` | integer\|null | Present when counted |
| `reviews_count` | integer\|null | Present when counted |
| `average_rating` | float\|null | 1.0 – 5.0, rounded to 1 decimal |
| `is_favorited` | boolean\|null | Only when authenticated |
| `owner` | User\|null | Present when loaded |
| `judiciary` | Judiciary\|null | Present when loaded |
| `images` | Image[]\|null | Sorted by `sort_order` |

---

### Judiciary Object
```json
{
  "id": 2,
  "name": "Metn",
  "name_ar": "المتن",
  "apartments_count": 25
}
```

| Field | Type | Notes |
|-------|------|-------|
| `id` | integer | |
| `name` | string | English name |
| `name_ar` | string\|null | Arabic name |
| `apartments_count` | integer\|null | Present on list endpoint |

---

### ApartmentImage Object
```json
{
  "id": 1,
  "image_url": "http://localhost:8000/storage/apartments/1/photo1.jpg",
  "sort_order": 0
}
```

| Field | Type | Notes |
|-------|------|-------|
| `id` | integer | |
| `image_url` | string | Full URL ready for `<img src>` |
| `sort_order` | integer | 0 = first/primary image |

---

### Message Object
```json
{
  "id": 42,
  "message": "Is the apartment still available?",
  "is_read": false,
  "created_at": "2026-03-02T15:00:00.000000Z",
  "sender": { /* User object */ },
  "receiver": { /* User object */ },
  "apartment_id": 1
}
```

---

### Review Object
```json
{
  "id": 1,
  "rating": 5,
  "comment": "Amazing place, highly recommend!",
  "created_at": "2026-02-20T10:00:00.000000Z",
  "reviewer": { /* User object */ },
  "owner": { /* User object - present in owner reviews endpoint */ },
  "apartment_id": 1
}
```

---

### Report Object
```json
{
  "id": 1,
  "reason": "Fake photos in this listing.",
  "status": "pending",
  "created_at": "2026-03-01T10:00:00.000000Z",
  "reporter": { /* User object */ },
  "apartment": { /* Apartment object */ }
}
```

---

### Paginated Response Wrapper
All paginated endpoints follow this structure:
```json
{
  "data": [ /* array of objects */ ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  }
}
```

---

## 12. Error Responses

### 401 Unauthenticated
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "Forbidden. Admin access required."
}
```
```json
{
  "message": "Forbidden. Owner access required."
}
```
```json
{
  "message": "Your account has been banned. Please contact support."
}
```

### 404 Not Found
```json
{
  "message": "No query results for model [App\\Models\\Apartment] 999"
}
```

### 422 Validation Error
```json
{
  "message": "The title field is required. (and 2 more errors)",
  "errors": {
    "title": [
      "The title field is required."
    ],
    "price_usd": [
      "The price usd field is required."
    ],
    "judiciary_id": [
      "The selected judiciary id is invalid."
    ]
  }
}
```

### 500 Server Error
```json
{
  "message": "Server Error"
}
```

---

## 13. Enums & Constants

### User Roles
| Value | Description |
|-------|-------------|
| `renter` | Regular user browsing/renting apartments |
| `owner` | Property owner managing listings |
| `admin` | Platform administrator |

### Apartment Status
| Value | Description |
|-------|-------------|
| `available` | Currently available for rent |
| `rented` | Currently rented out |
| `pending` | Pending review or approval |

### Report Status
| Value | Description |
|-------|-------------|
| `pending` | Not yet reviewed by admin |
| `reviewed` | Admin took action |
| `rejected` | Report was dismissed |

### Sort Options (`sort_by`)
| Value | Description |
|-------|-------------|
| `newest` | Most recently created (default) |
| `lowest_price` | Cheapest first |
| `highest_price` | Most expensive first |
| `most_viewed` | Most viewed first |

### Lebanese Judiciaries (Seeded)
| ID | Name | Arabic |
|----|------|--------|
| 1 | Baabda | بعبدا |
| 2 | Metn | المتن |
| 3 | Keserwan | كسروان |
| 4 | Tripoli | طرابلس |
| 5 | Zahle | زحلة |
| 6 | Tyre | صور |
| 7 | Sidon | صيدا |
| 8 | Aley | عاليه |
| 9 | Batroun | البترون |
| 10 | Jbeil | جبيل |
| 11 | Chouf | الشوف |
| 12 | Nabatieh | النبطية |
| 13 | Bint Jbeil | بنت جبيل |
| 14 | Marjayoun | مرجعيون |
| 15 | Hasbaya | حاصبيا |
| 16 | West Bekaa | البقاع الغربي |
| 17 | Rashaya | راشيا |
| 18 | Baalbek | بعلبك |
| 19 | Hermel | الهرمل |
| 20 | Zgharta | زغرتا |
| 21 | Koura | الكورة |
| 22 | Bsharri | بشري |
| 23 | Miniyeh-Danniyeh | المنية-الضنية |
| 24 | Akkar | عكار |
| 25 | Beirut | بيروت |
| 26 | Jezzine | جزين |

---

## 🔑 Authentication Header

All protected routes require:
```
Authorization: Bearer {your_token_here}
```

The token is returned in the `token` field on login or register.  
Store it in `localStorage` or a secure cookie in your React app.

**Example with Axios:**
```js
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${localStorage.getItem('token')}`
  }
});
```

---

## 📤 File Upload Example (Create Apartment)

```js
const formData = new FormData();
formData.append('judiciary_id', 2);
formData.append('title', 'Spacious 3 Bedroom in Metn');
formData.append('description', 'Beautiful apartment...');
formData.append('price_usd', 900);
formData.append('number_of_rooms', 3);
formData.append('number_of_bathrooms', 2);
formData.append('size_m2', 160);
formData.append('furnished', true);
formData.append('parking', true);
formData.append('minimum_months', 6);
formData.append('latitude', 33.8938);
formData.append('longitude', 35.5018);

// Multiple images
images.forEach((file) => {
  formData.append('images[]', file);
});

await axios.post('/api/apartments', formData, {
  headers: {
    'Content-Type': 'multipart/form-data',
    'Authorization': `Bearer ${token}`
  }
});
```

---

*Last updated: March 3, 2026*
