# Task Management API

A RESTful Task Management API built with **Laravel** and powered by **Laravel Sail (Docker)**.
The application provides authentication, task management, task dependencies, and status transitions following clean domain-driven principles.

---

## 📌 Tech Stack

* **Laravel** 
* **Laravel Sail** (Docker-based local development)
* **MySQL 8.4**
* **Laravel Sanctum** (API authentication)
* **Postman** (API testing & documentation)

---

## 📁 Project Overview

This API allows you to:

* Authenticate users
* Create, update, and view tasks
* Assign tasks to users
* Manage task dependencies
* Control task status transitions (pending, completed, canceled)
* Enforce business rules such as:

  * A task cannot be completed unless all dependencies are completed
  * A task cannot be updated to the same status it already has
  * Circular dependencies are prevented

---

## 🚀 Getting Started

### 1️⃣ Prerequisites

Make sure you have the following installed:

* Docker
* Docker Compose
* Git

> ❗ You do **NOT** need PHP or MySQL installed locally — Sail handles everything.

---

### 2️⃣ Clone the Repository

```bash
git clone https://github.com/YousefAlsayed4/Task-Management-Api.git
cd Task-Management-Api
```

---

### 3️⃣ Environment Configuration

Create a `.env` file:

```bash
cp .env.example .env
```

Update the following environment variables to match the Docker (Sail) setup:

```env
APP_PORT=8080
PHPMYADMIN_PORT=8081

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=sail
DB_PASSWORD=password

```

---

### 4️⃣ Start the Application (Laravel Sail)

Run the following command:

```bash
./vendor/bin/sail up -d
```

This will start:

* Laravel application
* MySQL database
* phpMyAdmin

---

### 5️⃣ Install Dependencies & Setup App

```bash
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

---

## 🌐 Application URLs

| Service      | URL                                            |
| ------------ | ---------------------------------------------- |
| API Base URL | [http://localhost:8080](http://localhost:8080)           |
| phpMyAdmin   | [http://localhost:8081](http://localhost:8081) |

---

## 🔐 Authentication Flow

This API uses **Laravel Sanctum** for authentication.

### Login Endpoint

```http
POST /api/auth/login
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "token": "YOUR_ACCESS_TOKEN"
  }
}
```

Use the returned token as a **Bearer Token** for all protected endpoints.

---

## 📡 API Endpoints

### Public

| Method | Endpoint        | Description |
| ------ | --------------- | ----------- |
| POST   | /api/auth/login | User login  |

---

### Protected (Require Authentication)

| Method | Endpoint                         | Description         |
| ------ | -------------------------------- | ------------------- |
| GET    | /api/tasks                       | List all tasks      |
| POST   | /api/tasks                       | Create a new task   |
| GET    | /api/tasks/{task}                | Show task details   |
| PUT    | /api/tasks/{task}                | Update task         |
| POST   | /api/tasks/{task}/add-dependency | Add task dependency |
| PATCH  | /api/tasks/{task}/status         | Update task status  |
| PATCH  | /api/tasks/{task}/assign         | Assign task         |

---

## 🧪 Postman Setup & Testing

The Postman collection and environment files are located in the `postman/` directory.

### 1️⃣ Import Collection

* Open Postman
* Click **Import**
* Import the provided **Postman Collection** file

---

### 2️⃣ Import Environment Variables

Import the provided environment file (example):

```
Task-Management-API.postman_environment.json
```

---

### 3️⃣ Configure Base URL

In Postman → Environment Variables:

```text
baseUrl = http://localhost:8080/api
```

> If you change `APP_PORT`, update the base URL accordingly.

---

### 4️⃣ Authentication in Postman

1. Call **Login** request
2. Token is automatically saved in the environment
3. All secured requests use the token via Bearer Authentication

---

## ⚙️ Docker Services (Overview)

### Laravel Application

* PHP 8.5 runtime
* Mounted volume for live code changes

### MySQL

* Version: 8.4
* Persistent volume (`sail-mysql`)

### phpMyAdmin

* Accessible via browser
* Uses same DB credentials as Laravel

---

## 🧠 Design Decisions

* Domain logic is handled inside the **Model layer**
* Controllers remain thin and focused
* Business rules enforced using **Domain Exceptions**
* Clean separation between:

  * Request validation
  * Authorization
  * Domain logic

---

## 🛑 Common Commands

```bash
# Stop containers
./vendor/bin/sail down

# View logs
./vendor/bin/sail logs

# Run artisan commands
./vendor/bin/sail artisan
```

---

## 🧾 Notes

* Environment variables are **not** shared with the Postman collection by default
* Use the provided environment template for safe sharing
* Tokens should never be committed to Git

---

## 👨‍💻 Author

**Yousef Alsayed**
Task Management API – Laravel & Sail

---

## 📄 License

This project is open-source and available under the MIT License.
