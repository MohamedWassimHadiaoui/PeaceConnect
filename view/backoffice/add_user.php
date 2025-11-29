<?php
require_once "../../controller/userController.php";
require_once "../../model/User.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $uc = new userController();

    

    $user = new User(
        null,
        $_POST['name'],
        $_POST['lastname'],
        $_POST['email'],
        $_POST['password'],
        $_POST['cin'],
        $_POST['tel'],
        $_POST['gender'],
        $_POST['role']
    );

    $uc->addUser($user);²

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add User</title>
    <link rel="stylesheet" href="../frontoffice/index.css">
</head>

<body>

<header>
    <div class="container">
        <h1 class="logo">Peace</h1>

        <nav>
            <ul>
                <li><a href="#home" class="super-button">Home</a></li>
                <li><a href="#deals" class="super-button">Forum</a></li>
                <li><a href="#deals" class="super-button">Événements</a></li>
                <li><a href="#contact" class="super-button">Signaler</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top:150px;">
    <div class="card">

        <h2>Add New User</h2>

        <form method="POST" onsubmit="return saisie()">

            <div class="input-row">
                <input type="text" id="name" name="name" placeholder="Name" >
                <input type="text" id="name" name="lastname" placeholder="Lastname" >
            </div>

            <input type="email" id="email"name="email" placeholder="Email" ><br><br>
            <input type="password" id="password" name="password" placeholder="Password" ><br><br>
            <input type="text" id="cin" name="cin" placeholder="CIN"><br><br>
            <input type="text" id="tel" name="tel" placeholder="Phone"><br><br>

            <select name="gender">
                <option value="">Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select><br><br>

            <input type="text" name="role" value="client" placeholder="Role"><br><br>

            <button class="shop-now-btn" type="submit">Add User</button>
            <p id="errorBox"></p>
        </form>

        <br>
        <a href="index.php" class="shop-now-btn">Back</a>
        <script src="script.js"></script>


    </div>
</div>

</body>
</html>
