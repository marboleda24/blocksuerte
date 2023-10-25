<template>
    <div class="relative space-y-1" v-if="showInputValue">
        <input
            type="text"
            id="search"
            v-model="searchText"
            placeholder="Escribe aqui"
            class="form-control"
            :class="customClass"
            autocomplete="off"
        >

        <ul v-show="showResults && searchValues.length > 0"
            class="w-full rounded bg-white border border-gray-300 px-4 py-2 space-y-1 absolute z-50 dark:bg-darkmode-700 max-h-72 overflow-y-auto" >
            <li class="px-1 pt-1 pb-2 font-bold border-b border-gray-200">
                Mostrando {{ searchValues.length }} resultados.
            </li>
            <li
                v-for="value in searchValues"
                :key="value"
                @click="selectValue(value)"
                class="cursor-pointer hover:bg-gray-100 p-1"
            >
                <a href="javascript:void(0)" class="w-full">
                    {{ value[showField] }}
                </a>
            </li>
        </ul>
    </div>

    <div class="input-group" v-else>
        <input type="text"
               class="form-control"
               :class="customClass"
               :value="selectedValue[showField]" disabled>
        <div class="input-group-text cursor-pointer" @click="resetValue">
            <font-awesome-icon icon="xmark"/>
        </div>
    </div>
</template>

<script setup>
import {ref, computed, watch, defineExpose} from 'vue'

const emit = defineEmits()

const props = defineProps({
    title: {
        type: String,
        default: 'Escribe algo para comenzar la búsqueda…'
    },
    selectedText: {
        type: String,
        default: 'Registro seleccionado: '
    },
    url: {
        type: String,
        default: ''
    },
    showField: {
        type: String,
        default: 'name'
    },
    initValue: {
        type: String,
        default: ''
    },
    customClass: {
        type: String,
        default: ''
    },
    iterateKey: {
        type: Number,
        default: 0
    }
});

let searchText = ref('')
const selectedValue = ref({});
const searchValues = ref([]);
const showResults = ref(false);
const showInputValue = ref(true);

const CancelToken = axios.CancelToken;
let source;

watch(
    computed(() => searchText.value),
    () => {
        if (searchText.value === '') {
            searchValues.value = []
            showResults.value = false
        }else {
            if (source) {
                source.cancel();
            }
            source = CancelToken.source();

            axios.get(props.url, {
                cancelToken: source.token,
                params: {
                    q: searchText.value
                }
            }).then(resp => {
                searchValues.value = resp.data
                showResults.value = true
            }).catch(err => {
                searchValues.value = []
            })
        }
    },
)

const selectValue = (value) => {
    selectedValue.value = value
    showResults.value = false
    searchText.value = ''
    emit('selectedValue', value, props.iterateKey)
    showInput(false)
}

const showInput = (ctx) => {
    showInputValue.value = ctx
}

const resetValue = () => {
    showInput(true)
    emit('resetVal')
}

const setValue = (value)=> {
    console.log(value)
    selectValue(value)
}

if (props.initValue){
    selectValue({"value": props.initValue})
}

defineExpose({
    showInput,
    setValue,
})

</script>