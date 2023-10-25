<template>
    <input type="checkbox" :value="value" v-model="proxyChecked"
           class="form-check-input border mr-1">
</template>

<script lang="jsx">
    export default {
        model: {
            prop: "checked",
            event: "change",
        },

        props: {
            checked: {
                type: [Array, Boolean],
                default: false,
            },
            value: {
                default: null,
            },
        },

        computed: {
            proxyChecked: {
                get() {
                    return this.checked;
                },
                set(val) {
                    this.$emit("change", val);
                },
            },
        },
    }
</script>

