dojo.provide("js.custom.AutocompleteColor");
dojo.require("dojox.data.QueryReadStore");
dojo.declare("js.custom.AutocompleteColor", dojox.data.QueryReadStore, {
    fetch:function (request) {
		var calidad = dojo.byId('calidad').value;
        request.serverQuery = { qcolor:request.query.color,qcalidad:calidad };
        return this.inherited("fetch", arguments);
    }
});