# Contact Manager
## Description
This contact manager is a simple web app built using a LAMP stack. Users can log in through the web portal, add new contacts to their profile, and search for contacts that have been added. Editing and deleting contacts is also supported.
## Technologies Used
- LAMP stack: Linux, Apache, MySQL, and PHP.
- Server hosting: DigitalOcean
- IDE: Visual Studio Code
- Version Control: Git/GitHub
## Setup Instructions
1. Copy everything inside of this repository to /var/www/html on an Apache web server.
2. Install composer at https://getcomposer.org in /var/www/html/API.
3. Create the MySQL database on the web server using contactmanagerdb.sql.
4. Create a database user for the API so it can interact with the database.
5. Create a .env file in /var/www/html/API with the database user credentials (DB_HOST, DB_USER, DB_PASSWORD, and DB_NAME).
6. Insert at least one user account into the database.
7. Change the url paths in both JavaScript files to use your server.
## Access Instructions
- Access your contact manager application at http://<your Apache server's IP address>.
- The web app is hosted at this domain: http://cop4331group13.site.