/**
 * Objeto javascript standard no es un componente del tipo Isomorphic .
 * Basicamente pone la imagen sobre el canvas y permite su interaccion para marcar un hover.
 *
 * Podra ser usado sobre cualquier container un tag canvas cuyo canvas id esta definido por el
 * parametro canvasId.
 *
 * @param canvasId texto conteniendo el id del canvas contenido en el tag <canvas>
 * @param image Objeto tipo Image ya debidamente cargado.
 * @param top coordenada superior derecha del hover de existir
 * @param left coordenada superior izquierda del hover de existir
 * @param width ancho del hover.
 * @param height altura del hover.
 * @param canCreateHovers booleano , true si se podra crear hovers sobre la imagen , false si no es posible.
 * @param isImgMirror booleano , true si la imagen esta compuesta por un original y una copia debajo para representar
 *  la posisicion final del hover.
 */
var canvasImageMgr = function(canvasId,image,top,left,width,height,canCreateHovers,isImgMirror) {
    var canvas = document.getElementById(canvasId);
    var ctx = canvas.getContext("2d");
    var tool = null;

    canvas.height = image.height;
    canvas.width = image.width;

    // Metodo para determinar si un valor es un numero.
    this._isNumber = function(value) {
        return (!isNaN(value) && isFinite(value));
    };

    /**
     * Metodo que retorna el rectangulo final del area de hover, debe validarse estos valores
     * ya que podria cancelarse la operacion y no haberse definido el area.
     *
     * @returns {{left: *, top: *, width: *, height: *}}
     */
    this.getRectangle = function() {
        return {
            left: left,
            top: top,
            width : width,
            height: height
        };

    };

    /**
     * Metodo que retorna las dimensiones totales de la imagen , debe validarse estos valores
     * ya que podria cancelarse la operacion y no haberse definido las dimensiones.
     *
     * @returns {{width: *, height: *}}
     */
    this.getImageSize = function() {
        return {
            width : canvas.width,
            height: canvas.height
        };

    };

    /**
     * Metodo que remueve los event listeners de ser necesario , util solo para browsers
     * antiguos ya que los modernos garantizan remover los listeners.
     */
    this.cleanUp =  function() {
        // Eventos solo se registraran cuando canCreateHovers == true

        if (canCreateHovers == true) {
            if (canvas.removeEventListener) {                   // For all major browsers, except IE 8 and earlier
                canvas.removeEventListener("mousedown", canvas.ev_canvas);
                canvas.removeEventListener("mousemove", canvas.ev_canvas);
                canvas.removeEventListener("mouseup", canvas.ev_canvas);
            } else if (canvas.detachEvent) {                    // For IE 8 and earlier versions
                canvas.detachEvent("mousedown", canvas.ev_canvas);
                canvas.detachEvent("mousemove", canvas.ev_canvas);
                canvas.detachEvent("mouseup", canvas.ev_canvas);
            }
        }
    };

    // Pintamos la imagen sobre el canvas.
    ctx.drawImage(image, 0, 0, image.width, image.height);

    // Solo se dibuja el hover si se ha requerido.
    if (canCreateHovers == true) {
        ctx.font = "bold 12px";
        ctx.strokeStyle = "red";
        ctx.fillStyle = "white";


        // Si las coordenadas son validas se dibuja los rectangulos
        if (this._isNumber(left) && this._isNumber(top) &&
            this._isNumber(width) && this._isNumber(height)) {
            if (width > 0 && height > 0) {
                ctx.strokeRect(left, top, width, height);

                var txt = " " + left + "," + top + "," + width + "," + height + " ";
                ctx.fillRect(left, top + height, ctx.measureText(txt).width, 15);
                ctx.strokeText(txt, left, top + height + 10);

                if (isImgMirror == true) {
                    ctx.strokeRect(left, top + canvas.height / 2, width, height);
                }
            }
        }
    }

    // Listener de los eventos de mouse soportados sobre el canvas,
    // los cuales son trasladados a las herramientas que en este caso
    // es un rectangulo.
    canvas.ev_canvas = function(ev) {
        if (ev.layerX || ev.layerX == 0) { // Firefox
            ev._x = ev.layerX;
            ev._y = ev.layerY;
        } else if (ev.offsetX || ev.offsetX == 0) { // Opera
            ev._x = ev.offsetX;
            ev._y = ev.offsetY;
        }

        // Si la herramienta de rectangulo no existe la crea y luego llama al metodo
        // que resolvera los metodos soportados..
        if (tool == null) {
            tool = new tools.rect(ctx,image);
        }
        tool[ev.type](ev);
    };


    // Dado que algunas imagenes tienen trasparencia , con double click perkitimos
    // cambiar el background entre blanco y negro para poder ver la imagen.
    // TODO: Leer el background original en caso este no sea blanco o negro.
    canvas.ondblclick = function() {
        console.log("paso");
        if (canvas.style.background !== 'black') {
            canvas.style.background = 'black';
        } else {
            canvas.style.background = 'white';
        }
    }

    if (canCreateHovers == true) {
        canvas.addEventListener('mousedown', canvas.ev_canvas, false);
        canvas.addEventListener('mousemove', canvas.ev_canvas, false);
        canvas.addEventListener('mouseup', canvas.ev_canvas, false);
    }

    // Las herramientas a usar , aqui solo usaremos el rectangulo.
    // solo es util si canCreateHovers = true
    var tools = {};
    // The rectangle tool.
    tools.rect = function (context,image) {
        var tool = this;
        this.started = false;

        this.mousedown = function (ev) {

            tool.started = true;
            tool.x0 = ev._x;
            tool.y0 = ev._y;

            left = tool.x0;
            top = tool.y0;
            width = 0;
            height = 0;

        };

        this.mousemove = function (ev) {
            if (!tool.started) {
                return;
            }

            var x = Math.min(ev._x,  tool.x0),
                y = Math.min(ev._y,  tool.y0),
                w = Math.abs(ev._x - tool.x0),
                h = Math.abs(ev._y - tool.y0);

            context.drawImage(image,0,0,image.width,image.height);

            if (!w || !h) {
                return;
            }

            context.strokeRect(x, y, w, h);

            left = x;
            top = y;
            width = w;
            height = h;
        };

        this.mouseup = function (ev) {
            if (tool.started) {
                tool.mousemove(ev);
                tool.started = false;

                // Si esta definido correctamente un rectangulo , guardo sus valores y lo pinto
                // sobre la imagen.
                if (width >0 && height > 0) {
                    var txt = " " + left + "," + top + "," + width + "," + height + " ";
                    context.fillRect(left,top+height,ctx.measureText(txt).width,  15);
                    context.strokeText(txt, left, top + height + 10);
                }
            }
        };
    };
};

