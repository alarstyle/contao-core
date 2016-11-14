(function () {

    var Pagination = {

        template: '#pagination-template',

        props: {
            current: Number,
            total: Number
        },

        data: function () {
            return {
                pages: []
            }
        },

        watch: {
            total: function (newTotal) {

            },
            current: function (newCurrent) {

            }
        },

        methods: {

            onClick: function (e) {
                this.isOpened = true;
            }

        },

        created: function () {

        }

    };

    Vue.component('pagination', Pagination);

}());