(function() {

    var UnitPassword = {

        extends: AbstractUnit,

        template: '#unit-password-template',

        methods: {

            onInputChange: function(e) {
                this.$emit('change', e.target.value, this);
            }

        }

    };

    Vue.component('unit-password', UnitPassword);

}());