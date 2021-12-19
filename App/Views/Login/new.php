<?php include_once '../App/Views/header.php'; ?>

<h1>Log in</h1>

<form method="post" action="http://localhost/PHP/Other/MVC/public/login/create">

    <div>
        <label for="inputEmail">Email address</label>
        <input id="inputEmail" name="email" placeholder="email address" autofocus value=<?= isset($user->email) ? htmlspecialchars($user->email) : ''; ?>>
    </div>

    <div>
        <label for="inputPassword">Password</label>
        <input type="password" id="inputPassword" name="password" placeholder="Password">
    </div>

    <button type="submit">Sign up</button>

</form>

<?php include_once '../App/Views/footer.php'; ?>