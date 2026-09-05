<?php

use Illuminate\Support\Facades\Artisan;

if (file_exists(__DIR__ . '/../storage/installed.lock')) {
    die("Application déjà installée.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $appName = $_POST['app_name'] ?? "PICSOU";
    $dbHost  = $_POST['db_host'] ?? "127.0.0.1";
    $dbPort  = $_POST['db_port'] ?? "3306";
    $dbName  = $_POST['db_name'] ?? "";
    $dbUser  = $_POST['db_user'] ?? "";
    $dbPass  = $_POST['db_pass'] ?? "";

    // Génération du contenu .env
    $envContent = "
APP_NAME=\"$appName\"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=postgres
DB_HOST=$dbHost
DB_PORT=$dbPort
DB_DATABASE=$dbName
DB_USERNAME=$dbUser
DB_PASSWORD=\"$dbPass\"

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
";

    $envPath = __DIR__ . '/../.env';

    if (!is_writable(__DIR__ . '/../')) {
        die("Impossible d'écrire le fichier .env : droits insuffisants.");
    }

    file_put_contents($envPath, trim($envContent));

    // Charger Laravel après écriture .env
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // Générer APP_KEY
    Artisan::call('key:generate', ['--force' => true]);

    // Migration
    Artisan::call('migrate', ['--force' => true]);

    // Seeder
    Artisan::call('db:seed', ['--force' => true]);

    // Lock fichier installation
    file_put_contents(__DIR__ . '/../storage/installed.lock', date('Y-m-d H:i:s'));

    echo "<h2>Installation terminée.</h2>";
    echo "<a href='../'>Accéder à l'application</a>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PICSOU - Installation</title>
    <style>
        body{font:14px Tahoma;}
        .header,.footer,.content{
            width:600px;
            margin:0 auto;
            border:1px solid #eee;
            padding:10px;
            background:#EAEAEA;
        }
        input{
            padding:.5em;
            border:1px solid #ccc;
            border-radius:4px;
        }
    </style>
</head>
<body>

<div class="header"><b>PICSOU - Installation</b></div>

<div class="content">
<form method="POST">

    <h3>Application</h3>
    <label>Nom Application</label><br>
    <input type="text" name="app_name" value="PICSOU" style="width:100%"><br><br>

    <h3>Base de données</h3>

    <label>Host</label><br>
    <input type="text" name="db_host" value="127.0.0.1" style="width:100%"><br><br>

    <label>Port</label><br>
    <input type="text" name="db_port" value="3306" style="width:100%"><br><br>

    <label>Nom base</label><br>
    <input type="text" name="db_name" style="width:100%"><br><br>

    <label>Utilisateur</label><br>
    <input type="text" name="db_user" style="width:100%"><br><br>

    <label>Mot de passe</label><br>
    <input type="password" name="db_pass" style="width:100%"><br><br>

    <input type="submit" value="Installer" onclick="return confirm('Confirmer l’installation ?')">
</form>
</div>

</body>
</html>
