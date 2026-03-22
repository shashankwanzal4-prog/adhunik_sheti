# Adhunik Krushi Bhandar - Agricultural eCommerce Website

A modern, responsive eCommerce website for agricultural products built with Core PHP and MySQL.

## 🌱 Features

### Frontend Features
- **Responsive Design**: Mobile-first approach with Bootstrap 5
- **Hero Section**: Eye-catching banner with call-to-action
- **Product Catalog**: Dynamic product listing with categories
- **Shopping Cart**: Session-based cart functionality
- **3-Step Checkout**: Streamlined checkout process
- **Testimonials**: Customer reviews carousel
- **Search & Filter**: Product search and category filtering
- **Animations**: Smooth scroll animations with AOS
- **WhatsApp Integration**: Quick contact via WhatsApp

### Backend Features
- **Admin Dashboard**: Complete admin panel
- **Product Management**: Add, edit, delete products
- **Order Management**: View and manage customer orders
- **Order Status Updates**: Real-time order tracking
- **Secure Authentication**: Admin login system
- **Database Management**: MySQL with optimized queries

## 🛠 Tech Stack

- **Backend**: PHP 8.0+ (Core PHP, no frameworks)
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript
- **CSS Framework**: Bootstrap 5
- **Icons**: Font Awesome 6
- **Animations**: AOS (Animate On Scroll)
- **Carousel**: Swiper.js
- **Server**: XAMPP/WAMP/LAMP

## 📁 Project Structure

```
Katkar_New/
├── index.php                 # Homepage
├── config/
│   └── db.php               # Database configuration
├── includes/
│   ├── header.php           # Header component
│   └── footer.php           # Footer component
├── pages/
│   ├── products.php         # Products listing
│   ├── cart.php            # Shopping cart
│   ├── checkout.php        # Checkout page
│   └── confirmation.php    # Order confirmation
├── admin/
│   ├── dashboard.php       # Admin dashboard
│   ├── login.php           # Admin login
│   ├── logout.php          # Admin logout
│   ├── products.php        # Product management
│   ├── add_product.php     # Add product form
│   └── orders.php          # Order management
├── assets/
│   ├── css/
│   │   ├── style.css       # Main styles
│   │   └── admin.css       # Admin styles
│   ├── js/
│   │   └── script.js       # Main JavaScript
│   └── images/             # Product images
├── database_setup.sql      # Database setup script
└── README.md              # This file
```

## 🚀 Setup Instructions

### 1. Prerequisites
- XAMPP/WAMP/LAMP server
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Modern web browser

### 2. Database Setup

1. **Start XAMPP/WAMP**
   - Start Apache and MySQL services

2. **Create Database**
   ```sql
   -- Option 1: Using phpMyAdmin
   -- Create a new database named "krushi_bhandar"
   
   -- Option 2: Using command line
   mysql -u root -p
   CREATE DATABASE krushi_bhandar;
   USE krushi_bhandar;
   ```

3. **Import Database Schema**
   - Open phpMyAdmin
   - Select the `krushi_bhandar` database
   - Click "Import" tab
   - Choose the `database_setup.sql` file
   - Click "Go"

### 3. Project Setup

1. **Extract/Clone Project**
   - Place the project folder in `htdocs/` (XAMPP) or `www/` (WAMP)

2. **Configure Database**
   - Edit `config/db.php` if needed:
   ```php
   private $host = 'localhost';
   private $username = 'root';
   private $password = ''; // Your MySQL password
   private $database = 'krushi_bhandar';
   ```

3. **Set Permissions**
   - Ensure write permissions for `assets/images/products/` folder

### 4. Access the Website

1. **Frontend**: `http://localhost/Katkar_New/`
2. **Admin Panel**: `http://localhost/Katkar_New/admin/`

### 5. Admin Login

- **Username**: `admin`
- **Password**: `admin123`

## 🎨 Customization

### Colors
Edit `assets/css/style.css` to modify the color scheme:
```css
:root {
    --primary-green: #2e7d32;
    --light-green: #66bb6a;
    --accent-orange: #f57c00;
}
```

### Images
- Replace placeholder images in `assets/images/`
- Add product images to `assets/images/products/`

### Content
- Edit text content directly in PHP files
- Update contact information in `includes/footer.php`

## 📱 Responsive Design

The website is fully responsive and works on:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (320px - 767px)

## 🔧 Features in Detail

### Shopping Cart
- Session-based cart management
- Add/remove products
- Quantity updates
- Real-time cart count

### Checkout Process
1. **Cart Review**: Review selected products
2. **Customer Details**: Enter shipping information
3. **Order Confirmation**: Receive order confirmation

### Admin Panel
- **Dashboard**: Overview with statistics
- **Products**: Full CRUD operations
- **Orders**: View and manage orders
- **Status Updates**: Update order status

### Payment Methods
- Cash on Delivery (COD)
- Bank Transfer
- UPI Payment

## 🌟 Key Features

### SEO Optimization
- Semantic HTML5 structure
- Meta tags and descriptions
- Clean URLs
- Fast loading times

### Security
- SQL injection prevention
- XSS protection
- Secure admin authentication
- Input validation

### Performance
- Optimized database queries
- Image lazy loading
- Minified CSS/JS
- Caching headers

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check MySQL service is running
   - Verify database credentials in `config/db.php`
   - Ensure database exists

2. **Images Not Loading**
   - Check file permissions
   - Verify image paths
   - Ensure images exist in correct folder

3. **Admin Login Not Working**
   - Check session settings
   - Verify admin user exists in database
   - Clear browser cookies

4. **Cart Not Working**
   - Check session is enabled
   - Verify session path permissions
   - Clear browser cache

### Error Reporting
Add this to `index.php` for development:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📞 Support

For support and queries:
- **Email**: info@adhunikkrushi.com
- **Phone**: +91 98765 43210

## 📄 License

This project is for demonstration purposes. Please modify as needed for production use.

## 🔄 Updates

### Version 1.0.0
- Initial release
- Complete eCommerce functionality
- Admin panel
- Responsive design

---

**Note**: This is a demonstration project. For production use, please:
1. Change default admin credentials
2. Implement proper error handling
3. Add SSL certificate
4. Set up proper backups
5. Implement logging system
6. Add email notifications
