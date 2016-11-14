(function() {

    var List = {

        data: function() {
            return {
                state: '',
                groups: [],
                list: [],
                fields: [],
                currentId: null
            }
        },

        watch: {

        },

        mounted: function() {

            this.showList();

        },

        methods: {

            showList: function() {
                var _this = this;
                this.currentId = null;
                grow.action('getList')
                    .then(function (response) {
                        _this.list = response.data;
                        _this.state = 'list';
                    });
            },

            editItem: function(id) {
                var _this = this;
                grow.action('getListItem', {id: id})
                    .then(function (response) {
                        _this.fields = response.data;
                        _this.state = 'edit_item';
                    });
            },

            newItem: function() {
                this.currentId = null;
                // grow.action('getListItem', {id: 1})
                //     .then(function (response) {
                //         console.log(response.data);
                //     });
                this.state = 'edit_item';
            },

            saveItem: function() {
                var _this = this;
                grow.action('saveItem', {id: id})
                    .then(function (response) {

                    });
            },

            cancelEditItem: function() {
                this.showList();
            }

        }

    };

    window.ExtendedVue = Vue.extend(List);

}());