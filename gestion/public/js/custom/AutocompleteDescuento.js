dojo.provide("js.custom.AutocompleteDescuento");
dojo.require("dojox.data.QueryReadStore");
dojo.declare("js.custom.AutocompleteDescuento", dojox.data.QueryReadStore, {
    fetch:function (request) {
		var calidad = dojo.byId('calidad').value;
		var idCliente = dojo.byId('idCliente').innerHTML;
        request.serverQuery = { qdescuento:request.query.descuento,qidCliente:idCliente,qcalidad:calidad };
        return this.inherited("fetch", arguments);
    }
});