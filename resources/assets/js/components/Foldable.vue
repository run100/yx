<template>
    <li>
        <div class="foldable-title" @click="toggle">
            <p>{{title}}</p>
        </div>
        <div v-show="show" class="foldable-content">
                <slot></slot>
        </div>
    </li>
</template>
<script>
    export default {
        props: ['title', 'open'],
        data() {
            return {
                show: this.open
            }
        },
        methods: {
            toggle() {
                this.show = !this.show;
            }
        }

    }
</script>
<style scoped>
    .foldable-title {
        padding: 20px;
    }
     li {
         background: #ffffff;
         margin: 10px 0 10px 0;
     }

</style>