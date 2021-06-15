<?php

/**
 * do clean in cli
 *
 */

require "bittorrent.php";

$fd = fopen(sprintf('%s/nexus_cleanup_cli.lock', sys_get_temp_dir()), 'w+');
if (!flock($fd, LOCK_EX|LOCK_NB)) {
    do_log("can not get lock, skip!");
}
register_shutdown_function(function () use ($fd) {
    fclose($fd);
});

$force = 0;
if (isset($_SERVER['argv'][1])) {
    $force = $_SERVER['argv'][1] ? 1 : 0;
}

try {
    if ($force) {
        require_once(ROOT_PATH . 'include/cleanup.php');
        $result = docleanup(1, true);
    } else {
        $result = autoclean();
    }
    do_log("[CLEANUP_CLI DONE!] $result");
} catch (\Exception $exception) {
    do_log("ERROR: " . $exception->getMessage());
    throw new \RuntimeException($exception->getMessage());
}

