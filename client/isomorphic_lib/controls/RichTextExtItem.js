/**
 * Clase que crea un control que envuelve el full rich text editro en un canvas para poder interactura
 * conmo un fomitem mas dentro de un DynamicForm, el unico existente no permite por ejemplo modificar
 * los controles de cabecera y viene con grupos default y punto.
 *
 * Al ser usada dentro de una forma o container el type sera RichTextExt
 *
 *
 * @example
 *  this.form = isc.DynamicFormExt.create({
 *          fields: [
 *              {name: "editor", title: "Editor HTML", editorType: "RichTextExt",},
 *          ]
 *      });
 *
 * @author Carlos Arana Reategui
 * @version 1.00
 * history - 23-06-2017 Version inicial.
 */
isc.ClassFactory.defineClass("RichTextExtItem", "CanvasItem");
isc.RichTextExtItem.addProperties({
    height:"*", width:"*",colSpan:"*",
    rowSpan:"*", endRow:true, startRow:true,
    border: '0px',
    autoDraw: false,

    // this is going to be an editable data item
    shouldSaveValue:true,

    // Implement 'createCanvas' para construit r un container al verdadero RichTextEditor.
    createCanvas : function () {
        var rteditor =  isc.RichTextEditor.create({
            autoDraw: false,
            controlGroups: ["fontControls", "formatControls", "styleControls", "colorControls",
                            "bulletControls","insertControls"],
            valueChanged: function (oldValue, newValue) {
                // parent canvas es el SectionStack
                this.getParentCanvas().canvasItem.storeValue(newValue,newValue);
            }
        });

        return isc.SectionStack.create({
            width: "*",
            border:"1px solid blue",
            autoDraw: false,
            sections: [
                {
                    title: "HTML", expanded: true, items: [rteditor],
                    canCollapse: false
                }
            ]

        });

    },
    setValue: function(value) {
        this.Super('setValue',arguments);

        // Esto representa al RichTextEditor
        this.canvas.sections[0].items[0].setValue(value);

        // Este es el objeto creado en createCanvas
        this.canvas.canvasItem.storeValue(value,value);
    }
});
