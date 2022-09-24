<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * @filesource
 */

/**
 * Controlador basico especificamente disenado para el caso WEB , basicamente efectuara
 * la validacion de los parametros , control de errores a nivel de problemas de sistema y
 * define metodos a ser requeridos para todos los controladores que implementen basados en este,
 * como determinar la vista de salida y el response procesor que se requerira
 * para transformar los datos para la vista.
 *
 * @author carana
 * @version 1.00
 *
 */
abstract class TSLBaseController extends CI_Controller {

    /**
     * El Data transfer Object
     * @var TSLIDataTransferObj
     */
    protected $DTO;

    /**
     * Instancia del encoder que procesara los datos resultados obtenidos en el DTO
     * y los transformara al formato requerido por la vista de salida, por ejemplo
     * JSON, XML.
     *
     * @var TSLIResponseProcessor un encoder de salida
     */
    protected $responseProcessor;

    /**
     * Si envia mensajes de debug o no.
     * @var boolean
     */
    private $DEBUG = false;

    /**
     * TSLBaseController constructor.
     *
     * Crea las estructuras requeridas por el DTO  carga el response processor,
     * asi mismo setea el sistema de errores del PHP tales como el metodo de
     * shutdown y el exception handler.
     *
     */
    public function __construct() {
        parent::__construct();
        // Inicializa el Data Transfer Object
        $this->DTO = new TSLDataTransferObj();
        // Dado el contexto de las funciones de error las clases deben estar cargadas.
        $this->DTO->getOutMessage();
        $this->responseProcessor = $this->getResponseProcessor();

        // Se modifica los defaults de manejo de errores
        // para poder manejar mejor la salida de los mismo.
        //error_reporting(E_ALL ^ E_WARNING);
        // Esto se setea el¡n el main de la aplicacion (ejemplo muniren_admin.php)
        //       error_reporting(E_ALL & ~E_DEPRECATED);

        ini_set('display_errors', 0);

        // Se apuntan los manejadores de errores y excepciones a los
        // contenidos en esta clase.
        @register_shutdown_function([
            $this,
            'MyShutdown'
        ]);
        //       @set_error_handler(array($this, 'myErrorHandler'));
        @set_exception_handler([
            $this,
            'MyExceptionHandler',
        ]);
    }

    /**
     * Funcion callback al terminar el ciclo del controller en el cual se detectaran
     * si hubo errores inesperados para ser colocados como parte de la salida.
     */
    public function MyShutdown() {
        $isError = false;
        if ($error = error_get_last()) {
            switch ($error['type']) {
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                case E_RECOVERABLE_ERROR:
                case E_NOTICE:
                case E_DEPRECATED:
                case E_PARSE:
                    $isError = true;
            }
        }

        if ($isError) {
            if ($this->DEBUG) {
                echo "Script fue detenido ({$error['message']})";
            }

            $outMessage = &$this->DTO->getOutMessage();
            $outMessage->setSuccess(false);
            $outMessage->setAnswerMessage('Error : '.$error['message'], -10000);

            //   $data['data'] = $this->DTO;
            //  echo $this->responseProcessor->encode($this->DTO);

            // Parche para cerrar lo pendiente de codeigniter , por este camino se finaliza
            // abruptamente. NO EJECUTA HOOKS
            // TODO : Manejar los hooks.
            $CI = &get_instance();
            $OUT = &load_class('Output', 'core');

            $OUT->_display($this->responseProcessor->process($this->DTO));
            if (class_exists('CI_DB') and isset($CI->db)) {
                $CI->db->close();
            }
        } else {
            if ($this->DEBUG) {
                echo "Script completado correctamente";
            }
        }
    }

    /**
     * Funcion callback del sistema para en caso sucedan errores no controlados, colocara
     * el mensaje de error como parte de la salida.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     */
    public function myErrorHandler(int $errno, string $errstr, string $errfile, int $errline) {
        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $errors = "Notice";
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $errors = "Warning";
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $errors = "Fatal Error";
                break;
            default:
                $errors = "Unknown";
                break;
        }

        if (ini_get("display_errors")) {
            printf("<br />\n<b>%s</b>: %s in <b>%s</b> on line <b>%d</b><br /><br />\n", $errors, $errstr, $errfile, $errline);
        }

        if (ini_get('log_errors')) {
            error_log(sprintf("PHP %s:  %s in %s on line %d", $errors, $errstr, $errfile, $errline));
        }
        //printf("<br />\n<b>%s</b>: %s in <b>%s</b> on line <b>%d</b><br /><br />\n", $errors, $errstr, $errfile, $errline);
        //   exit();

