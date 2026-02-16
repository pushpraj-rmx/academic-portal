<?php

return [
    'grades' => [
        ['name' => 'A+', 'min' => 90, 'max' => 100, 'is_passing' => true],
        ['name' => 'A', 'min' => 80, 'max' => 89.99, 'is_passing' => true],
        ['name' => 'B+', 'min' => 70, 'max' => 79.99, 'is_passing' => true],
        ['name' => 'B', 'min' => 60, 'max' => 69.99, 'is_passing' => true],
        ['name' => 'C', 'min' => 50, 'max' => 59.99, 'is_passing' => true],
        ['name' => 'D', 'min' => 40, 'max' => 49.99, 'is_passing' => true],
        ['name' => 'F', 'min' => 0, 'max' => 39.99, 'is_passing' => false],
    ],
];
