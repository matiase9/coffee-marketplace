

Requirements
------------

* [Composer][2].
* [Docker][3].

Installation
-------------

* Run 
```
    docker-compose up -d
``` 
* Access to PHP container (coffee-php)
```
    docker exec -it coffee-php /bin/bash
```
* Run composer
```
    composer install
```

* Update Database Schema
```
    bin/console doctrine:schema:update --force
```

* Run dummy data to create the access users
```
    bin/console doctrine:fixtures:load
```

* Exit the container and add the next line in the hosts file (/etc/hosts)
```
    127.0.0.1       local.coffee-marketplace.com
```  

Api
---
- USER
With the Dummy data we have two users created:

CUSTOMER: 
> `username => customer` \
> `password => customer123`

ADMIN:
> `username => admin`\
> `password => admin123`


```
    http://local.coffee-marketplace.com/api/login_check
```
- COFFEE
```
    http://local.coffee-marketplace.com/api/coffee/{id}
    http://local.coffee-marketplace.com/api/coffee/new
    http://local.coffee-marketplace.com/api/coffee/delete/{id}
```
- ORDER
```
    http://local.coffee-marketplace.com/api/order/new
```

Examples
---------
These are some examples to use the API.

https://documenter.getpostman.com/view/6606267/S1TWzGor?version=latest

Test Case
---------
I created a test case to User connection. This test is compatible with Mac users.\

To run the test:
- Connect to container PHP
```
    docker exec -it coffee-php /bin/bash
```
- Run the command
```
    bin/phpunit tests/UserTest.php
```

Pending
-------
 - Update services to Coffee Model.
 - Test cases to Coffee and Order services.

References
---------

* [Symfony][1].
* [View Routes in Postman][5]

[1]: https://symfony.com
[2]: https://getcomposer.org/
[3]: https://www.docker.com/
[4]: http://local.coffee-marketplace.com
[5]: https://documenter.getpostman.com/view/6606267/S1TWzGor?version=latest

