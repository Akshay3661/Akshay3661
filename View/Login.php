<?php
session_start();
?>
<!doctype html>
<html lang="en">

<head>
    <title>sign_Up-Webcreta Technologies</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">

    <link rel="stylesheet" href="./style.css">

</head>

<body class="text-center">
    <div class="form-signin bg-light">
        <form action="../Models/LoginHandler.php" method="post">
            <img class="mb-4" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_l8CHB-dnWGliErDYPan3Xn89RCKnQuUroklA_WndhbiuiWhsl-tAomw0kI3revYg4-o&usqp=CAU" alt="" width="72">
            <h1 class="h3 mb-3 fw-normal">Please Login</h1>


            <div class="form-floating">
                <input type="text" class="form-control" name="identifier" id="floatingInput" placeholder="name@example.com">
                <label for="floatingInput">Username or Email</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="Password">
                <label for="floatingPassword">Password</label>
            </div>
            <?php
            if (isset($_SESSION['error_message'])) {
                echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
                unset($_SESSION['error_message']); 
            }
            ?>

            <button class="w-100 btn btn-lg btn-dark" type="submit">Login</button>
            <p class="mt-5 mb-3 text-muted">don't have account then <a href="./RegisterUser.PHP" class="text-decoration-none">Sign-Up</a></p>
            <p class="mt-5 mb-3 text-muted">&copy; 2017–2023</p>
        </form>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous">
    </script>
</body>

</html>