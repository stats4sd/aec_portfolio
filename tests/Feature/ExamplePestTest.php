<?php

test('example', function () {
    expect(true)->toBeTrue();
});


test('todo')->todo();

test('this will be skipped because is has no function, but is not explictly marked as a todo');
