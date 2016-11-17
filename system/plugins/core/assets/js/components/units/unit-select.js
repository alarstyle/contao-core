(function() {

    var UnitSelect = _.defaultsDeep({

        template: '#unit-select-template',

        data: function() {
            return {
                options: []
            }
        },

        mounted: function() {
            this.options = this.config.options;
        },

        methods: {

            onInputChange: function(e) {
                console.log(e.target.value);
                this.$emit('change', e.target.value, this);
            }

        }

    }, AbstractUnit);

    Vue.component('unit-select', UnitSelect);

}());