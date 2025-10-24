M-Commerce Project Report: GadgetHut (Refurbished)

**Name:** [Your Name]
**Course/ID:** [Your Course Name/ID]
**Date:** [Date]

## 1. Project Overview

This project involved the design, development, and deployment of a full-stack M-Commerce web application named "GadgetHut," focusing on selling certified refurbished electronic gadgets. The application was built using PHP (with XAMPP for local development), MySQL for the database, and deployed on AWS EC2. Version control was managed using Git and GitHub.

The primary goal was to create a responsive, functional, and professional-looking online store accessible via mobile and desktop, incorporating features relevant to selling refurbished goods, such as condition details and usage duration.

## 2. Technologies Used

* **Frontend:** HTML5, Tailwind CSS (for styling and responsiveness), JavaScript (for dynamic features like live search, modals, cart management, API calls).
* **Backend:** PHP 8.2 (via XAMPP locally, installed on EC2).
* **Database:** MySQL (MariaDB via XAMPP locally, MariaDB on EC2). PDO was used for database interaction in PHP.
* **Web Server:** Apache (via XAMPP locally, installed on EC2).
* **Cloud Platform:** Amazon Web Services (AWS) EC2 (t2.micro instance with Amazon Linux 2).
* **Version Control:** Git, GitHub.
* **Local Development:** XAMPP, VS Code.

## 3. Key Features Implemented

* **Responsive UI:** Designed with Tailwind CSS for optimal viewing on various devices (mobile, tablet, desktop).
* **Product Catalog Display:** Fetches and displays refurbished products from the MySQL database, including images, descriptions, prices, ratings, and condition.
* **Live Search:** Instantly filters the product list as the user types in the search bar.
* **Product Detail Modal:** Clicking a product opens a pop-up window showing detailed information, including image, condition, usage duration, and specific refurbishment notes.
* **Shopping Cart:** Users can add items, view the cart, adjust quantities, remove items, and see the subtotal.
* **Local Checkout:** Simulates an order placement by storing order details in the `orders` and `order_items` tables in the database. (No real payment processing).
* **Refurbished Theme:** Site branding and content tailored to selling refurbished electronics.
* **Admin Panel:** A separate page (`/admin/`) allowing updating of product image URLs in the database.
* **Animated Help/Support Modal:** Provides FAQs and a contact form with smooth animations.
* **Dark Mode Toggle:** Allows users to switch between light and dark themes.

## 4. Screenshots

### 4.1. Application Running Locally (XAMPP)

*(Paste a screenshot here of your app running in the browser with the http://localhost/gadgethut-store/ URL visible)*

*(Optional: Add another local screenshot, e.g., the product detail modal or the cart)*

### 4.2. Application Running on AWS EC2

*(Paste a screenshot here of your app running in the browser using the EC2 Public IP address)*

*(Optional: Add another EC2 screenshot, e.g., the Admin Panel or the Help modal)*

## 5. Deployment Details

* **AWS EC2 Instance:** An Amazon Linux 2 (t2.micro) instance was launched.
* **LAMP Stack:** Apache, MariaDB (MySQL compatible), and PHP were installed and configured on the EC2 instance.
* **Security Group:** Configured to allow inbound traffic on Port 80 (HTTP) and Port 22 (SSH).
* **Code Deployment:** The project code was cloned directly from the GitHub repository onto the EC2 instance's web root (`/var/www/html`). File permissions were set for the Apache user.
* **Database Setup (EC2):** The `m_commerce_db` database and a dedicated user (`m_commerce_user`) were created on the EC2 MariaDB server. The `database.sql` file was imported.
* **Configuration:** The `config/db.php` file on the EC2 instance was updated with the EC2 database credentials.

## 6. Project Links

* **GitHub Repository URL:** [Paste Your GitHub Repository Link Here]
* **EC2 Public IP/Domain Link:** http://[Paste Your EC2 Public IP Address Here]
* **SQL File:** `database.sql` (included in the GitHub repository).

## 7. Challenges and Learnings

*(Briefly mention any challenges faced, e.g., debugging PHP errors, configuring EC2, making the site responsive, fixing JavaScript bugs, and what you learned from the process).*

One key challenge was debugging PHP errors that prevented API endpoints from returning valid JSON, requiring careful error handling and inspection of server logs and browser network responses. Setting up the EC2 environment and ensuring correct file permissions was also a crucial learning step. Implementing features like the live search and product detail modal enhanced understanding of front-end JavaScript interaction with a backend API.

---
