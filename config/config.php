<?php
// Load Env Config
(new Dotenv\Dotenv('../'))->load();

$env = strtolower(getenv('ENV'));
$assets = json_decode(file_get_contents('../asset-manifest.json'), true);
switch ($env) {
    case 'dev':
        $assets = array_combine(array_keys($assets), array_keys($assets));
        break;
}

$isProd = ($env === 'prod');
$container = new Slim\Container([
    'settings' => [
        'env' => $env,
        'base_path' => realpath(__DIR__.'/../'),

        'app.name'        => 'Won For The Land',
        'app.description' => 'Celebrating the Cleveland Cavaliers 2016 Championship that ended a 52-year title drought for the city of Cleveland #OneForTheLand',
        'app.keywords'    => '#OneForTheLand,Cleveland,Cavs,Championship,Lebron James,NBA,Cleveland Cavaliers,Golden State Warriors,NBA Finals,Game 7,Archive',

        'app.urls.assets' => '/build',
        'app.paths.js'    => '/build/js',
        'app.paths.css'   => '/build/css',
        'app.assets'      => $assets,

        'app.paths.upload_path' => '/public/uploads',
        'app.paths.upload_dir'  => '/uploads',

        'hashids.salt'       => 'NfDPDtGEHmzC7WPHfv6N73WxzUKbbyE2',
        'hashids.min-length' => 3,

        'db.host'    => getenv('DB_HOST'),
        'db.name'    => getenv('DB_NAME'),
        'db.user'    => getenv('DB_USER'),
        'db.pass'    => getenv('DB_PASS'),
        'db.charset' => 'utf8',

        'ga.tracking_id' => $isProd ? 'UA-67735723-5' : null,
    ],
]);

return $container;