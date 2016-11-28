(function () {

    var ListingTable = {

        template: '#listing-table-template',

        props: {
            headers: {type: Array, default: function() { return []; }},
            items: {type: Array, default: function() { return []; }}
        },

        watch: {
        },

        methods: {

            emitOperation: function(id, operationName) {
                this.$emit('operation', id, operationName);
            }

        },

        created: function () {

        }

    };

    Vue.component('listing-table', ListingTable);

}());