# Blood Donation Management System API

## Project Overview

The Blood Donation Management System API is designed to streamline and facilitate the blood donation process by connecting blood requesters with NGOs that manage donors. The system involves three key stakeholders:

1. **Blood Requesters** – Individuals who need blood and can submit requests.
2. **NGOs** – Organizations responsible for donor management and request fulfillment.
3. **Super Admin & Staff** – Admins who oversee operations, manage users, and ensure smooth system functioning.

This API allows requesters to submit blood requests, NGOs to assign donors, and admins to monitor the entire process, os efficient coordination and transparency.

## Features

### For Blood Requesters (Users)

- Register/Login
- Submit Blood Requests
- Track Request Status
- Upload Proof of Donation
- Provide Feedback & Donate

### For NGOs

- Register/Login
- Admin Approval Process
- Manage Donors (Single/Bulk Upload)
- Receive & Assign Requests
- Generate Donor Certificates
- Monitor Request Status

### For Super Admin & Staff

- Manage NGOs (Approval/Rejection)
- Oversee Donor Records
- Monitor and Manage Requests
- Generate Reports & Analytics
- Handle System Configurations

---

## How to Run the Project

### Using Docker

To run the project in a Dockerized environment, follow these steps:

1. **Clone the repository**
   ```sh
   git clone <repository-url>
   cd <project-folder>
   ```
2. **Copy the environment file**
   ```sh
   cp .env.example .env
   ```
3. **Update the environment variables** in `.env` (such as database connection, application URL, etc.).
4. **Start the Docker containers**
   ```sh
   docker-compose up -d
   ```
5. **Run migrations and seed the database**
   ```sh
   docker-compose exec app php artisan migrate --seed
   ```
6. **Generate the application key**
   ```sh
   docker-compose exec app php artisan key:generate
   ```
7. **Access the application**
   - API base URL: `http://localhost:8000`

---

### Running Without Docker

If you prefer running the project without Docker, follow these steps:

1. **Clone the repository**

   ```sh
   git clone <repository-url>
   cd <project-folder>
   ```

2. **Install dependencies**

   ```sh
   composer install
   ```

3. **Copy the environment file**

   ```sh
   cp .env.example .env
   ```

4. **Set up the database**

   - Create a database manually in MySQL
   - Update the `.env` file with database credentials

5. **Run migrations and seed the database**

   ```sh
   php artisan migrate --seed
   ```

6. **Generate the application key**

   ```sh
   php artisan key:generate
   ```

7. **Serve the application**

   ```sh
   php artisan serve
   ```

   The API will be accessible at `http://127.0.0.1:8000`.

---

## API Documentation

For details on the API endpoints, refer to the API documentation (Swagger or Postman collection if available).


---

## License

This project is open-source and available under the [MIT License](LICENSE).

