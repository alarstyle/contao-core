(function() {

    var UnitText = {

        extends: AbstractUnit,

        template: '#unit-kit-template',

        data: function() {
            return {
                items: []
            }
        },

        watch: {
            value: {
                handler: function (value) {
                    this.updateItems(value);
                    console.log('KIT VALUE CHANGEd')
                },
                deep: true
            }
        },

        methods: {
            handleUnitChange: function(value, unit) {
                var unitId = unit.id,
                    itemIndex, fieldName;
                [itemIndex, fieldName] = unitId.split('|');
                this.items[itemIndex][fieldName] = value;
                console.log('KIT CHANGED');
                console.log(this.items);

                var _this = this;
                Vue.nextTick(function() {
                    _this.$emit('change', _this.items, _this);
                });
            },

            updateItems: function(value) {
                if (!this.config.fields) return;
                this.items = value ? JSON.parse(JSON.stringify(value)) : [];
                // if (this.items.length === 0) {
                //     this.addItem();
                // }
            },

            addItem: function() {
                var item = {};
                Object.keys(this.config.fields).each(function(key) {
                    item[key] = null;
                });
                this.items.push(item);
            },

            deleteItem: function(itemIndex) {
                this.items.splice(itemIndex, 1);
                // if (this.items.length === 0) {
                //     this.addItem();
                // }

                var _this = this;
                Vue.nextTick(function() {
                    _this.$emit('change', _this.items, _this);
                });
            }
        },

        mounted: function() {
            console.log("KIT MOUNTED");
            console.log(this.config);
            this.updateItems(this.value);
        }

    };

    Vue.component('unit-kit', UnitText);

}());