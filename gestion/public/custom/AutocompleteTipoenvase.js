dojo.provide("custom.AutocompleteTipoenvase");
dojo.require("dojox.data.QueryReadStore");
dojo.declare("custom.AutocompleteTipoenvase", dojox.data.QueryReadStore, {
    fetch:function (request) {
		var calidad = dojo.byId('calidad').value;
		var color = dojo.byId('color').value;
        request.serverQuery = { qtipoenvase:request.query.tipoenvase,qcalidad:calidad,qcolor:color };
        return this.inherited("fetch", arguments);
    }
});