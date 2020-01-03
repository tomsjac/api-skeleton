<?php
namespace App\services\dependencies;

/**
 * Logger With Monolog
 * @author thomas
 */

use Exception;
use Interop\Container\Exception\ContainerException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Log\LoggerInterface;
use Slim\Container;

class Log extends Logger
{
    /**
     * Construct
     * @param Container $container
     * @throws ContainerException
     * @throws Exception
     */
    public function __construct(Container $container)
    {
        $settings  = $container->get('settings');
        $optionsLogger = $settings->get('logger');

        parent::__construct($optionsLogger['name']);

        $this->pushProcessor(new UidProcessor());
        $this->pushProcessor(new IntrospectionProcessor());
        $this->pushProcessor(new WebProcessor());

        //File Save
        $stream = new StreamHandler($optionsLogger['path'], $optionsLogger['level']);
        $stream->setFormatter($this->getFormatter());
        $this->pushHandler($stream);

        //Handler to send email on critical (or above) errors
        if (isset($optionsLogger['mailLog']) == true && empty($optionsLogger['mailLog']) == false) {
            $mailHandler = new NativeMailerHandler(
                $optionsLogger['mailLog'],
                '[Bug] ('.$container['app-name'].') : Unexpected error happened that requires immediate attention ' . date('Y-m-d'),
                $optionsLogger['mailLog'],
                $optionsLogger['levelMail'],
                true,
                2000
            );
            $this->pushHandler(new FingersCrossedHandler($mailHandler, $optionsLogger['levelMail']));
        }
    }

    /**
     * Return Object Log
     * @return Logger|LoggerInterface
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * Format content Log
     * @return LineFormatter
     */
    private function getFormatter()
    {
        $dateFormat = "Y-m-d H:i:s";
        // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
        $output     = "[%datetime%] %channel%.%level_name% > %message% %context% %extra% \n";
        // finally, create a formatter
        return new LineFormatter($output, $dateFormat);
    }
}
