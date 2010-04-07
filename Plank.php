<?PHP
/* This is Piracy Inc, and I wrote it.

I am Nicholas Avenell, it is 25th January (I'll be 28 tomorrow) and this is
the third restart of the game. It started as a formula one racing game, became 
a pirate racing game, and eventually just a pirate game. One day I hope to write
the racing game, I suppose. Anyway, I originally wrote the first bits of racr in
 PHP, shifted to Django for the Pirate version, and now I'm back onto PHP 
because I want this to be managable, and because I'm still learning Django I 
find myself having to go back and rewrite things as I understand them better.
 
PHP I'm better at.

I'm also not using a framework (look, ma, no wires) because I haven't found one that doesn't fill up the world with crap I don't need or - and this is why I just deleted CodeIgniter - doesn't work the way I want web applications to.

See how long this lasts. (25th Jan 2009, 11:30)

*********************
A note on classnames:
*********************

Plank is the name of the "framework" or system code. 
PInc is the name of the game and application code.

Rule of thumb for what belongs where is that Plank_ stuff I expect to reuse.

Yes, I know I lasted less than five hours before realising I was writing my own
framework. In structure it's like the way we were using Zend Framework at trutap
with a bit of the bits of Django I like enough to include (One of the reasons
for the Zend similarity is that it means I can pull in bits of Zend for bits 
I don't want to write.)

*/
header('Content-type: text/html; charset=UTF-8') ;
ob_start();
define('SHOWDEBUG', true);

define('T', microtime(true));

define('CODE_PATH', '../application/');
define('PLANK_PATH', '../lib/');

define('L_TRACE', 32);
define('L_DEBUG', 16);
define('L_INFO', 8);
define('L_WARN', 4);
define('L_ERROR', 2);
define('L_FATAL', 1);

include(PLANK_PATH.'/Plank/Autoload.php');

function __exception_handler($exception){
	include(PLANK_PATH."/Plank/templates/Errors/Exception.template.php");
}
set_exception_handler("__exception_handler");

function __autoload($class_name){
	Plank_Autoload::loadClass($class_name);
}

$request  = new Plank_HTTP_Request();
$response = new Plank_HTTP_Response();


function handle_exceptions($e){
	global $response;
	Plank_Error::Error503($e, $response);
}

set_exception_handler("handle_exceptions");


try {	
	
	Plank_Logger::log('Init', 'Hello World', L_INFO);
	Plank_Logger::logStat('Init', 'Hello World');
	Plank_Config::getInstance();

	
	define('INIT', time());

Plank_Logger::logStat('Init', 'Finished init');
	
	new Plank_Site($request, &$response);
	
Plank_Logger::logStat('Init', 'Finished MVC');
	
} catch ( Plank_Exception_NotFound $e ){
	Plank_Error::Error404($e->getMessage(), $response);	
#} catch ( Exception $e ){
#	Plank_Error::Error503($e, $response);	
}

$response->respond();


Plank_Logger::logStat('Init', 'Goodbye, Cruel World');

Plank_Logger::log('Init', 'Memory Use: '.number_format(memory_get_usage()), L_INFO);

echo Plank_Logger_Display::display();

define('DESTRUCT', true);
