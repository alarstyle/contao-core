(function() {

    var UnitTextarea = {

        extends: AbstractUnit,

        template: '#unit-textarea-template',

        methods: {

            handleTextareaChange: function(newValue) {
                this.currentValue = newValue;
                this.$emit('change', this.currentValue, this);
            },

            reset: function () {
                this.$refs.textarea.reset();
            }

        }

    };

    Vue.component('unit-textarea', UnitTextarea);

}());