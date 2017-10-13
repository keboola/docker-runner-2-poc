<?php
require_once './vendor/autoload.php';


function killContainer($containerId) {
    $command = sprintf("docker kill %s", escapeshellarg($containerId));
    $killProcess = new \Symfony\Component\Process\Process($command);
    $killProcess->mustRun();
}

echo "START\n";

$memory = getenv('KBC_CONTAINER_MEMORY') !== false ? getenv('KBC_CONTAINER_MEMORY') : '64m';
$timeout = getenv('KBC_CONTAINER_TIMEOUT') !== false ?  (int) getenv('KBC_CONTAINER_TIMEOUT') : '240';
$runTime = getenv('KBC_CONTAINER_RUN_TIME') !== false ? getenv('KBC_CONTAINER_RUN_TIME') : '120';

$containerId = uniqid('kbc-run', true);

$command = "docker run --rm"
    . " --memory " . escapeshellarg($memory)
    . " --memory-swap " . escapeshellarg($memory)
    . " --name " . escapeshellarg($containerId)
    . " alpine sleep "
    . escapeshellarg($runTime)
    ;

echo "Running command: {$command}\n";
echo "Container timeout: {$timeout}\n";

$process = new \Symfony\Component\Process\Process($command);
$process->setTimeout($timeout);

// handle job termination
pcntl_signal(SIGTERM, function ($signo) use($containerId)  {
    // if we just kill the current process it will return 143 but the docker container remains running on host
    // so we have to use docker kill
    print "Job stopped by signal $signo";
    killContainer($containerId);
    print "Child container killed.";
    exit(0);
});

$process->start(function ($type, $buffer) {
    if (mb_strlen($buffer) > 64000) {
        $buffer = mb_substr($buffer, 0, 64000) . " [trimmed]";
    }
    if ($type === \Symfony\Component\Process\Process::ERR) {
        print 'ERR:' . $buffer . "\n";
    } else {
        print $buffer . "\n";
    }
});

try {
    do {
        // check the signal
        pcntl_signal_dispatch();
        $process->checkTimeout();
        sleep(1);
    } while ($process->isRunning());
} catch (\Symfony\Component\Process\Exception\ProcessTimedOutException $e) {
    print "Container run timeout\n";
    killContainer($containerId);
    print "Child container killed.";
}

var_dump($process->getExitCode(), $process->getExitCodeText());

echo "END\n";
