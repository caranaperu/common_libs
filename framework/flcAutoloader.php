<?php

/**
 * Autoloader specific for the library files and classes
 */
spl_autoload_register(function (string $class) {
    static $framework_classes = [
        // core
        'FLC' => BASEPATH.'/core/',
        'flcConfig' => BASEPATH.'/core/',
        'flcController' => BASEPATH.'/core/',
        'flcIController' => BASEPATH.'/core/',
        'flcLanguage' => BASEPATH.'/core/',
        'flcLog' => BASEPATH.'/core/',
        'flcRequest' => BASEPATH.'/core/',
        'flcResponse' => BASEPATH.'/core/',
        'flcServiceLocator' => BASEPATH.'/core/',
        'flcUtf8' => BASEPATH.'/core/',
        'flcValidation' => BASEPATH.'/core/',
        'flcMessageTrait' => BASEPATH.'/core/',
        'flcErrorHandler' => BASEPATH.'/core/',

        // session stuff
        'flcSession' => BASEPATH.'/core/session/',
        'flcSessionInterface' => BASEPATH.'/core/session/',
        'flcBaseHandler' => BASEPATH.'/core/session/handler/',
        'flcFileHandler' => BASEPATH.'/core/session/handler/',

        // Database stufs
        'flcDbResult' => BASEPATH.'/database/',
        'flcDbResultOutParams' => BASEPATH.'/database/',
        'flcDbResults' => BASEPATH.'/database/',
        // Database drivers
        'flcDriver' => BASEPATH.'/database/driver/',
        'flcMssqlDriver' => BASEPATH.'/database/driver/mssql/',
        'flcMssqlResult' => BASEPATH.'/database/driver/mssql/',
        'flcMysqlDriver' => BASEPATH.'/database/driver/mysql/',
        'flcMysqlResult' => BASEPATH.'/database/driver/mssql/',
        'flcPostgresDriver' => BASEPATH.'/database/driver/postgres/',
        'flcPostgresResult' => BASEPATH.'/database/driver/postgres/',
        // accessor
        'flcPersistenceAccessor' => BASEPATH.'/core/accessor/',
        'flcDbAccessor' => BASEPATH.'/core/accessor/',
        'flcConstraints'=> BASEPATH.'/core/accessor/constraints/',
        'flcJoinEntry'=> BASEPATH.'/core/accessor/constraints/',
        'flcJoins'=> BASEPATH.'/core/accessor/constraints/',
        // model
        'flcBaseEntity' => BASEPATH.'/core/entity/',

        // dto
        'flcInputData' => BASEPATH.'/core/dto/',

        // others
        'flcCommon' => BASEPATH.'/',
        'flcStrUtils' => BASEPATH.'/utils/',
        'flcErrorHandlers' => BASEPATH.'/utils/',

    ];
    // remove namespace if exist.
    $class_non_ns = substr($class, (strrpos($class, '\\') ?: -1) + 1);
    //echo 'loading class '.$class_non_ns.PHP_EOL;

    if (array_key_exists($class_non_ns, $framework_classes)) {
        include_once $framework_classes[$class_non_ns].$class_non_ns.'.php';

    }

});