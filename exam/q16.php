<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Inheritance Example</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .user-info, .admin-info {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .user-info h2, .admin-info h2 {
            margin: 0 0 10px 0;
        }
    </style>
</head>
<body>
    <h1>PHP Inheritance Example</h1>

    <?php
    // Base User class
    class User {
        protected $name;
        protected $email;

        public function __construct($name, $email) {
            $this->name = $name;
            $this->email = $email;
        }

        public function displayInfo() {
            return "Name: {$this->name}<br>Email: {$this->email}<br>";
        }
    }

    // Derived Admin class extending User
    class Admin extends User {
        private $accessLevel;

        public function __construct($name, $email, $accessLevel) {
            parent::__construct($name, $email); // Call the parent constructor
            $this->accessLevel = $accessLevel;
        }

        // Overriding displayInfo method to include access level
        public function displayInfo() {
            return parent::displayInfo() . "Access Level: {$this->accessLevel}<br>";
        }
    }

    // Create a User instance
    $user = new User("John Doe", "john@example.com");

    // Create an Admin instance
    $admin = new Admin("Jane Smith", "jane@example.com", "Super Admin");

    // Display User information
    echo '<div class="user-info">';
    echo "<h2>User Info</h2>";
    echo $user->displayInfo();
    echo '</div>';

    // Display Admin information
    echo '<div class="admin-info">';
    echo "<h2>Admin Info</h2>";
    echo $admin->displayInfo();
    echo '</div>';
    ?>

</body>
</html>
