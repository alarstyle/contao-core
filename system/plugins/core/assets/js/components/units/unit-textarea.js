(function() {

    var UnitTextarea = {

        extends: AbstractUnit,

        template: '#unit-textarea-template',

        data: function() {
            return {

            }
        },

        methods: {

            handleChange: function(newValue) {
                this.currentValue = newValue;
            }

        }

    };

    Vue.component('unit-textarea', UnitTextarea);

}());