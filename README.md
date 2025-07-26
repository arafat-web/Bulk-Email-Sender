<div align="center">
  
<svg width="120" height="120" viewBox="0 0 120 120">
  <defs>
    <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
    </linearGradient>
  </defs>
  <circle cx="60" cy="60" r="55" fill="url(#logoGradient)"/>
  <rect x="30" y="42" width="60" height="36" rx="6" fill="white" opacity="0.95"/>
  <path d="M30 48 L60 65 L90 48" stroke="url(#logoGradient)" stroke-width="2" fill="none" stroke-linecap="round"/>
  <circle cx="85" cy="35" r="2" fill="white" opacity="0.8"/>
  <circle cx="90" cy="30" r="1.5" fill="white" opacity="0.6"/>
  <circle cx="95" cy="35" r="1" fill="white" opacity="0.4"/>
</svg>
  
  <h1>🚀 Bulk Email Sender v2.0</h1>
  
  <p><strong>Professional Laravel-based email marketing solution with advanced contact management</strong></p>
  
  <p>
    <img src="https://img.shields.io/badge/Laravel-10.x-red?style=for-the-badge&logo=laravel" alt="Laravel">
    <img src="https://img.shields.io/badge/PHP-8.1+-blue?style=for-the-badge&logo=php" alt="PHP">
    <img src="https://img.shields.io/badge/Bootstrap-5.x-purple?style=for-the-badge&logo=bootstrap" alt="Bootstrap">
    <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
  </p>
  
  <p>
    <img src="https://img.shields.io/github/stars/arafat-web/Bulk-Email-Sender?style=for-the-badge" alt="Stars">
    <img src="https://img.shields.io/github/issues/arafat-web/Bulk-Email-Sender?style=for-the-badge" alt="Issues">
    <img src="https://img.shields.io/github/forks/arafat-web/Bulk-Email-Sender?style=for-the-badge" alt="Forks">
  </p>
</div>

---

## ✨ Features

**What's New in v2.0:**
- 👥 **Contact Management** - Complete contact database with tags and categories
- 📧 **Individual Emails** - Send personalized emails to specific contacts  
- 🏷️ **Tag System** - Organize and target contacts with custom tags
- 📊 **Excel Import/Export** - Seamlessly import contacts from CSV/Excel files
- 🎨 **Modern UI** - Clean, responsive design with breadcrumb navigation
- � **Queue System** - Background email processing with Laravel Queues

**Core Features:**
- ✅ Bulk email sending with HTML templates
- ✅ Contact management with tagging system
- ✅ Excel/CSV import and export
- ✅ Email validation and verification
- ✅ Multi-SMTP support
- ✅ Real-time delivery tracking

---

## � Screenshots

<div align="center">

### Dashboard Overview
<img src="./public/bulk-mailer-screenshots/dashboard.png" alt="Dashboard" width="700">

### Contact Management
<img src="./public/bulk-mailer-screenshots/contacts.png" alt="Contact Management" width="700">

### Email Templates
<img src="./public/bulk-mailer-screenshots/email-templates.png" alt="Email Templates" width="700">

### Individual Email Sending
<img src="./public/bulk-mailer-screenshots/individual-emails.png" alt="Individual Emails" width="700">

### Instant Campaign
<img src="./public/bulk-mailer-screenshots/instant-campaign.png" alt="Instant Campaign" width="700">

### Tag Management
<img src="./public/bulk-mailer-screenshots/tags.png" alt="Tag Management" width="700">

### Email Accounts Settings
<img src="./public/bulk-mailer-screenshots/email-accounts.png" alt="Email Accounts" width="700">

### User Profile
<img src="./public/bulk-mailer-screenshots/profile.png" alt="User Profile" width="700">

### Login Page
<img src="./public/bulk-mailer-screenshots/login.png" alt="Login Page" width="700">

</div>

---

## �🛠️ Prerequisites

- **PHP**: 8.1+
- **Composer**: Latest version
- **Laravel**: 10.x
- **Database**: MySQL 5.7+ or PostgreSQL 10+
- **SMTP Server**: Gmail, SendGrid, Mailgun, etc.

---

## ⚡ Installation

1. **Clone and setup**
   ```bash
   git clone https://github.com/arafat-web/Bulk-Email-Sender.git
   cd Bulk-Email-Sender
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

2. **Database setup**
   ```bash
   # Configure database in .env file
   php artisan migrate --seed
   ```

3. **Email configuration in .env**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_ENCRYPTION=tls
   ```

4. **Start application**
   ```bash
   php artisan serve
   php artisan queue:work
   ```

**Default Login:** `admin@email.com` / `12345678`

---

## 📸 Screenshots

<div align="center">
  <img src="./docs/images/dashboard.png" alt="Dashboard" width="45%">
  <img src="./docs/images/contact-management.png" alt="Contact Management" width="45%">
</div>

<div align="center">
  <img src="./docs/images/bulk-email.png" alt="Bulk Email" width="45%">
  <img src="./docs/images/individual-email.png" alt="Individual Email" width="45%">
</div>

---

## 🎮 How to Use

### 📧 **Bulk Email Campaign**
1. Navigate to "Instant Campaign"
2. Import contacts via CSV/Excel or use existing contacts
3. Write your email subject and content
4. Send to all contacts or specific tags

### 👥 **Contact Management**
1. Go to "Contacts" → "Add Contact" for individual entries
2. Use "Import Contacts" for bulk CSV/Excel uploads
3. Organize contacts with tags
4. Export contact lists when needed

### 🏷️ **Tag System**
1. Create tags in "Contact Tags" section  
2. Assign tags to contacts for organization
3. Send targeted emails to specific tag groups
4. Filter contacts by tags for better management

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

For issues and feature requests, use [GitHub Issues](https://github.com/arafat-web/Bulk-Email-Sender/issues).

---

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 📱 Connect With Me

<div align="center">
  
[![Email](https://img.shields.io/badge/Gmail-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:arafat.122260@gmail.com)
[![Facebook](https://img.shields.io/badge/Facebook-1877F2?style=for-the-badge&logo=facebook&logoColor=white)](https://www.facebook.com/arafathossain000)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/arafat-hossain-ar-a174b51a6/)
[![Website](https://img.shields.io/badge/website-000000?style=for-the-badge&logo=About.me&logoColor=white)](https://arafatdev.com)

</div>

---

<div align="center">
  <p><strong>⭐ If you found this project helpful, please give it a star! ⭐</strong></p>
  <p>Made with ❤️ by <a href="https://github.com/arafat-web">Arafat Hossain</a></p>
</div>
