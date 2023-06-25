# Agroecology Coalition (AEC) Portfolio
A proof of concept for the AEC Consortium Project Management / Assessment System

# Development
This platform is built using Laravel/PHP. The admin panel uses [Backpack for Laravel PRO.](https://backpackforlaravel.com/products/pro-for-one-project)

## Setup Local Environment
1.	Clone repo: `git@github.com:stats4sd/aec_portfolio.git`
2.	Copy `.env.example` as a new file and call it `.env`
3.	Update variables in `.env` file to match your local environment:
    1.	Check APP_URL is correct
    2.	Update DB_DATABASE (name of the local MySQL database to use), DB_USERNAME (local MySQL username) and DB_PASSWORD (local MySQL password)
4.	Create a local MySQL database with the same name used in the `.env` file
5.	Run the following setup commands in the root project folder:
```
composer install
php artisan key:generate
php artisan backpack:install
npm install
npm run dev
```
6.	Migrate the database: `php aritsan migrate:fresh --seed`



## TODOS
- [ ] Add export data button to dashboard and initiatives page.
- [ ] Turn summary pages into tabs to test out.
- [ ] Resize principle summary block to always fit onto the page. 
- [ ] Get Import Initiatives Working
- [ ] At the "Institution Data Main View", add navigation to:
  - Dashboard
  - Portfolios
  - Initiatives
  - Additional Assessment Criteria
- [ ] Permanently hide the sidebar and confirm that Institutional users can still access all the things...  


- [ ] For Site Managers, Tidy up and document main sidebar view.

## TESTS
- [ ] Workflow for creating a new institution + inviting members
- [ ] Workflow for a member deleting their account
- 
----
