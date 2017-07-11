<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//require_once (APPPATH . "../framework/techsoft/fw/defs/TSLJsonAble.php");

/**
 * Esta es la clase base para definir los modelos de datos , esta clase permite
 * como cualquier objeto puro con getters y setters o como una clase normal
 * pero sin acceso directo a sus componentes, ya que siempre pasara por los
 * getter y setters gracias al uso de __get and __set magic methods,.
 *
 *
 * Por Ejemplo :
 *
 * <code>
 * class test extends TLSDataModel {
 *      protected $_var1;
 *      protected $_var2;
 *
 *      public function setVar1($var1) {
 *          $this->_var1 = $var1;
 *      }
 *
 *      public function getVar2() {
 *          return $this->_var2;
 *      }
 * }
 *
 * $clstest = new test();
 * $clstest->var1 = 'test'; // calls the method setVar1()
 * $clstest->var2 = 'test'; // do a direct assign using magic method __set
 * echo  $clstest->var1; // // returns directly the value of $_var1  using the magic methosd __get
 * echo  $clstest->var2; // // call the method getVar2()
 *
 * </code>
 *
 *  Desde luego se puede llamar a los getters y setters en forma tradicional.
 *
 * @author Carlos Arana Reategui
 * @version 1.00 , 14 JUL 2009
 * @history 27 MAY 2011 , adaptada para TECHSOFT y se le agrega la capacidad de convertir
 * a XML o JSON.
 * 18 JUL 2016 en el metodo __set ahora al armar la funcion la base incluye el underscrore
 * que es donde debe tenerse, en otras palabras se asignaran los valores si existe un metodo
 * que inicie con set_ como set_unidad_codigo, de lo contrario se asignara como atributo en forma directa.
 * 
 * 18 JUL 2016 , ahora en el metodo setOptions si no existe un metodo definido la llave no apendeara
 * el underscore.
 *
 * @since 1.00
 *
 */
class TSLDataModel implements \TSLJsonAble {

    // Todos los modelos de datos deberan tener al menos un id
    // y un numero de version, valor que e usara para ver si hay cambios
    // previo a una actualizacion.
    protected $id;
    protected $versionId;
    protected $usuario;
    protected $usuario_mod;
    protected $activo = TRUE;

    /**
     *
     * @var DateTime
     */
    protected $fecha_creacion;

    /**
     *
     * @var DateTime
     */
    protected $fecha_modificacion;

