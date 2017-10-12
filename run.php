<?php
require_once './vendor/autoload.php';

echo "START\n";

$memory = getenv('KBC_CONTAINER_MEMORY') !== false ? getenv('KBC_CONTAINER_MEMORY') : '64m';
$runTime = getenv('KBC_CONTAINER_RUN_TIME') !== false ? getenv('KBC_CONTAINER_RUN_TIME') : '60';

$command = "docker run --rm"
    . " --memory " . escapeshellarg($memory)
    . " --memory-swap " . escapeshellarg($memory)
    . " alpine sleep "
    . escapeshellarg($runTime)
    ;

echo "Running command: {$command}\n";

$process = new \Symfony\Component\Process\Process($command);
$process
    ->setTimeout(null)
    ->mustRun();

echo "END\n";
