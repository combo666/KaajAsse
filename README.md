# Task-Management

# **Project Setup Guide**

This document will guide you through setting up the project, including installing PHP, Composer, and configuring the `.env` file.

---

## **Setup Instructions**

### **1. Install PHP**

#### On Windows:
1. Download the latest PHP binaries from the [official PHP website](https://windows.php.net/download).
2. Extract the downloaded zip file to a directory, e.g., `C:\php`.
3. Add the directory to your system’s PATH environment variable:
    - Search for `Environment Variables` in the Windows search bar.
    - Under `System Variables`, find `Path` and edit it.
    - Add the path to your PHP directory (e.g., `C:\php`).

#### On macOS:
1. Install PHP using Homebrew:
    ```bash
    brew install php
    ```

#### On Linux:
1. Install PHP using your distribution’s package manager:
    ```bash
    sudo apt update
    sudo apt install php-cli
    ```

#### Verify Installation:
Run the following command in your terminal:
```bash
php -v
```

### **2. Install Composer**

#### On Windows:
1. Download the Composer installer from the [Composer website](https://getcomposer.org/Composer-Setup.exe).
2. Run the installer and follow the prompts.

#### On macOS/Linux:
1. Run the following commands in your terminal:
    ```bash
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"
    sudo mv composer.phar /usr/local/bin/composer
    ```

#### Verify Installation:
Run the following command:
```bash
composer --version
```

### **3. Clone the Repository**
Clone the project repository to your local machine:
```bash
git clone https://github.com/combo666/KaajAsse.git
cd KaajAsse
```


### **4. Install Dependencies**
Run the following command to install all PHP dependencies:

```bash
composer install
```

### **5. Setup .env File**
The .env file contains configuration variables for the project. Follow these steps to set it up:

1. Create a new .env file in the root of the project directory.
2. Fill in the required values in the .env file. Example:
```env
DB_HOST=mysql-91cbba5-kaajasse.e.aivencloud.com
DB_PORT=17375
DB_USER=avnadmin
DB_PASS=AVNS_zlCdhgb4XH_WXm_nYmz
DB_NAME=KaajAsse
```


