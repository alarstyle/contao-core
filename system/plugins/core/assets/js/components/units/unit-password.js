(function() {

    var UnitPassword = _.defaultsDeep({

        template: '#unit-password-template',

        data: function() {
            return {

            }
        },

        mounted: function() {

        },

        methods: {

            onInputChange: function(e) {
                this.$emit('change', e.target.value, this);
            }

        }

    }, AbstractUnit);

    Vue.component('unit-password', UnitPassword);

}());