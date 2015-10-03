table = {};

table.messages = {};

table.setDefaultMessages = function() {
    table.messages.serverCommunicationError = 'Ocurrió un error al intentar obtener datos.';
    table.messages.loadingMessage = 'Cargando datos...';
    table.messages.noDataAvailable = 'No se encontraron elementos.';
    table.messages.areYouSure = '¿Seguro?';
    table.messages.deleteConfirmation = '¿Seguro que desea eliminar?';
    table.messages.save = 'Guardar';
    table.messages.saving = 'Guardado';
    table.messages.cancel = 'Cancelar';
    table.messages.sortingMessage = 'Ordenando.';
    table.messages.error = 'Error';
    table.messages.close = 'Cerrar';
    table.messages.cannotLoadOptionsFor = 'No se pudo cargar la información para el elemento' + '{0}';
    table.messages.pagingInfo = 'Mostrando' + ' {0}-{1} ' + 'de' + ' {2}';
    table.messages.pageSizeChangeLabel = 'Filas' + ' ';
    table.messages.gotoPageLabel = 'Ir a página' + ' ';
};

table.sortTable = function(target, column) {
    var currentSort = $(target).jtable('getCurrentSort');
    var sort;

    if (currentSort.length == 0 || currentSort[0].fieldName != column)
        sort = 'asc';
    else
        sort = currentSort[0].sortOrder == 'asc' ? 'desc' : 'asc';

    $(target).find('thead th').removeClass('jtable-column-header-sorted-asc jtable-column-header-sorted-desc');

    var newSort = {fieldName: column, sortOrder: sort};

    $(target).jtable('sortTableByColumns', [newSort]);

    return newSort;
};
