(function() {

    var UnitTextarea = {

        extends: AbstractUnit,

        template: '#unit-textarea-template',

        methods: {

            handleTextareaChange: function(newValue) {
                this.currentValue = newValue;
                this.$emit('change', this.currentValue, this);
            }

        }

    };

    Vue.component('unit-textarea', UnitTextarea);

}());