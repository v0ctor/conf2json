# conf2json
Library that converts PHP configuration files to JSON. It also includes a command-line interface.

PHP configuration files are regular PHP files that return an array. They may be used to define configurations and language strings, among others.

```PHP
<?php

return [
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'hogwarts',
        'username' => 'severus',
        'password' => '4lw4y5',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ]
];
```

In some cases it is necessary to convert these files to JSON to make them readable from other environments.

## Features
* Converts single files and directories recursively.
* Allows to export minified and human-readable JSON files.
* Includes a command-line interface to use the library from the shell.

## License
This software is distributed under the MIT license. Please read LICENSE for information on the software availability and distribution.

## Installation
This library requires Composer and PHP 7 or later.

```Shell
composer require victordzmr/conf2json
```

## Usage

### PHP library
```PHP
use victordzmr\conf2json;

$conf2json = new conf2json([, input [, output [, pretty [, recursive [, verbose]]]]]);
$conf2json->run();
```

### Command-line interface
```Shell
vendor/bin/conf2json [, input [, output [, pretty [, recursive [, verbose]]]]]
```

### Arguments
* `input` is the file or directory that contains the files to be converted. Default: current directory.
* `output` is the directory where the output files will be saved. Default: current directory.
* `pretty` defines whether the output files must be encoded using the [JSON_PRETTY_PRINT](https://php.net/manual/en/json.constants.php) option. Default: true.
* `recursive` determines whether the conversion must be recursive when the input is a directory. Default: true.
* `verbose` defines whether the library should print information to standard and error outputs. Default: false from the library and true from the CLI.