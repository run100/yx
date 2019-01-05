<template>
    <div>
        <div style="height: 400px"></div>
        <box gap="10px 10px">
            <x-button type="primary" :show-loading="showLoading && type == 'single'" @click.native="single()" >
                个人报名
            </x-button>
            <x-button type="primary" :show-loading="showLoading && type == 'team'" @click.native="single('team')">
                团队报名
            </x-button>
        </box>
    </div>
</template>

<script>
    import { XButton, Box  } from 'vux';
    import Test from './Test.vue';
    import axios from 'axios';

    export default {
        components: {
            XButton,
            Box,
        },
        data() {
            return {
                showLoading: false,
                type: ''
            }

        },
        methods: {
            single(type) {
                this.type = type == 'team' ? 'team' : 'single';
                this.showLoading = true;
                axios.get(`/lua/baoming/check-register?proj=${projectId}&type=${this.type}`)
                    .then(res => {
                        this.showLoading = false;
                        if (res.data.code === 0) {
                            this.$router.push('/register/' + this.type)
                        } else {
                            this.$vux.alert.show({
                                title: res.data.msg
                            })
                        }
                    }).catch(error => {
                        this.showLoading = false;
                        this.$vux.alert.show({
                            title: '出错了'
                        })
                });


            }
        }
    }
</script>

