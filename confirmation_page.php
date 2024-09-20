
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Confirmation Page</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        .confirmation-container {
            text-align: center;
            margin: 100px auto;
            max-width: 400px;
        }

        .confirmation-msg {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
        }

        .btn {
            width: 45%;
        }
    </style>
</head>
<body>

<div class="confirmation-container">
    <?php
    if (isset($_GET['error']) && $_GET['error'] == 'true') {
        echo '<h3 class="confirmation-msg">An error occurred!</h3>';
    } else {
        echo '<h3 class="confirmation-msg">Song deleted successfully!</h3>';
    }
    ?>
    <div class="btn-container">
        <a href="your_previous_page.php" class="btn btn-secondary">Go Back</a>
        <a href="indexhome.php" class="btn btn-primary">Go to Home</a>
    </div>
</div>

</body>
</html>
