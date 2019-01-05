<template>
    <div class="weui-cell weui-cells_checkbox" style="padding-top: 0;padding-bottom: 0;">
        <label class="weui-cell__hd">{{title}}</label>
        <label class="weui-cell weui-check__label" v-for="option,index in options">
            <div class="weui-cell__hd">
                <input type="radio" class="weui-check" v-model="currentValue" :value="option.key">
                <i class="weui-icon-checked"></i>
            </div>
            <div class="weui-cell__bd">
                <p>{{option.value}}</p>
            </div>
        </label>
    </div>
</template>
<script>
    export default {
        props: {
            value: [String, Number],
            options: {
                type: Array,
                required: true
            },
            title: {
                type: String
            },
        },
        data() {
            return {
                currentValue: ''
            }
        },
        mounted() {
            if (this.options[0] && this.options[0].key !==undefined) {
                this.currentValue = this.options[0].key
            }
        },
        watch: {
            value(value) {
                this.currentValue = value;
            },
            currentValue(value) {
                this.$emit('input', value);
            }

        }

    }

</script>