<template>
    <div>
        <Head title="Documentos empleados"/>

        <portal to="application-title">
            Documentos empleados
        </portal>
        <div class="intro-y box">
            <div class="p-5">
                <div class="grid grid-cols-3 gap-5">
                    <input type="number" v-model="form.document" class="form-control" placeholder="Documento empleado"/>
                    <button class="btn btn-primary" @click="generateDocument('working-letter', this.form.document)">
                        Carta Laboral
                    </button>
                    <button class="btn btn-danger" @click="generateDocument('peace-safe', this.form.document)">
                        Carta Retiro
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3";

export default {
    components: {
        Head,
        Link
    },

    data() {
        return {
            form: {
                document: ''
            }
        }
    },

    methods: {
        generateDocument(type, employee) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.post(route('employee-documents.generate_document'), {
                type: type,
                document: employee
            }, {
                responseType: 'blob'
            }).then(resp => {
                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');
                link.href = url;

                let name = type === 'working-letter' ? `carta-laboral-${employee}.pdf` : `carta-paz-y-salvo-${employee}.pdf`

                link.setAttribute('download', name);
                document.body.appendChild(link);
                link.click();

                this.$swal.close();
                this.resetForm()
            }).catch(async err => {
                console.log(err)
                let errorString = JSON.parse(await err.response?.data?.text());

                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: errorString,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Aceptar',
                });

                this.resetForm()
            })
        },

        resetForm(){
            this.form = {
                document: ''
            }
        }
    }
}
</script>
