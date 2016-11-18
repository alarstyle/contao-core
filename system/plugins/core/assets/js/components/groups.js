(function () {

    var Groups = {

        template: '#groups-template',

        props: {
            title: String,
            data: {type: Object, default: []}
        },

        data: function () {
            return {
                list: []
            }
        },

        watch: {},

        methods: {

            onGroupClick: function (e) {

            },

            newGroup: function () {

            }

        },

        mounted: function () {
            this.list = this.data.list;
        }

    };

    Vue.component('groups', Groups);

}());