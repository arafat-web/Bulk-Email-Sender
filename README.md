<p align="center">
  <img src="./public/assets/img/github-temp.png" alt="Icon Description">
</p>

<h1 align="center">Bulk Email Sender</h1>

<p align="center">
  <img src="https://img.shields.io/github/stars/arafat-web/Bulk-Email-Sender?style=for-the-badge" alt="Total Issues">
  <img src="https://img.shields.io/github/issues/arafat-web/Bulk-Email-Sender?style=for-the-badge">
  <img src="https://img.shields.io/github/license/arafat-web/Bulk-Email-Sender?style=for-the-badge" alt="License">
  <!-- Add more badges with different icons if necessary -->
</p>

## Introduction
Bulk Email Sender is an open-source Laravel application designed to simplify the process of sending bulk emails using data from an Excel file. With this application, administrators can effortlessly import an Excel file, extract email addresses, and send personalized HTML emails to recipients. This documentation provides a step-by-step guide on how to set up the project and configure email settings.

### Prerequisites
- PHP 8.1 or higher
- Composer
- Laravel 9 or higher
- MySQL or any other supported database system

## Setup Instructions

### Step 1: Clone the Repository
```
git clone https://github.com/arafat-web/bulk-email-sender.git
cd bulk-email-sender
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Configure Environment Variables
Duplicate the `.env.example` file and rename it to `.env`. Update the following variables:

```ini
DB_CONNECTION=mysql
DB_HOST=your_database_host
DB_PORT=your_database_port
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

MAIL_DRIVER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=your_smtp_port
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 4: Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Run Migrations and Seed Database
```bash
php artisan migrate --seed
```

### Step 6: Start Queue
```bash
php artisan queue:listen
```

### Step 6: Serve the Application
```bash
php artisan serve
```

Access the application in your browser at `http://localhost:8000`.


## How to Use

### 1. One Time Sender
1. Login to the admin panel.
    ```
    Email: admin@email.com
    Password: 12345678
    ```
2. Navigate to the "One Time Sender" section.
3. Enter your email subject in the provided field.
4. Design your email template using the textarea editor.
5. Click the "Send" button.
6. Wait a few minutes. Your emails will be sent one by one to the recipients.

## Demo
<img src="./public/assets/img/demo/demo1.png" alt="Demo" width="33%" height="250px">
<img src="./public/assets/img/demo/demo2.png" alt="Demo" width="33%" height="250px">
<img src="./public/assets/img/demo/demo3.png" alt="Demo" width="33%" height="250px">

## Contributing
For any issues or inquiries, please open an issue on the [Issues](https://github.com/arafat-web/Bulk-Email-Sender/issues).<br/>
If you can help me by contributing. Please don't hesitate to open a [Pull Request](https://github.com/arafat-web/Bulk-Email-Sender/pulls).<br/>
🎉 **Thanks for reading!** 🌟  



### Contact Me
[![Email](https://img.shields.io/badge/Gmail-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:arafat.122260@gmail.com)
[![Facbook](https://img.shields.io/badge/Facebook-1877F2?style=for-the-badge&logo=facebook&logoColor=white)](https://www.facebook.com/arafathossain000)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/arafat-hossain-ar-a174b51a6/)
[![Sololearn](https://img.shields.io/badge/-Sololearn-3a464b?style=for-the-badge&logo=Sololearn&logoColor=white)](https://www.sololearn.com/profile/4703319)
[![Website](https://img.shields.io/badge/website-000000?style=for-the-badge&logo=About.me&logoColor=white)](https://arafatdev.com)
