(function() {

    var UnitRadio = {

        extends: AbstractUnit,

        template: '#unit-radio-template',

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
                if (!e.target.checked) return;
                this.$emit('change', e.target.value, this);
            }

        }

    };

    Vue.component('unit-radio', UnitRadio);

}());