# Agroecology Coalition Portfolio
A proof of concept for the AEC Consortium Project Management / Assessment System

# Development
This platform is built using Laravel/PHP. The front-end is written in VueJS and the admin panel uses Backpack for Laravel.
## Setup Local Environment
1.	Clone repo: `git clone git@github.com:stats4sd/.......... `
2.	Copy `.env.example` as a new file and call it `.env`
3.	Update variables in `.env` file to match your local environment:
a.	Check APP_URL is correct
b.	Update DB_DATABASE (name of the local MySQL database to use), DB_USERNAME (local MySQL username) and DB_PASSWORD (local MySQL password)
c.	If you need to test the Kobo link, make sure QUEUE_CONNECTION is set to `database` or `redis` (and that you have redis setup locally). Also add your test KOBO_USERNAME and KOBO_PASSWORD / ODK_USERNAME and ODK_password
d.	If you need to test real email sending, update the MAIL_MAILER to mailgun, and copy over the Stats4SD Mailgun keys from 1 Password
4.	Create a local MySQL database with the same name used in the `.env` file
5.	Copy `auth.json.example.json` as a new file calling it `auth.json` and add the login details for the Stats4SD Spatie account - this is required for the Media Library Pro package
6.	Run the following setup commands in the root project folder:
```
composer install
php artisan key:generate
php artisan backpack:install
php artisan telescope:publish
php artisan updatesql
npm install
npm run dev
cd scripts/Rscript && Rscript -e "renv::restore()"
```
7.	Migrate the database: `php aritsan migrate:fresh --seed` (or copy from the staging site)

