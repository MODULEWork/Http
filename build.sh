composer install --prefer-source --no-interaction --dev
phpunit
php apigen/apigen.php --source src/ --destination build/docs/