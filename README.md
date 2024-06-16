# Pluma Framework
A PHP Web Application Framework

[![CircleCI](https://circleci.com/gh/ssa-academy/yggdrasil/tree/develop.svg?style=svg&circle-token=a0debc833ce9b95bf19d33d2ca511772b0cf0fc5)](https://circleci.com/gh/ssa-academy/yggdrasil/tree/develop)

<hr>

### About Pluma
Pluma is a web management system framework, automating most of the tasks when developing CRUD-like applications.
The software is based on [Laravel](https://github.com/laravel/laravel), a framework with expressive, elegant syntax that provide power tools required for large, robust applications. Pluma adds various view scaffolding, console commands, and an administrative dashboard to get starting in a project painlessly and swiftly.

### Features
* Administrative Dashboard
* User Management
* Permission Based Access Control and Management
* Taxonomic Resource Management
* Widgets Management
* Themes Management
* Extendable through Modules Development

----
### Prerequisites
* PHP ^7.2
* Composer v1
* Node v14.21.3 (npm v6.14.18)

### Installation
* **Step 1: Install via Composer**

    Pluma runs like a Laravel project:
    ```
    composer create-project --prefer-dist pluma/pluma [folder-name]
    ```
    Alternatively, you may clone this repository directly.

* **Step 2: Run the installer**

    After the project is downloaded, navigate to the project folder and run:
    ```
    php pluma app:install
    php pluma passport:install
    ```

* **Step 3: Set the theme and run the application**
    ```
    php pluma theme:activate
    select [dovetail]

    php pluma serve
    ```

    We are good to go!

Start up a local development server with `php pluma serve`, and visit http://localhost:8000/admin.

-----
##### Administrative User
By default, no users will be installed upon installation.
To generate a user, run the console command:

```
php pluma make:user
```

And follow the instructions displayed. After retrieving the new credentials, visit http://localhost:8000/login

##### Generating Modules
To generate scaffolding for a new module, run the console command:

```
php pluma make:module ModuleName
```

Run the console command `php pluma list` to view all available commands.

----

### Deploying
<small>Visit the `docs/deploying` folder to view an in-depth write-up about deploying to production.</small>

Run the optimize command:
```
php pluma optimize
```

### License
The Pluma framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
