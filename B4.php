<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">

    <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Script Injection</title>
    <meta name="viewport" content="width=device-width; initial-scale=1.0">

    <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1>Script Injection</h1>
        <form action="#" method="POST">
            <div class="form-group">
                <label for="item">Item *</label>
                <input type="text" name="item" id="item" class="form-control" placeholder="Item erfassen" maxlength="255" required />
            </div>
            <button type="submit" name="submit" class="btn btn-default">Senden</button>
            <button type="reset" name="reset" class="btn btn-default">Löschen</button>
        </form>

        <div>
            <h3>Augabe:</h3>
            <?php

            // nur wenn Daten per POST gesendet wurden
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                
                //echo $_POST['item'];
                echo htmlspecialchars($_POST['item']);

            }

            ?>
        </div>
    </div>
</body>
</html>