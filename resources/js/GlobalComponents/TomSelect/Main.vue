<template>
    <select
        v-select-directive="{ props, emit, computedOptions }"
        class="tom-select"
    >
        <slot></slot>
    </select>
</template>

<script setup>
import {computed, watch} from "vue";
import {setValue, init, reInit} from "./index";
import dom from "@left4code/tw-starter/dist/js/dom";

const vSelectDirective = {
    mounted(el, {value}) {
        // Clone the select element to prevent tom select remove the original element
        const clonedEl = dom(el).clone().insertAfter(el)[0];
        dom(el).attr("hidden", true);

        // Initialize tom select
        setValue(clonedEl, value.props);
        init(clonedEl, value.emit, value.computedOptions);
    },
    updated(el, {value}) {
        const clonedEl = dom(el).next()[0];
        setValue(clonedEl, value.props);
        reInit(clonedEl, value.props, value.emit, value.computedOptions);
    },
};

const props = defineProps({
    options: {
        type: Object,
        default() {
            return {};
        },
    },

    modelValue: {
        type: [String, Number, Array],
        default: "",
    },
});

const emit = defineEmits();

// Compute all default options
const computedOptions = computed(() => {
    let options = {
        ...props.options,
        plugins: {
            ...props.options.plugins,
        },
    };

    if (Array.isArray(props.modelValue)) {
        options = {
            persist: false,
            create: false,
            onDelete: function (values) {
                return confirm(
                    values.length > 1
                        ? "¿Estás seguro de que quieres eliminar " +
                        values.length +
                        " items?"
                        : '¿Estás seguro de que quieres eliminar "' + values[0] + '"?'
                );
            },
            ...options,
            plugins: {
                remove_button: {
                    title: "Eliminar este elemento",
                },
                ...options.plugins,
            },
        };
    }

    return options;
});


// Watch value change
watch(
    computed(() => props.modelValue),
    () => {
        emit("change", props.modelValue);
    },
);

</script>
