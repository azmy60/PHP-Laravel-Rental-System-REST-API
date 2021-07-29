<h1 align="center"> PHP-Laravel-Rental-System-REST-API </h1>
<p>
  <img alt="Version" src="https://img.shields.io/badge/version-1.0.0-blue.svg?cacheSeconds=2592000" />
  <a href="https://github.com/cagilceren/PHP-Simple-TO-DO-List-REST-API/blob/main/README.md" target="_blank">
    <img alt="Documentation" src="https://img.shields.io/badge/documentation-yes-brightgreen.svg" />
  </a>
  <a href="https://github.com/cagilceren/PHP-Simple-TO-DO-List-REST-API/graphs/commit-activity" target="_blank">
    <img alt="Maintenance" src="https://img.shields.io/badge/Maintained%3F-yes-green.svg" />
  </a>
</p>
<p>

 </p>

<br>

This project has been created as a part of self-learning.

In this project, I have created a Laravel REST API Service for a rental system App. 

This rental system is consisting of three components: 
[User](), [Inventory]() and [Rental](). They are representing three different tables in SQL. Accordingly, I have created 3 migration files under [/database/migration]() folder.

User table has the information about the users of the system:
- name
- email
- password

In inventory table there are different objects and their properties:
- inventory no
- description
- count (number of the item)
- condition (must be between 1 and 5)
- serial no
- lendability

Rental table represents the renting information:
- inventory id (as foreign key)
- name
- adress (of the person who borrows the item)
- email (of the person who borrows the item)
- phone (of the person who borrows the item)
- deposit
- borrow date
- due date
- return date
- comment (optional)
- lending user
- receiving user

In addition, I have created 3 Models under [/App/Models]() folder, to be able to communicate with the database.
Accordingly, 3 controller classes have been created under [/App/Http/Controllers](), which have the CRUD functions and logic controls.
Finally, the routes for CRUD functions have been added in [web.php]() file.

## Build With

- PHP OOP
- Laravel
- Eloquent ORM
- MySQL

## Tools & Technologies

- Postman
- JSON
- Rest API
- Docker

## Highlights

In this project I have used different modules for different compartments in the project, so that the project is easily upgradeable.

During the project I have used Laravel framework, which offer many facilities while creating PHP REST API Services. That is why, I didn't have to manually deal with many things, like SQL Injection and creating JSON documents (see: [PHP Rest API](https://github.com/cagilceren/PHP-Simple-TO-DO-List-REST-API.git)).

Thanks to the Laravel framework, I have created CRUD functions based on [RESTful web API design](https://docs.microsoft.com/en-us/azure/architecture/best-practices/api-design).

Moreover, I have added some logic controls. I have checked,
- if "count" and "deposit" are positive numbers.
- if "condition" is between 1 and 5.
- if email has the right format.
- if the item is lendable, before adding some records to [RentalController]().
- if there is an item with the given inventory id, before adding records to [RentalController]().
- if "count" is already "0" before adding records to [RentalController]().

During the logic controls I have checked the possible error resources and threw a related Exception, when it was needed. Successful requests return "HTTP 200 OK" while unsuccessful ones return "HTTP 400 Bad Request", "HTTP 412 Precondition Failed" or "HTTP 404 Not Found".


Additionally, I have created a `getAllRentalByInventoryId()` with the route `/inventory{id}/rental` function in order to get all existing rental data with the given id of inventory and which are not returned (`` returnDate = null; ``) or which are returned.

## TODO

- User authorization
- Import a search function


## Usage

> 1) Clone the repository to your local machine

```sh
$ git clone https://github.com/cagilceren/PHP-Laravel-Rental-System-REST-API.git
```

> 2) Install [Docker](https://www.docker.com/products/docker-desktop).

> 3) Follow the [link](https://laravel.com/docs/8.x/installation#getting-started-on-macos) for laravel installation.

> 4) Migrate your database
```sh
$ docker ps #list your running docker containers.
$ docker exec -it {dockerid} /bin/bash
# php artisan migrate
```
> 5) Install Postman and import the file "[rental.postman_collection.json]()".

> [Download Postman](https://www.postman.com/downloads/)

## Authors

<img src="https://avatars.githubusercontent.com/u/45261915?v=2" width="25" height="25"> **Cagil Ceren Aslan**




- Github: [@cagilceren](https://github.com/cagilceren)

## Contributing

I am happy to have some improvement ideas for my project :)
