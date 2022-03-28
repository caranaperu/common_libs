isc.DynamicForm.create({
    width:500,
    fields: [
        {   
            name: "manager", 
            title: "ComboBoxItem", 
            editorType: "ComboBoxItem", 
            wrapTitle: false,
            optionDataSource: "employees", 
            valueField: "EmployeeId", 
            displayField: "Name",
            dataSetType: "tree",
            pickListWidth: 350, 
            pickListProperties: { autoFitFieldWidths: true },
            pickListFields: [
                { name: "Name" },
                { name: "Email" }
            ],
            autoOpenTree: "all"
        },
        {   
            name: "manager2", 
            title: "SelectItem", 
            editorType: "SelectItem", 
            wrapTitle: false,
            optionDataSource: "employees", 
            valueField: "EmployeeId", 
            displayField: "Name",
            dataSetType: "tree",
            pickListWidth: 350, 
            pickListProperties: { autoFitFieldWidths: true },
            pickListFields: [
                { name: "Name" },
                { name: "Email" }
            ],
            autoOpenTree: "all"
        }
    ]
});