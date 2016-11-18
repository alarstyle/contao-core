(function () {

    var ListingTable = {

        template: '#listing-table-template',

        props: {
            headers1: {type: Array, default: function() { return []; }},
            items1: {type: Array, default: function() { return []; }}
        },

        data: function () {
            return {
                headers: [
                    { name: 'img', label: ''},
                    { name: 'name', label: 'User Name'},
                    { name: 'category', label: 'User Category'},
                    { name: 'operations', label: ''}
                ],
                items: [
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