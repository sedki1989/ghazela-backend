ghazela: Backend
=======

Installation
========================

1. Clone or download repository

    https://github.com/edlef/symfony-demo.git

2. Run composer

	composer install
 
3. Run CMD

   1. php bin/console doctrine:database:create
   2. php bin/console doctrine:schema:update --force
   3. php bin/console doctrine:fixtures:load
   4. php bin/console server:run 127.0.0.1:8001
