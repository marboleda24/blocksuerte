<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                Requerimientos administrativos
            </h2>
        </portal>

        <div>
            <embed src="http://glpi.ciev.local/marketplace/formcreator/front/formdisplay.php?id=1" width="100%" height="1100" quality="high" type="text/html">
        </div>
    </div>
</template>

<script lang="jsx">

    import {defineComponent} from "vue";

    export default defineComponent({
        metaInfo: {
            title: 'Requerimientos administrativos'
        }
    })
</script>
