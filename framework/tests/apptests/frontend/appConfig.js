/**
 * Lugar donde se definien las variables de configuracion de la aplicacion
 *
 * @version 1.00
 * @since 1.00
 * $Author: aranape $
 * $Date: 2016-01-24 17:30:48 -0500 (dom, 24 ene 2016) $
 */


/**
 * @cfg {String} glb_DataUrl
 * Define el directorio base para llamar a las operaciones de
 * datos.
 */
var glb_dataUrl = '/common/framework/tests/apptests/index.php/';

/**
 * @cfg {String} glb_photoUrl
 * Define el directorio base donde se colocan las fotos de los atletas
 */
var glb_photoUrl = '../../photos';

/**
 * @cfg {String} glb_photoMaleUrlurl
 * default para la imagen de foto default de varon
 */
var glb_photoMaleUrl = glb_photoUrl + "/user_male.png"
/**
 * @cfg {String} glb_photoFemaleUrl
 * @cfg {String} glb_photoMaleUrlurl
 * default para la imagen de foto default de varon
 */
var glb_photoFemaleUrl = glb_photoUrl + "/user_female.png"

// Para marcaidaciones

/**
 * @cfg {String} glb_RE_onlyValidText
 * Define la expresion regular para marcaidaciones de texto marcaido
 */
var glb_RE_onlyValidText = '^[A-Za-z0-9][A-Za-z0-9 ._\/-ÁÉÍÓÚáéíóuñÑ]*[A-Za-z0-9.]$';

/**
 * @cfg {String} glb_RE_onlyValidText
 * Define la expresion regular para marcaidaciones de texto marcaido
 */
var glb_RE_onlyValidTextWithComma = '^[A-Za-z0-9][A-Za-z0-9 ._\/-ÁÉÍÓÚáéíóuñÑ,]*[A-Za-z0-9.]$';

/**
 * @cfg {String} glb_MSK_phone
 * Define la mascara para los telefonos del sistema
 */
var glb_MSK_phone = '##########';

/* @cfg {String} glb_defaultDateFormat
 * Define el formato default de input date
 */
var glb_defaultInputDateFormat = 'DMY';


/* @cfg {String} glb_RE_url
 * Define la expresion regular para verificar URL
 */
var glb_RE_url = "^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|localhost|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$";

/* @cfg {String} glb_RE_email
 * Define la expresion regular para verificar email
 */
var glb_RE_email = "[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)";

/* @cfg {String} glb_RE_alpha_dash
 * Define la expresion regular para verificar alfanumerico , guion bajo y guion
 */
var glb_RE_alpha_dash = "^[a-zA-Z0-9]+[_-]*[a-zA-Z0-9]+$";

/* @cfg {String} glb_systemident
 * Define a que sistema pertenece este config
 */
var glb_systemident = 'ATLETISMO';

/* @cfg {String} glb_reportServerUrl
 * Define el url basico del  sevidor de reportes
 */
var glb_reportServerUrl = 'http://192.168.1.42:8080/jasperserver';

var glb_reportServerUser = 'atluser';
var glb_reportServerPsw = 'atluser';


Date.setShortDisplayFormat("toEuropeanShortDate");
Date.setInputFormat("DMY");

/**
 * Clase global con metodos utilitarios al sistema.
 */
isc.defineClass("AtlUtils").addClassProperties({
    /**
     * NOTA: Este metodo es estatico.
     *
     * Dada una marca (string) esta es validada de acuerdo a su clasificacion de prueba
     * De ser manual se retorna su valor ajustado.
     *
     * @param {string} marca la marca averificar en formato de string
     * por ejemplo 1:20.00 osea una hora 20 minutos 0 segundos
     * @param {string} Clasificacion de prueba osea el codigo de clasificacion si es de velocidad,
     * fondo , etc.
     * @param {boolean} ismanual si la marca es manual
     * @param {float}  adjustManualTime con el valor en decimas de segundo de ajuste entre
     * la marca electronica y la manual.
     *
     * @return un integer on la marca en milisegundos (no formales).
     */
    getMarcaNormalizada: function (marca, clasificacion_prueba, ismanual, adjustManualTime) {
        //  console.log('MN : ' + marca + ' Clasificacion ' + clasificacion_prueba);
        if (marca && marca.trim().length > 0) {
            marca = marca.replace('.', ':');
            marca = marca.split(':');

            var alen = marca.length;

            if ((clasificacion_prueba == 'SEG' && (alen != 2 && alen != 3)) ||
                    (clasificacion_prueba == 'HMS' && alen != 4) ||
                    (clasificacion_prueba == 'MS' && alen != 3) ||
                    (clasificacion_prueba == 'PUNT' && alen != 1) ||
                    (clasificacion_prueba == 'MTSCM' && alen != 2)) {
                return 0;
            }

            // Si el clasificacion_prueba es segundos pero el arreglo es de 3
            // sera tratado como Minutos/Segundos
            if (clasificacion_prueba == 'SEG' && alen == 3) {
                clasificacion_prueba = 'MS';
            }

            var factor = 1;
            if (clasificacion_prueba == 'SEG') {
                if (ismanual == true) {
                    // Usamos factor de correccion
                    if (adjustManualTime > 0.00) {
                        marca[1] = Math.round(((adjustManualTime + marca[1] / 10.00) * 100.00));
                    } else {
                        factor = 100;
                    }
                }
                return marca[0] * 1000 + marca[1] * factor;

            } else if (clasificacion_prueba == 'HMS') {
                if (ismanual == true) {
                    // Usamos factor de correccion
                    if (adjustManualTime > 0.00) {
                        marca[3] = Math.round(((adjustManualTime + marca[3] / 10.00) * 100.00));
                    } else {
                        factor = 100;
                    }
                }
                return marca[0] * 3600000 + marca[1] * 60000 + marca[2] * 1000 + marca[3] * factor;
            } else if (clasificacion_prueba == 'MS') {
                if (ismanual == true) {
                    // Usamos factor de correccion
                    if (adjustManualTime > 0.00) {
                        marca[2] = Math.round(((adjustManualTime + marca[2] / 10.00) * 100.00));
                    } else {
                        factor = 100;
                    }
                }
                return marca[0] * 60000 + marca[1] * 1000 + marca[2] * factor;
            } else if (clasificacion_prueba == 'PUNT') {
                return marca[0] * 1;
            } else if (clasificacion_prueba == 'MTSCM') {
                return marca[0] * 100 + marca[1];
            }
            return 0;
        } else {
            return 0;
        }
    }});