        $outMessage = &$this->DTO->getOutMessage();
        // Solo se apendea error si no hay mensaje previo
        $outMessage->setSuccess(false);
        $outMessage->setAnswerMessage($errors.' : '.$errstr.' Archivo : '.$errfile.' Linea :'.$errline, -10000);

        // $data['data'] = $this->DTO;
        echo $this->responseProcessor->process($this->DTO);

        // Parche para cerrar lo pendiente de codeigniter , por este camino se finaliza
        // abruptamente. NO EJECUTA HOOKS
        //
        // TODO : Manejar los hooks.
        $CI = &get_instance();
        $OUT = &load_class('Output', 'core');
        $OUT->_display();
        if (class_exists('CI_DB') and isset($CI->db)) {
            $CI->db->close();
        }

        // Adios , nada mas que hacer.
        exit();

        //$this->load->views($this->getView(), $data);
    }

    /**
     * Funcion callback en el caso de excepciones no controladas.
     *
     * @param Throwable $exception
     */
    public function MyExceptionHandler(Throwable $exception) {
        echo "Uncaught exception: ", $exception->getMessage(), "\n";
    }

    /**
     * Reemplaza el valor de un parametro por el enviado en
     * el caso se cumpla la condicion de igualdad con el valueToSearch.
     * Util por ejemplo si el lado cliente envia el string 'null' para indicar
     * campo vacio  y deseamos reemplazarlo por ejemplo con un NULL
     * real.
     *
     * @param string $parameterName Nombre del parametro de entrada a trabajar
     * @param string $valueToSearch valor a buscar
     * @param mixed  $valueToReplace valos por el cual reemplazar
     *
     * @return mixed con el valor del parametro modificado o NULL si el parametro no existe.
     */
    protected function fixParameter(string $parameterName, string $valueToSearch, $valueToReplace) {
        if (array_key_exists($parameterName, $_POST)) {
            if ($_POST[$parameterName] === $valueToSearch) {
                $_POST[$parameterName] = $valueToReplace;
            }

            if ($_REQUEST[$parameterName] === $valueToSearch) {
                $_REQUEST[$parameterName] = $valueToReplace;
            }

            return $_POST[$parameterName];
        }

        return NULL;
    }

    /**
     * Validara los parametros que vienen enviados desde el cliente (browser) y el resultado de
     * dicha validacion sera colocado en el out message que es parte del DTO.
     *
     * Como ejemplo de un archivo de validacion :
     * <pre>
     * $config['v_colorclass'] = array(
     * 'getColorClass' => array(
     * array(
     * 'field' => 'color_class_codigo',
     * 'label' => 'lang:color_class_codigo',
     * 'rules' => 'required|alpha_dash|max_length[20]'
     * )
     * ),
     * 'updColorClass' => array(
     * array(
     * 'field' => 'color_class_codigo',
     * 'label' => 'lang:color_class_codigo',
     * 'rules' => 'required|alpha_dash|max_length[20]'
     * ),
     * array(
     * 'field' => 'color_class_value',
     * 'label' => 'lang:color_class_value',
     * 'rules' => 'required|max_length[7]'
     * ),
     * array(
     * 'field' => 'color_class_protected',
     * 'label' => 'lang:color_class_protected',
     * 'rules' => 'required|is_boolean'
     * ),
     * array(
     * 'field' => 'versionId',
     * 'label' => 'lang:versionId',
     * 'rules' => 'required|integer'
     * )
     * ),
     * 'delColorClass' => array(
     * array(
     * 'field' => 'color_class_codigo',
     * 'label' => 'lang:color_class_codigo',
     * 'rules' => 'required|alpha_dash|max_length[20]'
     * ),
     * array(
     * 'field' => 'versionId',
     * 'label' => 'lang:versionId',
     * 'rules' => 'required|integer'
     * )
     * ),
     * 'addColorClass' => array(
     * array(
     * 'field' => 'color_class_codigo',
     * 'label' => 'lang:color_class_codigo',
     * 'rules' => 'required|alpha_dash|max_length[20]'
     * ),
     * array(
     * 'field' => 'color_class_value',
     * 'label' => 'lang:color_class_value',
     * 'rules' => 'required|max_length[7]'
     * ),
     * array(
     * 'field' => 'color_class_protected',
     * 'label' => 'lang:color_class_protected',
     * 'rules' => 'required|is_boolean'
     * )
     * )
     * );
     * </pre>
     *
     * y su respectivo archivo de lenguaje, donde estaran los nombres de los campos en el idioma
     * que se este usando, como minimo seria asi en este ejemplo:
     * <pre>
     * $lang['color_class_codigo'] = 'Codigo';
     * $lang['color_class_value'] = 'Valor';
     * $lang['color_class_protected'] = 'Protegido';
     * $lang['versionId'] = 'Version';
     * </pre>
     *
     * @param TSLIDataTransferObj $DTO DTO donde residiran las respuestas a la validacion en caso
     * exista errores y especificamente iran en el out message del mismo.
     * @param string              $langId Nombre base del archivo que contendra las traducciones para
     * los mensajes de error de validaciones de campos , pej. factura , hara que el framework busque
     * el archivo factura_lang.
     * Este archivo debe ir en el directorio application/language/[idioma]  de la aplicacion.
     * @param string              $validationId Nombre base del archivo que contendra las validaciones, pej.
     * factura , hara que el framework busque el archivo factura_validation_ Este archivo debe ir
     * en el directorio application/config de la aplicacion. Para ver como se configura
     * {@see https://codeigniter.com/userguide3/libraries/form_validation.html#overview}
     * @param string              $validationGroupId Un archivo de validacion puede contener varios grupos,
     * este parametro indicara el grupo.
     * @param string              $validationRulesId Dentro de cada grupo habra u set de reglas para distintos
     * casos como pueden ser una para agregar , eliminar, etc.
     *
     * @return bool TRUE si las validaciones fueron correctas y FALSE si no lo fueron, en este caso en el out
     * message del DTO iran los mensajes de error de las validaciones para cada campo validado con error.
     */
    protected final function validateInputData(TSLIDataTransferObj $DTO, string $langId, string $validationId, string $validationGroupId, string $validationRulesId): bool {

        // IMPORTANTE , solo funciona si hay POST data!!!!

        $this->lang->load($langId);

        // Load validations rules from an own validation file
        $this->load->config('validation/'.$validationId);
        $rules = $this->config->item($validationGroupId);

        // cargamos la libreria de validacion
        $this->load->library('form_validation');

        // Ejecutar validacion.
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules($rules[$validationRulesId]);

        // Si hay errores se agregan al DTO
        if ($this->form_validation->run() === FALSE) {
            $outMsg = $DTO->getOutMessage();

            $flderrs = $rules[$validationRulesId];
            $count = count($flderrs);

            for ($i = 0; $i < $count; $i++) {
                $currentErrorMsg = $this->form_validation->error($flderrs[$i]['field']);

                // Solo agregamos el error si existe un mensaje de error
                if (isset($currentErrorMsg) && strlen($currentErrorMsg) > 0) {
                    // Crea el error de campo y lo agrega al mensaje de salida.
                    $fldError = new TSLFieldErrorMessage($flderrs[$i]['field'], $this->form_validation->error($flderrs[$i]['field']));
                    $outMsg->addFieldError($fldError);
                    unset($fldError);
                }
                unset($currentErrorMsg);
            }
            unset($flderrs);

            return FALSE;
        }

        return TRUE;
    }

    /**
     * Retornara el nombre de la vista , realmente debera corresponder
     * a la clase que implementa la vista de salida, estas vistas seran
     * buscadas en el directorio views de la aplicacion.
     *
     * Este subdirectorio debe estar bajo el $application_folder definido
     * para la aplicacion.
     *
     * @return string con el nombre de la clase de la vista
     */
    abstract protected function getView(): string;

    /**
     * Retornara la instancia del response processor para crear la salida
     * adecuada a la vista , por ejemplo podria ser para JSON,XML u otro cualquiera.
     *
     * @throws TSLProgrammingException en caso no pueda cargarse el response processor.
     *
     * @return TSLIResponseProcessor la instancia del response processor
     */
    abstract protected function getResponseProcessor(): TSLIResponseProcessor;

    /**
     * Override si se usa un response especifico en algun caso y que no se cargue
     * el default.
     * Ver {@see TSLResponseProcessorLoaderHelper}
     *
     * @return string con el nombre del procesador de salida definida
     * por el usuario , de ser null se aplicara los metodos default de carga
     * de dichas clases.
     */
    protected function getUserResponseProcessor(): ?string {
        return NULL;
    }
}