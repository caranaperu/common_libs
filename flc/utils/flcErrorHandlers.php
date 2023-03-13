<?php

namespace flc\utils;

use ErrorException;
use flc\core\flcRequest;
use flc\core\flcResponse;
use flc\core\flcServiceLocator;
use flc\flcCommon;
use Throwable;


class flcErrorHandlers {

    /**
     * Nesting level of the output buffering mechanism
     *
     * @var    int
     */
    public int $ob_level;


    public function initialize() {
        set_exception_handler([$this, '_exception_handler']);
        set_error_handler([$this, '_error_handler']);
        register_shutdown_function([$this, '_shutdown_handler']);
    }

    /**
     * Catches any uncaught errors and exceptions, including most Fatal errors
     * (Yay PHP7!). Will log the error, display it if display_errors is on,
     * and fire an event that allows custom actions to be taken at this point.
     *
     * @codeCoverageIgnore
     *
     * @param Throwable $exception
     *
     * @throws Throwable
     */
    public function _exception_handler(Throwable $exception) {
        $this->ob_level = ob_get_level();

        [$statusCode, $exitCode] = $this->get_codes($exception);

        // Log the message
        $log_msg = "\n{$exception->getMessage()}\nin {$exception->getFile()} on line {$exception->getLine()}.\n";
        foreach ($exception->getTrace() as $trace) {
            if (isset($trace['file'])) {
                $log_msg .= "File: {$trace['file']} \nLine: {$trace['line']} \nLine: {$trace['function']} \n\n";
            }
        }
        $log_msg .= '**********************************************************';
        flcCommon::log_message('error', $log_msg);

        $output_vars = '';
        $response = flcResponse::get_instance();

        if (!flcCommon::is_cli()) {
            $response->set_status_code($statusCode);
            if (!headers_sent()) {
                header(sprintf('HTTP/%s %s %s', $response->get_protocol_version(), $response->get_status_code(), $response->get_reason_phrase()), true, $statusCode);
            }


            if (strpos(flcRequest::get_instance()->get_header_line('accept'), 'text/html') === false) {
                if (ENVIRONMENT === 'development') {
                    $output_vars = [
                        'title' => get_class($exception),
                        'type' => get_class($exception),
                        'code' => $statusCode,
                        'message' => $exception->getMessage() ?? '(null)',
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'trace' => $exception->getTrace(),
                    ];

                }

                $response->set_status_code(empty($statusCode) ? 200 : $statusCode);
                $response->set_status_header($response->get_status_code());

            }
        }

        //print_r($exception);
        $this->_show_exception($exception, $response,$output_vars);
        //$this->render($exception, $statusCode);

        exit($exitCode);
    }

    /**
     * Even in PHP7, some errors make it through to the errorHandler, so
     * convert these to Exceptions and let the exception handler log it and
     * display it.
     *
     * This seems to be primarily when a user triggers it with trigger_error().
     *
     * @throws ErrorException
     *
     */
    public function _error_handler(int $severity, string $message, ?string $file = null, ?int $line = null) {
       /* if (!(error_reporting() & $severity)) {
            return;
        }*/

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * Checks to see if any errors have happened during shutdown that
     * need to be caught and handle them.
     *
     * @codeCoverageIgnore
     */
    public function _shutdown_handler() {
        $error = error_get_last();

        if ($error === null) {
            return;
        }

        ['type' => $type, 'message' => $message, 'file' => $file, 'line' => $line] = $error;

        if (in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
            $this->_exception_handler(new ErrorException($message, $type, 0, $file, $line));
        }
    }

    /**
     * @param Throwable $exception
     * @param           $response
     * @param           $data
     *
     * @return void
     * @throws Throwable
     */
    private function _show_exception(Throwable $exception, $response,$data) {
        $templates_path = flcCommon::get_config()->item('error_views_path');
        if (empty($templates_path)) {
            $templates_path = VIEWPATH.'errors'.DIRECTORY_SEPARATOR;
        }

        $message = $exception->getMessage();
        if (empty($message)) {
            $message = '(null)';
        }

        if (flcCommon::is_cli()) {
            $templates_path .= 'cli'.DIRECTORY_SEPARATOR;
            if (ob_get_level() > $this->ob_level + 1) {
                ob_end_flush();
            }

            ob_start();
            include($templates_path.'error_exception.php');
            $buffer = ob_get_contents();
            ob_end_clean();
            echo $buffer;
        } else {
            flcServiceLocator::get_instance()->service('views','errors/html/error_exception',$data);
            $to_display = $response->get_final_output();
            echo $to_display;

        }


    }


    /**
     * Determines the HTTP status code and the exit status code for this request.
     */
    protected function get_codes(Throwable $exception): array {
        $statusCode = abs($exception->getCode());

        if ($statusCode < 100 || $statusCode > 599) {
            $exitStatus = $statusCode + EXIT__AUTO_MIN;

            if ($exitStatus > EXIT__AUTO_MAX) {
                $exitStatus = EXIT_ERROR;
            }

            $statusCode = 500;
        } else {
            $exitStatus = EXIT_ERROR;
        }

        return [$statusCode, $exitStatus];
    }

}