<template>
    <ul class="ui-tree-container">
        <li v-for="item in items" :class="'tree-item-'+item.id">
            <span class="title" @click="selected(item)">{{ item.name }}</span>
            <div v-if="item.hasOwnProperty('children')">
                <ui-tree-component
                        :items="item.children"
                        v-on:select="onChildSelected"
                ></ui-tree-component>
            </div>
        </li>
    </ul>
</template>

<script>
    export default {
        name: "ui-tree-component",
        props: {
            items: Array
        },
        methods: {
            selected(item) {
                this.$emit('select', item);
            },
            onChildSelected(item) {
                this.$emit('select', item, true);
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../sass/variables";
    .ui-tree-container {
        .title {
            cursor: pointer;
        }
    }
</style>