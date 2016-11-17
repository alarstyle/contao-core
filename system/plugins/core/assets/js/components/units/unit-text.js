(function() {

    var UnitText = _.defaultsDeep({

        template: '#unit-text-template',

        methods: {

            onInputChange: function(e) {
                this.$emit('change', e.target.value, this);
            }

        }

    }, AbstractUnit);

    Vue.component('unit-text', UnitText);

}());