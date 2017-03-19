(function () {

    var ListingTable = {

        template: '#listing-table-template',

        props: {
            headers: {type: Array, default: function() { return []; }},
            items: {type: Array, default: function() { return []; }},
            sortable: {type: Boolean, default: false}
        },

        data: function() {
            return {
                sortableOptions: {
                    handle: '.item-sort',
                    onSort: this.handleSort
                }
            }
        },

        watch: {
        },

        methods: {

            handleSort: function(event) {
                this.$emit('reorder', event);
            },

            emitOperation: function(id, operationName) {
                this.$emit('operation', id, operationName);
            }

        },

        created: function () {

        }

    };

    Vue.component('listing-table', ListingTable);

}());