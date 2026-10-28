<?php

// Vercel's filesystem is read-only except /tmp, so Laravel's storage dirs live there.
foreach (['views', 'framework/cache', 'framework/sessions', 'framework/views'] as $dir) {
    if (!is_dir("/tmp/$dir")) {
        mkdir("/tmp/$dir", 0777, true);
    }
}

require __DIR__ . '/../public/index.php';
