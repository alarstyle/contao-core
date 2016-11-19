(function () {

    var ListingTable = {

        template: '#listing-table-template',

        props: {
            headers: {type: Array, default: function() { return []; }},
            items: {type: Array, default: function() { return []; }}
        },

        data: function () {
            return {
                items1: [
                    {
                        id: 1,
                        fields: [
                            '',
                            'Mikael <span>Anderson</span>',
                            'Manager',
                            {
                                operations: [
                                    {
                                        name: 'gogogo',
                                        label: 'Edit',
                                        icon: 'pencil'
                                    },
                                    {
                                        name: 'gogogo',
                                        label: 'Disable',
                                        icon: 'eye'
                                    },
                                    {
                                        name: 'gogogo',
                                        label: 'Delete',
                                        icon: 'trash'
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        id: 2,
                        fields: [
                            '',
                            'Mikael <span>Anderson</span>',
                            'Manager',
                            {
                                operations: [
                                    {
                                        name: 'gogogo',
                                        label: 'Edit',
                                        icon: 'pencil'
                                    },
                                    {
                                        name: 'gogogo',
                                        label: 'Disable',
                                        icon: 'eye'
                                    },
                                    {
                                        name: 'gogogo',
                                        label: 'Delete',
                                        icon: 'trash'
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        id: 3,
                        fields: [
                            '',
                            'Mikael <span>Anderson</span>',
                            'Manager',
                            {
                                operations: [
                                    {
                                        name: 'gogogo',
                                        label: 'Edit',
                                        icon: 'pencil'
                                    },
                                    {
                                        name: 'gogogo',
                                        label: 'Disable',
                                        icon: 'eye'
                                    },
                                    {
                                        name: 'gogogo',
                                        label: 'Delete',
                                        icon: 'trash'
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        id: 1,
                        fields: [
                            '',
                            'Mikael <span>Anderson</span>',
                            'Manager',
                            {
                                operations: [
                                    {
                                        name: 'gogogo',
                                        label: 'Edit',
                                        icon: 'pencil'
                                    },
                                    {
                                        name: 'gogogo',
                                        label: 'Disable',
                                        icon: 'eye'
                                    },
                                    {
                                        name: 'gogogo',
                                        label: 'Delete',
                                        icon: 'trash'
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
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