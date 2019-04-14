# FileTransferServer
Server for [FileTransferClient](https://github.com/sleeyax/FileTransferClient)

## Production
Clone the repo to your VPS and run:
```
$ composer install --no-dev
$ npm install --production
$ mv .env.example .env
```
Edit `.env` to change your database settings and run  `php artisan migrate` to create the tables. 

For server requirements, see the [laravel documentation](https://laravel.com/docs/5.8/installation#server-requirements)

## Development
Same procedure as the Production steps, but run
```
$ composer install
$ npm install
```
to install dev dependencies

## Links
[FileTransferClient](https://github.com/sleeyax/FileTransferClient) - client source code

[laravel-cors](https://github.com/barryvdh/laravel-cors) - CORS policy management module used in this project
