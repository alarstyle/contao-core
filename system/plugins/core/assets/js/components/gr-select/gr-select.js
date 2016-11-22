(function () {

    var GrSelect = {

        template: '#gr-select-template',

        props: {
            /**
             * [value, label, disabled]
             */
            name: String,
            value: '',
            label: '',
            options: {
                type: Array,
                default: []
            },
            disabled: {
                type: Boolean,
                default: false
            },
            multiple: {
                type: Boolean,
                default: false
            }
        },

        data: function () {
            return {
                opened: false
            }
        },

        methods: {

            inputClick: function () {
                if (this.disabled)  return;
                if (this.opened) {
                    this.opened = false;
                }
                else {
                    this.opened = true;
                }
            },

            optionClick: function (option) {
                if (this.disabled || option.disabled)  return;
                this.opened = false;
                this.label = option.label || option.value;
                this.value = option.value;
                this.$emit('change', this.value, this);
            },

            selected: function () {
                // this.$emit('input', result);
                // this.$emit('change', result);
            }

        },

        mounted: function () {
            console.log(this.options);
        }

    };

    Vue.component('gr-select', GrSelect);

}());