<template>
    <div>
        <loading text="Loading" :show="loading"></loading>
        <div v-show="!loading">
            <template v-if="$route.params.type == 'single'">
                <fields ref="fields" :fields="fields" :data="data[0]"></fields>
            </template>
            <template v-else>
                <x-number title="人数" v-model="quantity" :min="2" :max="4"></x-number>
                <ul>
                    <foldable  :open="index == 0" :title=" item.name ? item.name :'选手'+(index+1)" v-for="item, index in data">
                        <fields ref="fields" :fields="fields" :data="item"></fields>
                    </foldable>
                </ul>
            </template>
            <box gap="10px 10px">
                <x-button @click.native="submit()" type="primary">确定</x-button>
            </box>
        </div>
    </div>
</template>
<script>
    import axios from 'axios';
    import {Loading, XButton, Group, Box, XNumber} from 'vux';
    import Fields from './Fields.vue';
    import Foldable from './Foldable.vue';
    export default {
        components: {
            XNumber,
            Loading,
            Box,
            Foldable,
            Group,
            Fields,
            XButton
        },
        mounted() {
            axios.get('/lua/baoming/fields?proj='+projectId)
                .then(response => {
                    if (response.data.code === 0) {
                        this.fields = JSON.parse(response.data.msg);
                    } else {
                        this.$vux.alert.show({
                            title: '出错了',
                            content: response.data.msg
                        })

                    }
                    this.loading = false;
                })
        },
        data() {
            return {
                quantity: 2,
                loading: true,
                fields: [],
                data: [{}, {}]
            }
        },
        watch:{
            quantity(newValue, oldValue) {
                console.log('quantity changed');
                if (newValue > oldValue) {
                    for(let i = oldValue; i < newValue; i++) {
                        this.data.push({})
                    }
                } else {
                    for (let i = oldValue; i > newValue; i--) {
                        this.data.pop()
                    }
                }
            }
        },
        methods: {
            submit() {
                let valid = true;
                let error, field;
                let message = '表单填写错误';

                for (let i of this.$refs.fields) {
                    for (let j of i.$refs.input) {
                        j.setTouched();
                        j.validate();
                        valid = valid && j.valid;
                        if (!valid && !error) {
                            error = j.errors;
                            field = j.title;
                        }
                    }
                }

                if (error) {
                    for (let i in error) {
                        message = error[i];
                        if (i == 'required') {
                            message = field + message;
                        }
                        break;

                    }
                }
                if (!valid) {
                    this.$vux.alert.show({
                        title: '错误',
                        content: message
                    });
                    return;
                }

                for (let item of this.data) {
                    for (let field of this.fields) {
                        if (field.required && (item[field.field] === '' || item[field.field] === undefined || item[field.field] === null)) {
                            this.$vux.alert.show({
                                title: ' 请输入' + field.name
                            });
                            return;
                        }
                    }
                }


                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                };
                axios.post('/lua/baoming/registration?proj=' + projectId,
                'data='+JSON.stringify(this.data)
                , config)
                    .then(res => {
                        if (res.data.code === 0) {
                            this.$vux.toast.show({
                                text: '提交成功',
                                time: 500
                            });
                        } else {
                            this.$vux.alert.show({
                                title: '出错了',
                                content: res.data.msg
                            })
                        }
                    })
                    .catch(error => {
                        this.$vux.alert.show({
                            title: '出错了',
                        })
                    })
            },
        }
    }
</script>

<style scoped lang="less">
    .center {
        padding-top: 10px;
        padding-left: 15px;
        color: green;
    }
</style>