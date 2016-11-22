(function () {

    var NoticeItem = function(data) {

    };

    var Notice = {

        template: '#notice-template',

        props: {
            current: Number,
            total: Number
        },

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

            onItemClick: function (e) {
                this.isOpened = true;
            }

        },

        created: function () {

            var _this = this;

            this.$root.$on('newNotice', function(data) {
                _this.items(new NoticeItem(data));
            });

        }

    };

    Vue.component('notice', Notice);

}());