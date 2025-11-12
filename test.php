<?php
require 'vendor/autoload.php';
use Ramsey\Uuid\Uuid;

$secret = password_hash("Alexjustice23", PASSWORD_ARGON2I, ['cost' => 12]);
echo $secret;
echo "\n";
echo Uuid::uuid4()->toString();