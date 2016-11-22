(function () {

    var Groups = {

        template: '#groups-template',

        props: {
            title: String,
            newLabel: String,
            data: {type: Object, default: {}}
        },

        data: function () {
            return {
                active: 0,
                list: [],
                creatable: false
            }
        },

        watch: {
            'data.list': function() {
                this.initData();
            },
            'data.creatable': function() {
                this.initData();
            }
        },

        methods: {

            onGroupClick: function (i) {
                if (this.active === i) return;
                this.active = i;
                this.$emit('group-selected', this.list[i].id);
            },

            onNewClick: function () {
                this.$emit('new-group');
            },

            initData: function() {
                this.list = this.data.list;
                this.creatable = this.data.creatable;
            }

        },

        mounted: function () {
            this.initData();
        }

    };

    Vue.component('groups', Groups);

}());