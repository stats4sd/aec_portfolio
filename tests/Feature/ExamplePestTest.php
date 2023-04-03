<?php

test('example', function () {
    expect(true)->toBeTrue();
});


test('todo')->todo();

test('this will be skipped because is has no function, but is not explictly marked as a todo');


// CRUD panels - Continents, Regions, Countries, Users, Admin User Invites CRUD panels are accessible by site admin only
test('Continents CRUD panel is accessible by site admin only')->todo();

test('Regions CRUD panel is accessible by site admin only')->todo();

test('Countries CRUD panel is accessible by site admin only')->todo();

test('Users CRUD panel is accessible by site admin only')->todo();

test('Admin User Invites CRUD panel is accessible by site admin only')->todo();


// CRUD panels - Red lines, Principles CRUD panels are accessible by site admin and site manager only
test('Red lines CRUD panel is accessible by site admin and site manager only')->todo();

test('Principles CRUD panel is accessible by site admin and site manager only')->todo();


// CRUD panels - Red lines, Principles CRUD panels are accessible by site admin, site manager and institutional admin only
test('Score tags CRUD panel is accessible by site admin, site manager and institutional admin only')->todo();


// TBC: CRUD panels - Institutions, Projects CRUD panels are accessible by all users


