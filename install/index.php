<?php
#region PHP Code
declare(strict_types = 1);

$error = '';
$dbObj = null;
$dbTablesDir = __DIR__ . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR;

if(count($_POST) > 0){
    // Check if PHP version is compitable.
    if (version_compare(PHP_VERSION, '8.0.0', '<')) {
        $error = 'Error: PHP version 8.0.0 or higher is required. You have version ' . PHP_VERSION;
    }

    // Get database information. 
    if($error == '' && isset($_POST)){
        $hostname = sanitizeString($_POST['db-hostname'] ?? '');
        $username = sanitizeString($_POST['db-username'] ?? '');
        $password = sanitizeString($_POST['db-password'] ?? '');
        $dbName   = sanitizeString($_POST['db-name'] ?? '');

        if($hostname == '' && $username == '' && $password == '' && $dbName == ''){
            $error = "All form fields need to be filled.";
        }
    }

    // Connect to the database.
    if($error == ''){
        try{
            $dbObj = @(new mysqli($hostname, $username, $password, $dbName));
        } catch(Throwable $e){
            $error = "Could not connect to Database. ERROR: " . $e->getMessage();
        }
    }

    // Add tables and data to the database.
    if($error == ''){
        $files = getFilesFromDir($dbTablesDir);
        $res = execSqlFiles($files, $dbObj);
        if($res === false){
            $error = "Some thing went wrong while creating the database tables.";
        }
    }

    // redirect user to the login page.
    if($error == ''){
        header("Location: /login");
    }
}

function sanitizeString(string $text): string{
    return $text;
}

function getFilesFromDir(string $dir): array{
    $files = [];
    $filesDirs = scandir($dir);
    foreach($filesDirs as $fileDir){
        if(is_file($dir . $fileDir)){
            $files[] = $dir . $fileDir;
        }
    }
    return $files;
}

function execSqlFiles(array $files, mysqli $dbObj): bool{
    foreach($files as $file){
        $res = execSqlFile($file, $dbObj);
        if($res == false){
            return false;
        }
    }
    return true;
}

function execSqlFile(string $file, mysqli $dbObj): bool{
    $content = file_get_contents($file);
    $res = $dbObj->multi_query($content);
    if($res !== false){
        return true;
    }
    return false;
}
#endregion
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProdManager Installer</title>
    <style>
        *{
            font-family: Arial, Helvetica, sans-serif;
            box-sizing: border-box;
        }

        body{
            font-size: 16px;
        }

        .error{
            border: 1px solid rgba(200, 0, 0, .8);
            background-color: rgba(200, 0, 0, .2);
            color: #2e2e2e;
            padding: 15px 20px;
        }
    </style>
</head>
<body>
    <div class="intaller-cont">
        <div class="installer-cont-inner">
            <div class="logo-cont">
                <div class="logo">
                    <img src="/asserts/img/logo/logo.png" alt="ProdManager LOGO" class="logo-img">
                </div>
            </div>
            <div class="error-cont">
                <?php if($error): ?>
                    <div class="error">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-cont">
                <form method="post" action="/install/" id="installer-id">
                    <label for="db-hostname">Database Hostname:</label>
                    <input type="text" name="db-hostname" id="db-hostname" placeholder="localhost">

                    <label for="db-username">Database Username:</label>
                    <input type="text" name="db-username" id="db-username" placeholder="root">

                    <label for="db-password">Database User Password:</label>
                    <input type="password" name="db-password" id="db-password" placeholder="root">

                    <label for="db-name">Database Name:</label>
                    <input type="text" name="db-name" id="db-name" placeholder="prodmanager">

                    <label for="admin-username">ProdManager Admin Username:</label>
                    <input type="text" name="admin-username" id="admin-username" placeholder="admin">

                    <label for="admin-username">ProdManager Admin Password:</label>
                    <input type="text" name="admin-password" id="admin-password" placeholder="admin">

                    <input type="submit" value="Submit">
                </form>
            </div>
            
        </div>
    </div>
</body>
</html>