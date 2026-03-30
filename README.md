# Agro E-Commerce Platform

This repository is a basic agro-ecommerce system.

## Project Structure

- `index.php` - home page listing products
- `product.php` - public product catalog page with filter/search
- `contact.php` - contact form for customers to message farmers
- `login.php`, `register.php`, `logout.php` - auth pages
- `admin/` - admin panel pages
- `farmer/` - farmer panel pages
- `customer/` - customer panel pages
- `handlers/` - form handlers and business logic
- `config/` - configuration and DB connection
- `includes/` - header/footer/navbar shared templates
- `uploads/products/` - uploaded product images

## Contact/Agricultural Messaging Flow

### 1) Contact form
`contact.php`
- Select farmer from dropdown
- Enter name, email, message
- Submits to `handlers/contact_handler.php`

### 2) Message storage
`handlers/contact_handler.php`
- Validates input
- Creates `messages` table if missing
- Inserts message (`sender_name`, `sender_email`, `farmer_id`, `message`)
- Redirects with session message success/error

### 3) Farmer inbox and reply
`farmer/messages.php`
- Farmer views their message list
- Reply form per message
- Submits to `handlers/reply_handler.php`

`handlers/reply_handler.php`
- Validates farmer and message ownership
- Updates `messages.reply`, `messages.replied_at`

## Farmer product soft delete (and purge)

- `farmer/my_products.php`: delete action sets status=deleted
- `handlers/product_handler.php`: handles delete as soft-delete
- `admin/products.php`: excludes deleted products and adds `Purge Deleted Products`
- `admin/dashboard.php`: total products counts exclude deleted
- `farmer/dashboard.php`: excludes deleted products

## Admin user deletion

`admin/users.php`
- Farm/customer delete now removes related rows and user
- `admin/dashboard.php` ensures non-deleted product count

## Testing & Syntax checks
Use the PHP command:
```
C:\xampp\php\php.exe -l path/to/file.php
```

## Frequently used paths
- Navbar logic: `includes/navbar.php`
- Contact page: `contact.php`
- Contact handler: `handlers/contact_handler.php`
- Farmer inbox: `farmer/messages.php`
- Reply handler: `handlers/reply_handler.php`
- Product defaults: `admin/products.php`, `farmer/my_products.php`, `farmer/dashboard.php`
- User administration: `admin/users.php`

## Credentials (default)
- Admin: `admin@agro.com` / `admin123`

## Notes
- The messaging flow now allows farmer replies by storing reply text in `messages` table.
- Farmers can purge deleted products from admin products page.
