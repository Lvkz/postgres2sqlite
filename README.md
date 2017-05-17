# postgres2sqlite
A PHP Class to export data from PostgreSQL environment and prepare a INSERT file to SQLite database.

## Code Usage
```php
import 'ExportToSQLite.php'

$host = 'your_host_address';
$port = 'your_port';
$database = 'your_database_name';
$user = 'your_database_username';
$password = 'your_database_password';

//I'm using a Postgres App for Mac so for me is at: /Applications/Postgres.app/Contents/Versions/9.6/bin/pg_dump
$pg_dump_route = 'location_of_pg_dum';

# Create a new Object.
$dump = new ConvertToSQLite($host, $port, $database, $user, $password);

#Tell the class de pg_dump route.
$dump->SetAppLocation($pg_dump_route);

#The name of the postgres dump - in case you want to use it for something else.
$dump->SetDumpLocation('php_dump.sql');

#Execute conversion.
$dump->ProcessConversion();
```

## Data Usage
When you run the code above you will get a message like this: 
```html
Conversion ended successfully. You may want to look into db.sqlite3 to find your data.
```
And you just have to click the url to access to the dumped file curated for insertion in SQLite.
