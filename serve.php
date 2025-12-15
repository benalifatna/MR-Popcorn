<?php
echo "\nPress Ctrl-C to shutdown this server\n\n";

exec("php -S localhost:8080 -t public");
