# postgres2sqlite
A PHP Class to export data from PostgreSQL environment and prepare a INSERT file to SQLite database.

#Usage
import 

$host = '128.1.10.6';
$port = '5432';
$database = 'nasplazasdominicanas';
$user = 'nasplazasdominicanas';
$password = 'nasplazasdominicanas.2017##';

$dump = new ConvertToSQLite($host, $port, $database, $user, $password);
$dump->SetAppLocation('/Applications/Postgres.app/Contents/Versions/9.6/bin/pg_dump');
$dump->SetDumpLocation('php_dump.sql');
$dump->ProcessConversion();
