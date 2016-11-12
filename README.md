# logger

Simplest ever PSR-3 logger implementation.

## Usage
```
use Psr\Log\LogLevel;
use LiebigZs\Logger\SimpleLogger;
 
$log = new SimpleLogger('/path/to/log.txt', LogLevel::NOTICE);
$log->error('Failed to do something.', $context);
    
```
