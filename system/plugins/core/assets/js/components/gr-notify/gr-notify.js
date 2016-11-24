(function () {

    var id = 0;

    function getId() {
        return id++;
    }

    var Notice = {

        template: '#notice-template',

        data: function () {
            return {
                items: []
            }
        },

        watch: {
            total: function (newTotal) {

            },
            current: function (newCurrent) {

            }
        },

        methods: {

            add: function (settings) {
                var item = {
                    id: getId(),
                    message: settings.message,
                    class: 'item--' + settings.type
                };
                this.items.push(item);
                setTimeout(this.remove, 5000, item.id);
            },

            remove: function (id) {
                this.items = _.filter(this.items, function(item) {
                    return item.id !== id;
                });
                //this.items = _.union(_.slice(this.items, 0, index), _.slice(this.items, index+1));
            },

            itemClick: function(id) {
                this.remove(id);
            }

        },

        created: function () {

            var _this = this;

            this.$root.$on('notify', this.add);

        }

    };

    Vue.component('gr-notify', Notice);

}());