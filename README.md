### 1. Clone the Repository
Clone this repository to your local machine using Git

### 2. Install dependencies
cd kubitest
composer install

### 3. Setup database
symfony console doctrine:database:create 
symfony console doctrine:schema:update --force 

symfony console doctrine:fixtures:load

### 4. Build assests
npm install
npm run dev

### 5. Run the application
symfony serve

## 6. Login information
The following users are created by the fixtures:
User login: Use user@mail.com and user as credentials (you can also register a new user).
Admin login: Use admin@mail.com for admin access.