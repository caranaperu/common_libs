<html>
<head>
    <meta charset="utf-8">
    <title>Atletismo - Administrador</title>
    <SCRIPT>var isomorphicDir = "./isomorphic/";</SCRIPT>
    <SCRIPT SRC=./isomorphic/system/modules-debug/ISC_Core.js></SCRIPT>
    <SCRIPT SRC=./isomorphic/system/modules-debug/ISC_Foundation.js></SCRIPT>
    <SCRIPT SRC=./isomorphic/system/modules-debug/ISC_Containers.js></SCRIPT>
    <SCRIPT SRC=./isomorphic/system/modules-debug/ISC_Grids.js></SCRIPT>
    <SCRIPT SRC=./isomorphic/system/modules-debug/ISC_Forms.js></SCRIPT>
    <SCRIPT SRC=./isomorphic/system/modules-debug/ISC_DataBinding.js></SCRIPT>
    <SCRIPT SRC=./isomorphic/system/modules-debug/ISC_Calendar.js></SCRIPT>

    <SCRIPT SRC=./isomorphic/skins/EnterpriseBlue/load_skin.js></SCRIPT>

    <SCRIPT SRC=./appConfig.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/view/IControlledCanvas.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/controller/DefaultController.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/view/DynamicFormExt.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/view/WindowBasicFormExt.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/view/WindowBasicFormNCExt.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/view/WindowGridListExt.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/view/TabSetExt.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/controls/PickTreeExtItem.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/controls/ComboBoxExtItem.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/controls/SelectExtItem.js></SCRIPT>
    <SCRIPT SRC=./isomorphic_lib/controls/DetailGridContainer.js></SCRIPT>

    <SCRIPT SRC=./model/PaisesModel.js></SCRIPT>


    <SCRIPT SRC=./model/AtletasModel.js></SCRIPT>
    <SCRIPT SRC=./view/atletas/AtletasWindow.js></SCRIPT>
    <SCRIPT SRC=./view/atletas/AtletasMarcasForm.js></SCRIPT>
    <SCRIPT SRC=./view/atletas/AtletasForm.js></SCRIPT>


</head>
<body></body>
<script>
    controller = isc.DefaultController.create({
        mainWindowClass: 'WinAtletasWindow',
        formWindowClass: 'WinAtletasForm'
    });

    controller.doSetup(false, null);
</script>
</html>