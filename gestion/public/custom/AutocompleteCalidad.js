dojo.provide("custom.AutocompleteCalidad");
dojo.require("dojox.data.QueryReadStore");
dojo.declare("custom.AutocompleteCalidad", dojox.data.QueryReadStore, {
    fetch:function (request) {
        request.serverQuery = { qcalidad:request.query.calidad };
        return this.inherited("fetch", arguments);
    }
});