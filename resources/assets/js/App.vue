<template>
        <view-box>
            <x-header slot="header"
                      :left-options="leftOptions"
                      :title="title"
                      style="width:100%;position:absolute;left:0;top:0;z-index:100;"></x-header>
                <router-view class="router-view"></router-view>
        </view-box>
</template>

<script>
    import {ViewBox, XHeader} from 'vux';
    import Index from './components/index';

    export default {
        components: {
            ViewBox,
            XHeader,
        },
        data() {
            return {
                title: title
            }
        },
        computed: {
            leftOptions() {
                return {showBack: this.$route.path !== '/'}
            }
        }
    }
</script>

<style lang="less">
    body {
        background-color: #fbf9fe;
    }
    html, body {
        height: 100%;
        width: 100%;
        overflow-x: hidden;
    }


    .router-view {
        position: relative;
        width: 100%;
        top: 46px;
    }
</style>