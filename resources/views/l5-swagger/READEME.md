# l5-swagger package usage and help info

## Description
This is the usage of the l5-swagger package for generating the swagger annotations using the latest openai 3.0 standards which is an upgrade on 2.0 this package has a dependecy package laravel/swagger-php which has a genetor classes which simple the process, this class uses openai annotations in the controllers to generate the views including any validations without messing with any json files. for more info check [here](https://github.com/OAI/OpenAPI-Specification)

## Contributing
Shout of to the guys who created the packages and made life easy for us for more on the package documentation check this link [here](https://github.com/DarkaOnLine/L5-Swagger)

## .env expected variables are set
L5_SWAGGER_CONST_HOST="${APP_URL}/api"
L5_SWAGGER_UI_FILTERS=true

## Instructions
1. Clone this repository which resides on branch feature/laravel-swagger
2. need to run composer install to install all the packages and dependencies
3. after all depencies are install simple run sail php artisan l5-swagger:generate
4. visit localhost to view the new swagger-ui
