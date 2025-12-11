<?php
echo "\nPress Ctrl-C to shutdown this server\n\n";

exec("php -S localhost:8000 -t public");
