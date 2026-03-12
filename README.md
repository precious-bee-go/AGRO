# PreshyMarketplace 🚜🌽

A premium, MVC-based agricultural marketplace designed to connect local farmers directly with consumers. **PreshyMarketplace** empowers farmers through a unique time-based booking system, allowing customers to reserve produce while it is still in the soil.

## 🌟 Key Features

- **Premium Design**: A modern, glassmorphism-inspired UI with smooth animations and a "girlish" yet professional aesthetic.
- **Micro-Animations**: Uses entrance animations (`animate-up`) to create a polished user experience.
- **Time-Based Booking**: Integrated countdown timers for every product batch, showing exactly when harvest is ready.
- **Dual Dashboards**:
  - **Farmer Dashboard**: Manage listings, track incoming orders, and view real-time performance stats.
  - **Buyer Dashboard**: Monitor booking history, track order statuses, and manage delivery addresses.
- **Secure Ordering**: Restricted "Add to Cart" and checkout functionality ensure only verified users can place orders.
- **Dynamic Content**: Fully database-driven catalog with advanced category filtering.
- **Mobile Responsive**: 100% responsive design with a dedicated mobile navigation sidebar.

## 🛠️ Technology Stack

- **Backend**: PHP 8.2 (Pure MVC Architecture)
- **Database**: MySQL (Improved schema with secondary indexing)
- **Frontend**: Vanilla CSS3, Javascript (ES6)
- **Icons**: FontAwesome 6.4 (Professional Grade)
- **Typography**: Google Fonts (Inter)

## 🚀 Quick Setup & Installation

The project includes a universal setup script for one-click deployment.

1.  **Clone the project** into your local server directory (e.g., `htdocs`).
2.  **Configure Database**: Ensure your MySQL settings in `config/database.php` are correct.
3.  **Run Setup**: Visit the setup script in your browser:
    `http://localhost/Preshy_Project/AGRO/setup.php`
    _(This will automatically create the database, import the schema, and seed the demo products/images.)_

## 🔑 Test Login Credentials

| Account Role | Email             | Password      |
| :----------- | :---------------- | :------------ |
| **Admin**    | `admin@demo.com`  | `password123` |
| **Farmer**   | `farmer@demo.com` | `password123` |
| **Buyer**    | `buyer@demo.com`  | `password123` |

## 📁 Project Structure

```text
AGRO/
├── assets/         # CSS, JS, and Fonts
├── config/         # Database and Global Constants
├── controllers/    # MVC Controllers
├── core/           # Base MVC Engine classes
├── db/             # SQL Schemas
├── images/         # Product image library
├── models/         # MVC Models (Data Logic)
├── public/         # Public entry point (.htaccess)
├── views/          # HTML Templates (PHP)
├── setup.php       # One-click installation utility
└── .htaccess       # Root routing & static asset control
```
