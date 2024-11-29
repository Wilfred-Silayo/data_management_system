## DATA MANAGEMENT SYSTEM
This is a laravel version 11.x web application project. It has the following features:
 - Big Data Uploads
 - Big Data Exports
 - Sort, Filter, and Search  Big Data Uploaded
 - Display Big Data Uploaded

## Requirements
This project requires the followings to be installed in your computer:
 - Php version 8.2 - 8.4 [download php](https://www.php.net/downloads.php)

 - Composer, this is a php package manager [download composer](https://getcomposer.org/download/)

 - Mysql database system [download mysql](https://dev.mysql.com/downloads/mysql/) or download and install XAMPP server which by comes with mysql.

 ## How to deploy locally
  - Open terminal/ command prompt in your computer and  navigate to the root directory of the project 

  - Create a new .env file, copy the content of .env.example file and paste it in .env

  - If you are using XAMPP server, start XAMMP control panel and start mysql service

  - If you are using mysql as stand alone, make sure you have added it's path in the system environment variable. Then open a new terminal and start mysql service by running :
    - net start mysql (For windows) or 
    - sudo systemctl start mysql (For linux)

 - You can also use any database management system.

  - run the following commands:
    - composer install 
      - This command will install all the dependencies of the project.
      
    - php artisan key:generate

    - php artisan migrate
      - This will create all the tables in the database as specified in the database migrations. In this project we are using mysql database
    - php artisan serve
      - This command will start laravel development server. Open the browser of your choice and type the following address 127.0.0.1:8000 to access the web application.
    - open a new terminal and run the following command:
      - php artisan queue:work
   -->This command will run background jobs. Then you can upload the csv file in

