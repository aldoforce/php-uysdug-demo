<?php
echo "hello";

debug('hello from console');

function debug($s) {
    $stdout = fopen('php://stdout', 'w');
      fwrite($stdout, $s);
      fclose($stdout);
}

?>
