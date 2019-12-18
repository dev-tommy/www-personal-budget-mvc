<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Home view</title>
</head>

<body>
    <h1>Hello <?php echo htmlspecialchars($name); ?> !!</h1>
    <p>Home view test page</p>
    <ul>
        <?php foreach ($colours as $colour) : ?>
            <li><?php echo htmlspecialchars($colour); ?></li>
        <?php endforeach; ?>
    </ul>

</body>

</html>