/**
 * Clase que define una ventana que servira para mostrar y o crear hovers sobre la misma.
 * Basicamente sirve de container para un canvas a ser usado como lienzo para dibujar la imagen
 * y efectuar acciones sobre la misma.
 *
 * @version 1.00
 * @Author Carlos arana Reategui.
 * @Date 25-06-2017
 */
isc.defineClass("ImageWindow", "Window");
isc.ImageWindow.addProperties({
    title: "Vista Previa",
    canDragReposition: true,
    canDragResize: true,
    autoCenter: true,
    isModal: true,
    // Objeto tipo canvasImageMgr que presentara  permitira la definicion visual de un hover.
    canvasImageMgr: undefined,
    // Objeto tipo Image creado por este objeto y que servira de insumo a canvasImgMgr
    domImage: undefined,
    // Objeto tipo isc.DynamicForm o isc.DynamicFormExt al cual se copiaran los valores
    // del hover. De ser null no se copiara nada.
    targetForm: undefined,
    // string conteniendo el url fuente para obtener la imagen.
    imgSrc: undefined,
    // De existri un hover ya definido en estos 4 miembros iran los valores iniciales
    // para la presentacion del hover ya predefinido.
    hoverLeft: undefined,
    hoverTop: undefined,
    hoverWidth: undefined,
    hoverHeight: undefined,
    // Si este miembro es false solo presentara la imagen mas no permitira accion alguna sobre la misma.
    canCreateHovers: true,
    isImgMirror : false,
    copyImageSize: false,


    /**
     * Metodo standar el cual es overloaded para limpieza de elementos creados por ese
     * objeto.
     */
    close: function() {
        // Dado que este no es un objeto SmartClient por ende lo limpiamos directamente.
        if (this.canvasImageMgr) {
            this.canvasImageMgr.cleanUp();
            this.canvasImageMgr = undefined;
        }

        // Limpiamos la variable de imagen/
        this.domImage.onload = null;
        this.domImage=null;

        // Cerramos e indicamos que debe ser destruido no solo escondido.
        this.Super('close',arguments);
        this.markForDestroy();
    },
    init: function() {
        var self = this;

        // Los header controls son definidos antes del init para que sean tomados
        // en cuenta , pero solo si la funcion de create hovers sera usada ya que de lo
        // contrario el boton no sera de utilidad.
        if (this.canCreateHovers == true || this.copyImageSize == true) {
            this.headerControls = ["headerLabel", isc.Button.create({
                autoDraw: false,
                width: 120,
                title: 'Copiar',
                click: function() {
                    if (self.canCreateHovers == true) {
                        // Copia el rectangulo del hover marcado a la forma indicada
                        // por targetForm , siempre que el metodo setHoverRectangleValues
                        // este definido y que realmente los puntos representen un rectangulo.
                        var rect = self.canvasImageMgr.getRectangle();

                        if (rect.width <= 0 || rect.height <= 0) {
                            isc.warn('No se especifica el ancho o el largo');
                        } else {
                            if (self.targetForm && typeof self.targetForm.setHoverRectangleValues === 'function') {
                                self.targetForm.setHoverRectangleValues(rect.left, rect.top, rect.width, rect.height);
                                self.close();
                            }
                        }
                    } else {
                        var size = self.canvasImageMgr.getImageSize();

                        if (size.width <= 0 || size.height <= 0) {
                            isc.warn('No se especifica las dimensiones de la imagen');
                        } else {
                            if (self.targetForm && typeof self.targetForm.setImageSizeValues === 'function') {
                                self.targetForm.setImageSizeValues(size.width, size.height);
                                self.close();
                            }
                        }
                    }
                }
            }), "closeButton"];
        }

        this.Super("init",arguments);

        // Agregamos el container interior con el canvas.
        this.addItem(isc.HTMLFlow.create({
            autoDraw: false,
            padding: 4,
            ID:'htmlfCanvas'+this.ID,
            contents:"<canvas  id='imageCanvas'></canvas>"}));

        // Creamos el objeto imagen.
        this.domImage = new Image();

        /****************************************************************************
         * Manejadores de eventos del objeto imagen , tanto para onnload y onerror
         */
        this.domImage.onload = function () {

            self.setTitle("Vista Previa ("+self.domImage.width+" x "+self.domImage.height+")");

            // Dado que la imagen a esta altura ya esta cargada la pasamos al manejador de la misma.
            self.canvasImageMgr = new canvasImageMgr("imageCanvas",self.domImage,self.hoverTop,self.hoverLeft,
                                                        self.hoverWidth,self.hoverHeight,self.canCreateHovers,self.isImgMirror);

            // Ajustamos el ancho y alto de esta ventana poniendo como limites inferiores y superiores
            // valores razonables.
            var hh = self.domImage.height+40;
            var ww = self.domImage.width+20;

            if (self.domImage.height >= 768) {
                hh = 804;
            } else if (self.domImage.height <= 64) {
                hh = 104;
            }

            if (self.domImage.width >= 1024) {
                ww = 1064;
            } else if (self.domImage.width <= 220) {
                ww = 220;
            }

            self.resizeTo(ww,hh);
        };

        this.domImage.onerror = function() {
            isc.warn('Error cargando la imagen , posiblemente no existe o el servidor destino esta apagado');
            self.close();
        };

        // Al final por recomendaciones w3c ***y cargamos la imagen.
        this.domImage.src = this.imgSrc;

    }
});