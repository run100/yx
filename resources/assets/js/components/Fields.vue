<template>
    <group title="填写信息">
        <template v-for="field in fields" v-if="field.registration || field.confirm">
            <template  v-if="field.type == 'name'">
                <x-input ref="input" v-model="data[field.field]" :required="Boolean(field.required)" :title="field.name"  placeholder="请输入姓名" is-type="china-name"></x-input>
            </template>
            <template v-else-if="field.type == 'phone'">
                <x-input ref="input" v-model="data[field.field]" :required="Boolean(field.required)" title="手机" mask="999 9999 9999" placeholder="请输入手机号码"  :max="13" is-type="china-mobile"></x-input>
            </template>

            <template v-else-if="field.type == 'gender'">
                <inline-radio v-model="data[field.field]" :options="[{key: 'male', value: '男'},{key: 'female', value: '女'}]" title="性别"></inline-radio>
            </template>
            <template v-else-if="field.type == 'city'">
                <x-address v-model="data[field.field]"
                           :title="(data[field.field] = ['安徽省', '合肥市', '蜀山区'], field.name)" raw-value
                           :list="addressData" value-text-align="right" placeholder="请选择地址"></x-address>
            </template>
            <template v-else-if="field.type == 'string'">
                <x-input ref="input" v-model="data[field.field]" :required="Boolean(field.required)" :title="field.name" :placeholder="'请输入' + field.name" is-type=""></x-input>
            </template>
            <template v-else-if="field.type == 'integer'">
                <x-number  v-model="data[field.field]" :value="data[field.field]=10" :title="field.name" fillable></x-number>
            </template>
            <template v-else-if="field.type == 'select'">
                <selector  v-model="data[field.field]" ref="defaultValueRef" :title="field.name" :options="field.options.select.options.map(mapSelectOptions)" ></selector>
            </template>
            <template v-else-if="field.type == 'checkbox'">
                <checklist  v-model="data[field.field]" :title="field.name" label-position="left" required :options="field.options.checkbox.options.map(mapCheckboxOptions)"></checklist>
            </template>
            <template v-else-if="field.type == 'radio'">
                <radio  v-model="data[field.field]" :options="field.options.radio.options.map(mapRadioOptions)" ></radio>
            </template>
            <template v-else-if="field.type == 'datetime'">
                    <datetime  v-model="data[field.field]"
                               :format="field.options.datetime.datetime_type == 'date' ? 'YYYY-MM-DD' : field.options.datetime.datetime_type == 'time' ? 'HH:mm' : 'YYYY-MM-DD HH:mm'"
                               :required="Boolean(field.required)" :title="field.name"></datetime>
            </template>
            <template v-else-if="field.type == 'vote'">
                <x-number
                        :min="(data[field.field] = 1, 1)"
                        v-model="data[field.field]"
                           :title="field.name" fillable></x-number>
            </template>
            <template v-else-if="field.type == 'idcard'">
                <x-input  ref="input" v-model="data[field.field]" :required="Boolean(field.required)" :title="field.name" :placeholder="'请输入'+field.name" is-type=""></x-input>
            </template>
            <template v-else-if="field.type == 'passport'">
                <selector title="证件类型"
                          :options="(data[field.field+'_type'] = 'SFZ', identifications)"
                          v-model="data[field.field+'_type']"
                ></selector>
                <x-input  ref="input" v-model="data[field.field]" :required="Boolean(field.required)" :title="field.name" placeholder="请输入证件号码" is-type=""></x-input>

            </template>
            <template v-else-if="field.type == 'birthday'">
                <datetime  v-model="data[field.field]" title="生日" :required="Boolean(field.required)"></datetime>
            </template>
            <template v-else-if="field.type == 'age'">
                <x-number  v-model="data[field.field]" :min="0" :value="data[field.field]=20" title="年龄" fillable></x-number>
            </template>
            <template v-else-if="field.type == 'text'">
                <x-textarea  v-model="data[field.field]" :max="100" :placeholder="field.name"></x-textarea>
            </template>
        </template>
    </group>
</template>
<script>

    import {ChinaAddressV4Data,Checker, CheckerItem, Datetime, Radio, Group, XInput, XButton, XNumber, XAddress, XTextarea, Checklist, Selector} from 'vux';
    import InlineRadio from './InlineRadio.vue';

    export default {
        props: ['fields', 'data'],
        components: {
            Checker,
            CheckerItem,
            Radio,
            Datetime,
            Group,
            XInput,
            XButton,
            XNumber,
            XAddress,
            XTextarea,
            Checklist,
            Selector,
            InlineRadio
        },
        data() {
            return {
                addressData: ChinaAddressV4Data,
                identifications: [
                    {key: 'SFZ', value: '身份证'},
                    {key: 'TBZ', value: '台胞证'},
                    {key: 'GAT', value: '港澳台通行证'},
                    {key: 'HUZ', value: '护照'},
                ],
                defaultAddress: [],
            }
        },
        methods: {
            mapSelectOptions(data) {
                return {
                    key: data.key,
                    value: data.name
                }
            },
            mapCheckboxOptions(data) {
                return data.name;
            },
            mapRadioOptions(data) {
                return data.name;
            }
        }
    }

</script>
<style scoped>
    .gender-item {

    }
</style>
