# CSV
PHP CSV file reader

## Usage

```php
<?php

require './vendor/autoload.php'

use Aznoqmous\CSV;

$csv = new CSV('/path/to/file.csv');

// get parsed data objects
$content = $csv->load();
$content = $csv->load('/path/to/file.csv'); // load another file
$content = $csv->content;
```
