# Blackboard ReST API Wrapper

Lightweight wrapper for https://developer.blackboard.com/portal/displayApi

See also https://github.com/blackboard/BBDN-REST-Demo-PHP

## Installation

via [composer][2]:

    composer require razorbacks/blackboard-rest-api-wrapper

## Usage

```php
// setup
require_once __DIR__.'/vendor/autoload.php';

use razorbacks\blackboard\rest\Api;

$server = 'https://learn.uark.edu';
$applicationId = 'your-application-id';
$secret = 'secret';

$blackboard = new Api($server, $applicationId, $secret);

// create a new manual grade column for a course
$courseId = '_123_1';
$gradeColumn = [
    'name' => 'Example Assignment',
    'description' => 'This is something we did for course credit.',
    'score' => [
        'possible' => 10,
    ],
    'availability' => [
        'available' => 'Yes',
    ],
];

// create and hydrate the model with new ID
$gradeColumn = $blackboard->post("/courses/{$courseId}/gradebook/columns", $gradeColumn);

// assign a grade to a student
$userId = '_65538_1';
$blackboard->patch("/courses/{$courseId}/gradebook/columns/{$gradeColumn['id']}/users/{$userId}", [
    'score' => 9,
]);
```

See the [tests](./tests) for more examples.

## Testing

The test suite is composed of integration tests making real network calls.
See [the documentation][1] for setting up a vagrant virtual machine server.

[1]:https://community.blackboard.com/docs/DOC-1649-developer-virtual-machine
[2]:https://getcomposer.org/
