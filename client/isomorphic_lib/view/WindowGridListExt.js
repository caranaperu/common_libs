
/**
 * Clase generica para la definicion de la ventana que presenta la lista de registros
 * a procesar , basicamente provee metodos requeridos por el controlador y el funcionamiento
 * basico de todas las similares. ESe api de comunicacion es parte de la interfase IControlledCanvas
 * el cual interactua directamente con el controlador.
 *
 * @version 1.00
 * @since 1.00
 * $Author: aranape $
 * $Date: 2014-06-24 00:38:53 -0500 (mar, 24 jun 2014) $
 * @history
 *          Modificacion 17-11-2021 - Si la grilla indica que no se puede
 *          agregar o editar se desabilitan los correspondiente botones del toolbar
 */
isc.defineClass("WindowGridListExt", "Window", "IControlledCanvas");

isc.WindowGridListExt.addProperties({
    canDragResize: true,
    showFooter: false,
    autoCenter: true,
    // Inicializamos los widgets interiores
    initWidget: function() {
        this.Super("initWidget", arguments);

        var toolStrip = this._createToolStrip();
        var gridList = this._createGridList();
        if (gridList.fetchOperation === undefined) {
            gridList.fetchOperation = 'fetch';
        }
        gridList.reselectOnUpdate = true;
        gridList.showHeaderMenuButton = false;
        this.addItem(toolStrip);
        this.addItem(gridList);


        // Modificacion 17-11-2021
        // Si la grilla indica que no se puede agregar o editar se desabilitan los
        // correspondiente botones del toolbar

        // Modificacion 04-12-2021
        // Si la grilla indica que no se puede eliminar se desabilitan boton
        // correspondiente.
        if (gridList.canAdd == false) {
            this.getToolbarControl('add').setDisabled(true);
        }
        if (gridList.canEdit == false) {
            this.getToolbarControl('edit').setDisabled(true);
        }
        if (gridList.canRemove == false) {
            this.getToolbarControl('del').setDisabled(true);
        }
    }
});