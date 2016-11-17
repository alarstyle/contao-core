(function() {

    var UnitCheckbox = _.defaultsDeep({

        template: '#unit-checkbox-template',

        methods: {

            onInputChange: function(e) {
                this.$emit('change', e.target.checked, this);
            }

        }

    }, AbstractUnit);

    Vue.component('unit-checkbox', UnitCheckbox);

}());