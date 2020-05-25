# Integer_Net Deployer Timer

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


This is a recipe for [Deployer](https://deployer.org/) that lets you track the duration of all tasks

It can create a CSV file in the form of
```
BEGIN,deploy,1553682974.4,0
BEGIN,build,1553682974.4,0
END,build,1553682975,0.6
BEGIN,copy,1553682975,0
END,copy,1553682978.5,3.5
BEGIN,release,1553682978.5,0
END,release,1553682979.3,0.8
END,deploy,1553682979.3,4.9
```

The columns are:
- `BEGIN` or `END`, marking time where a task started or finished
- task name
- Unix timestamp (as float)
- Duration of task in seconds (only in `END` rows)

## Installation

1. Require via composer
    ```
    composer require integer-net/deployer-timer
    ```

## Usage

In your `deploy.php` file:

1. Include the recipe:
    ```
    require __DIR__ . '/vendor/integer-net/deployer-timer/recipe/timer.php';
    ```
2. Configure the timer **at the end**:
    ```
    after('deploy', timer()->createCsvResultTask('path/to/file.csv'));

    ```
    
    `timer()` must be called after all other tasks are defined. The generated task to create a CSV result file should be added at the end of the task/group that should be timed (e.g. `deploy`)

## Troubleshooting

If you receive errors about missing classes, include the standalone autoloader:

```
require __DIR__ . '/vendor/integer-net/deployer-timer/autoload.php';
```

This way, you can use the recipe without relying on the composer autoloader (e.g. when running deployer as phar)

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
composer test
```

Runs unit tests, mutation tests and static analysis

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email fs@integer-net.de instead of using the issue tracker.

## Credits

- [Fabian Schmengler][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.txt) for more information.

[ico-version]: https://img.shields.io/packagist/v/integer-net/deployer-timer.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/integer-net/deployer-timer/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/integer-net/deployer-timer.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/integer-net/deployer-timer.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/integer-net/deployer-timer.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/integer-net/deployer-timer
[link-travis]: https://travis-ci.org/integer-net/deployer-timer
[link-scrutinizer]: https://scrutinizer-ci.com/g/integer-net/deployer-timer/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/integer-net/deployer-timer
[link-downloads]: https://packagist.org/packages/integer-net/deployer-timer
[link-author]: https://github.com/schmengler
[link-contributors]: ../../contributors