    /**
     * Constructor
     * If an array with attr names and values are sended its used
     * for initialize the class attributes.
     * Si un arreglo de nombres - valores es enviado estos son usados para
     * inicializar los atributos de la clase.
     *
     * IMPORTANTE : cada atributo de la clase debera ser declarada protected
     * en las subclases
     *
     * Por ejemplo si el arreglo es ('field1'=>'val1','field2'=>'val2') , la subclase
     * debera tener como atributos protegidos $field1 and $field2.
     *
     * @access public
     *
     * @param array $options
     */
    public function __construct(array $options = null) {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Override del metodo magico __get
     *
     * Primero trata de llamar el metodo basado en el nombre del parametro , si el metodo
     * no existe tratara de setear directamente el valor.
     *
     * IMPORTANTE : El metodo setter si es creado en la subclase necesita usar el
     * Camel case , por ejemplo $this->attr = 'test' tratara de llamar al setter
     * setAttr.
     *
     * Si el metodo no existe tratara de accesar el atributo , por eso estos deben ser protected.
     *
     * @access public
     * @param string $name con el nombre del attribute
     * @param mixed $value  , El valor a asigna al atributo
     * @return void
     *
     */
    public function __set(string $name, $value) : void {
        $method = 'set_' . ucfirst($name);
        /* log_message('info', 'The method __set called for '.$method); */
        if (!method_exists($this, $method)) {
            $attr = "$name";
            $this->$attr = $value;
        } else {
            $this->$method($value);
        }
    }

    /**
     * Primero trata de llamar el metodo basado en el nombre del parametro , si el metodo
     * no existe tratara de setear directamente el valor.
     *
     * IMPORTANTE : El metodo getter si es creado en la subclase necesita usar el
     * Camel case , por ejemplo echo $this->attr tratara de llamar al getter
     * getAttr.
     *
     * Si el metodo no existe tratara de accesar el atributo , por eso estos deben ser protected.
     *
     * @access public
     * @param string $name string con el nombre del attribute
     * @return mixed
     *
     */
    public function __get(string $name) {
        $method = 'get' . ucfirst($name);
        /* log_message('info', 'The method __get called for '.$method); */
        if (!method_exists($this, $method)) {
            $attr = "$name";
            return $this->$attr;
        }
        return $this->$method();
    }

    /**
     * Este metodo setea los atributos del modelo de datos basado en un arreglo.
     *
     * Para cada elemento se buscara primero si existe el metodo detter , de no haberlo
     * trata de hacerlo directamente.
     *
     *
     * @access public
     * @param array $options un arreglo con los elementos conteniendo los pares attr->value .
     *
     */
    public function setOptions(array $options) : void  {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            } else {
                $method = 'set_' . $key;
                if (in_array($method, $methods)) {
                    $this->$method($value);
                } else {
                    $attr = "$key";
                    $this->$attr = $value;
                }
            }
        }
       // return $this;
    }

    /**
     * Este metodo retorna todos los atributos en forma de un arreglo de
     * pares key -> value
     * Para usos especificos puede hacerse override.
     *
     * @access public
     * @return  array with the properties.
     */
    public function getAsArray() : array {
        $serial = serialize($this);
        $serial = preg_replace('/O:\d+:".+?"/', 'a', $serial);
        if (preg_match_all('/s:\d+:"\\0.+?\\0(.+?)"/', $serial, $ms, PREG_SET_ORDER)) {
            foreach ($ms as $m) {
                $serial = str_replace($m[0], 's:' . strlen($m[1]) . ':"' . $m[1] . '"', $serial);
            }
        }
        return @unserialize($serial);
    }

    /**     * **************************************
     * GETTER AND SETTERS
     * *************************************** */

    /**
     * Retorna el unique id que identifica a este data model
     * @return Mixed con el identificador
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Setea el unique id de el data model.
     *
     * @param Mixed $id con el identificador unico, no NULL!!
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Hay casos en que el codigo unico no es el id y el id se usa
     * por eficiencia en los joins o en otras cosas el id no se usa
     * ya que se usa data migrada y se requeire que el codigo sea unico.
     *
     * En estos casos el codigo unico difirere del id , debera sobreescribirse
     * este metodo en esos casos.
     *
     * @return mixed con el codigo unico que identifica al registro.
     */
    public function getUniqueCode() {
        return $this->id;
    }

    /**
     * Retorna el numero de version del record que representa este Data Model.
     *
     * @return int con la version correspodiente a la version del datamodel
     * en la persistencia.
     */
    public function getVersionId() :  int {
        return $this->versionId;
    }

    /**
     * Setea la version del record que represnta el data model, este solo debe
     * ser seteado desde la persistencia nunca desde el cliente.
     *
     * @param int $versionId con el valor de la version del registor
     */
    public function setVersionId(int $versionId) : void {
        $this->versionId = $versionId;
    }

    /**
     *
     * @return string con el identificador del usuario
     */
    public function getUsuario() : string {
        return $this->usuario;
    }

    /**
     * Setea el identificador de usuario.
     *
     * @param string $usuario
     */
    public function setUsuario(string $usuario) : void {
        $this->usuario = $usuario;
    }

    /**
     *
     * @return DateTime con la fecha de creacion del registor
     */
    public function get_Fecha_creacion() : DateTime {
        return $this->fecha_creacion;
    }

    /**
     * Setea la fecha de creacion del registro.
     *
     * @param DateTime $fecha_creacion
     */
    public function set_Fecha_creacion(DateTime $fecha_creacion) : void {
        $this->fecha_creacion = $fecha_creacion;
    }

    /**
     *
     * @return string con el identificador de usuario que modifica
     */
    public function get_Usuario_mod() : string  {
        return $this->usuario_mod;
    }

    /**
     * Setea el usuario que modifica el registro.
     *
     * @param string $usuario_mod
     */
    public function set_Usuario_mod(string $usuario_mod) : void {
        $this->usuario_mod = $usuario_mod;
    }

    /**
     *
     * @return DateTime con la fecha de modificacion del registro.
     */
    public function get_Fecha_modificacion() : DateTime {
        return $this->fecha_modificacion;
    }

    /**
     * Setea la fecha de modificacion del registro.
     *
     * @param DateTime $fecha_modificacion
     */
    public function set_Fecha_modificacion(DateTime $fecha_modificacion) : void {
        $this->fecha_modificacion = $fecha_modificacion;
    }

    /**
     * Si se desea que un modelo este inactivo pero no eliminado
     * este campos era util para eso.
     *
     * Importante no se usa el hint pro problemas en la interpretacion del bbol
     * sobre todo cuando lo recuperamos de otro objeto y este es por un ejemplo
     * un string con 'false' seria tomado como true
     *
     * @param bool $activo , 1 o true , 0 o false
     */
    public function setActivo( $activo) : void {
        if ($activo !== 1 && $activo !== TRUE && strtoupper($activo) !== 'TRUE' && strtoupper($activo) != 'T') {
            $this->activo = FALSE;
        } else {
            $this->activo = TRUE;
        }
    }

    /**
     * Retorna TRUE si esta activo.
     *
     * @return bool TRUE verdadero FALSE falso
     */
    public function getActivo() : bool {
        return $this->activo;
    }
    /**
     * Retorna un arreglo con las llaves primarias del modelo.
     * Implementacion default.
     * @return array con las llaves primarias del modelo
     */
    public function &getPKAsArray() : array {
        $pk['id'] = $this->getId();
        return $pk;
    }

    /**
     * Muchas veces el id es una secuencia o identity , si ese fuera
     * el caso el tratamiento durante digamos un insert es diferente , ya que
     * luego del insert hay que determinar este valor , en cambio si el id
     * es una llave primaria ingresada previo al update (osea no es identity o sequence)
     * el valor de la llave unica es conocida previamente.
     *
     * Es recomendable que si luego de hacer un insert se requiere leer
     * el ultimo valor insertado para colocarlo en el modelo se llame a este metodo
     * para verificar si es una llave secuenciada o identity ya que de ser asi
     * getId() o getUniqueCode() tendran como valor null.
     *
     * LAs previsiones de como resolver este problema se deja para las clases que efectuan
     * las tareas sobre el modelo.
     *
     * Por default retornara null suponiendo que el id es un campo digamos digitado
     * y no automatico, el modelo final debera hacer un override a este metodo de no ser asi.
     *
     * @return bool true si el id es secuencia o identity o false si o lo es
     */
    public function isPKSequenceOrIdentity() : bool {
        return false;
    }

    /**
     * Convierte el objeto a JSON
     *
     * @return string conteniendo la represemtacion de data model en
     * JSON
     */
    public function toJSON() : string {
        return json_encode($this->getAsArray());
    }

    /**
     * Evalua el parametro y devuelve un valor booleano luego
     * del analisis del mismo , esto es ya que muchas bases de datos
     * devuelven diferentes valores como f , F,T,Y etc para representar
     * un booleano en la base de datos.
     * s
     * @param mixed $value
     * @return bool
     */
    protected static function getAsBool($value) : bool {
        if (is_bool($value)) {
            return $value;
        } else {
            if ($value === 'Y' or $value === 'y' or $value === 't' or $value === 'T' or $value === 'true' or $value === 'TRUE') {
                return true;
            } else {
                return false;
            }
        }
    }

}