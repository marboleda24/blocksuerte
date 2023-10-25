<template>
    <font-awesome-icon v-if="props.sortable" :icon="icon" class="fa-pull-right"/>
</template>

<script lang="jsx">
export default {
    name: "VtSortControl",
    props: ['props'],
    computed: {
        icon() {
            if (!this.props.sortStatus.sorted) return 'sort';
            return this.props.sortStatus.asc ? 'sort-up' : 'sort-down';
        }
    }
}
</script>
