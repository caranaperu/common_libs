<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controlador especifico para el backend KoolReport , basicamente prepara los parametros
 * e invoca el reporte.
 *
 * @author  $Author: aranape $
 * @since   21-OCT-2018
 * @version 1.0
 * @history ''
 *
 * $Date: 2018-10-21 22:31:42 -0500 $
 */
abstract class TSLReportKoolReportController extends TSLReportController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * No se usa en KoolReport.
     */
    protected function getReportURI() : string {
        return '';
    }

    /**
     * No se usa en KoolReport.
     */
    protected function getTmpOutputDirectory() : string {
        return '';
    }


    /**
     * No se usa en KoolReport.
     *
     * @param mixed $contents binario,texto, etc con la salida final del reporte
     * @return mixed string con el filename del reporte o FALSE si hubo un error
     */
    protected function createReportOutputFile(&$contents) {
        return FALSE;
    }

    /**
     * Metodo de ayuda para emitir los errores al lado cliente , para el caso
     * del jasper simplemente se indicara un error 500 y un mensaje de error
     * en texto , el cual podra ser usado para visualizarlo en el browser o
     * lado cliente.
     *
     * @param string $error el error a pintar.
     */
    protected function outputError(string $error) {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' 500 Internal Server Error');
        echo (strlen($error) > 0 ? $error : 'Error no conocido...');
    }

    /**
     * Rutina principal de entrada .
     *
     * @param string $reportServerUser
     * @param string $reportServerPassword
     * @param string $formatOutput
     */
    protected function executeReport(string $reportServerUser, string $reportServerPassword, string $formatOutput) : void {
        try {

            // recogemos los que seran parametros del reporte y los parseamos
            // para generar el formato requerido.
            $input_params = &$this->getInputReportParamsList();
            $report_params = &$this->getReportParams(array_keys($input_params));

            // ejecutamos el reporte .
            $result = $this->doReport($input_params, $report_params);

            // verficamos si el reporte pudo ser ejecutado.
            if (!$result) {
                $msg = 'Error executing the report..<br><font color="red"></font>';
                $this->outputError($msg);
                die();
            }

        } catch (\Throwable $ex) {
            $this->outputError($ex->getMessage());
        }
    }

    /**
     * Metodo a implementar que es el punto donde debera ejecutarse el reporte
     * de Koolreport.
     *
     * @param array $input_params  parametros que seran pasados al reporte que
     * influencian a los resultados del reporte.
     *
     * @param array $report_params parametros que seran pasados al reporte para la decision
     * de si la salida es PDF,EXCEL o formato de paginas , etc.
     *
     * @return bool retorna si existio un error
     */
    abstract protected function doReport(array $input_params,array $report_params) : bool ;
}
