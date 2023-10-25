<template>
    <div>
        <div class="intro-y box mt-4" v-if="open">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">Etiqueta Manual</h2>
            </div>
            <div class="p-5">
                <div class="grid sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-4 mt-4">
                    <div>
                        <div class="p-2 border-b text-base font-medium font-bold uppercase">
                            Datos de Impresión
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-5">

                            <div class="form-inline mt-5 col-span-2">
                                <label class="form-label sm:w-20">Producto:</label>
                                <autocomplete
                                    ref="product_item"
                                    url="/orders/search-products"
                                    class="w-full"
                                    show-field="field"
                                    @selected-value="getProductInfo"
                                />
                            </div>

                            <div class="form-inline mt-5">
                                <label class="form-label sm:w-20">Lote:</label>
                                <input type="text" class="form-control" v-model="form.lot"/>
                            </div>

                            <div class="form-inline mt-5">
                                <label class="form-label sm:w-20">Pedido:</label>
                                <input type="text" class="form-control" v-model="form.order"/>
                            </div>

                            <div class="form-inline mt-5">
                                <label class="form-label sm:w-20">Arte:</label>
                                <input type="text" class="form-control" v-model="form.art"/>
                            </div>

                            <div class="form-inline mt-5">
                                <label class="form-label sm:w-20">Cantidad:</label>
                                <input type="number" class="form-control" v-model.number="form.quantity"/>
                            </div>

                            <div class="form-inline mt-5">
                                <label class="form-label sm:w-20">Cantidad del paquete:</label>
                                <input type="number" class="form-control" v-model.number="form.package_quantity"/>
                            </div>

                            <div class="form-inline mt-5">
                                <label class="form-label sm:w-20">Cantidad de etiquetas:</label>
                                <input type="number" class="form-control" v-model.number="form.labels_quantity" disabled/>
                            </div>

                            <div class="form-inline mt-5">
                                <label class="form-label sm:w-20">Revisa:</label>
                                <select class="form-select" v-model="form.reviewer" @change="updateBarcode">
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option v-for="reviewer in reviewers" :value="reviewer.nick">{{ reviewer.name }}</option>
                                </select>
                            </div>

                            <div class="form-inline mt-5">
                                <label class="form-label sm:w-20">Empaca:</label>
                                <select class="form-control" v-model="form.packer" @change="updateBarcode">
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option v-for="packer in packers" :value="packer.nick">{{ packer.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="p-2 border-b text-base font-medium font-bold uppercase">
                            Etiqueta
                        </div>
                        <div class="mt-4 p-2">
                            <img class="mx-auto" :src="url" alt="" draggable="false">

                            <button class="btn btn-secondary mr-2" @click="printFile('zds4m')">Imprimir Blanca</button>
                            <button class="btn btn-danger" @click="printFile('ZDGT800')">Imprimir Rosada</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import dayjs from "dayjs";
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import * as qz from 'qz-tray'

qz.security.setCertificatePromise(function (resolve, reject) {
    resolve("-----BEGIN CERTIFICATE-----\n" +
        "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\n" +
        "-----END CERTIFICATE-----\n" +
        "--START INTERMEDIATE CERT--\n" +
        "-----BEGIN CERTIFICATE-----\n" +
        "MIIFEjCCA/qgAwIBAgICEAAwDQYJKoZIhvcNAQELBQAwgawxCzAJBgNVBAYTAlVT\n" +
        "MQswCQYDVQQIDAJOWTESMBAGA1UEBwwJQ2FuYXN0b3RhMRswGQYDVQQKDBJRWiBJ\n" +
        "bmR1c3RyaWVzLCBMTEMxGzAZBgNVBAsMElFaIEluZHVzdHJpZXMsIExMQzEZMBcG\n" +
        "A1UEAwwQcXppbmR1c3RyaWVzLmNvbTEnMCUGCSqGSIb3DQEJARYYc3VwcG9ydEBx\n" +
        "emluZHVzdHJpZXMuY29tMB4XDTE1MDMwMjAwNTAxOFoXDTM1MDMwMjAwNTAxOFow\n" +
        "gZgxCzAJBgNVBAYTAlVTMQswCQYDVQQIDAJOWTEbMBkGA1UECgwSUVogSW5kdXN0\n" +
        "cmllcywgTExDMRswGQYDVQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMxGTAXBgNVBAMM\n" +
        "EHF6aW5kdXN0cmllcy5jb20xJzAlBgkqhkiG9w0BCQEWGHN1cHBvcnRAcXppbmR1\n" +
        "c3RyaWVzLmNvbTCCAiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBANTDgNLU\n" +
        "iohl/rQoZ2bTMHVEk1mA020LYhgfWjO0+GsLlbg5SvWVFWkv4ZgffuVRXLHrwz1H\n" +
        "YpMyo+Zh8ksJF9ssJWCwQGO5ciM6dmoryyB0VZHGY1blewdMuxieXP7Kr6XD3GRM\n" +
        "GAhEwTxjUzI3ksuRunX4IcnRXKYkg5pjs4nLEhXtIZWDLiXPUsyUAEq1U1qdL1AH\n" +
        "EtdK/L3zLATnhPB6ZiM+HzNG4aAPynSA38fpeeZ4R0tINMpFThwNgGUsxYKsP9kh\n" +
        "0gxGl8YHL6ZzC7BC8FXIB/0Wteng0+XLAVto56Pyxt7BdxtNVuVNNXgkCi9tMqVX\n" +
        "xOk3oIvODDt0UoQUZ/umUuoMuOLekYUpZVk4utCqXXlB4mVfS5/zWB6nVxFX8Io1\n" +
        "9FOiDLTwZVtBmzmeikzb6o1QLp9F2TAvlf8+DIGDOo0DpPQUtOUyLPCh5hBaDGFE\n" +
        "ZhE56qPCBiQIc4T2klWX/80C5NZnd/tJNxjyUyk7bjdDzhzT10CGRAsqxAnsjvMD\n" +
        "2KcMf3oXN4PNgyfpbfq2ipxJ1u777Gpbzyf0xoKwH9FYigmqfRH2N2pEdiYawKrX\n" +
        "6pyXzGM4cvQ5X1Yxf2x/+xdTLdVaLnZgwrdqwFYmDejGAldXlYDl3jbBHVM1v+uY\n" +
        "5ItGTjk+3vLrxmvGy5XFVG+8fF/xaVfo5TW5AgMBAAGjUDBOMB0GA1UdDgQWBBSQ\n" +
        "plC3hNS56l/yBYQTeEXoqXVUXDAfBgNVHSMEGDAWgBQDRcZNwPqOqQvagw9BpW0S\n" +
        "BkOpXjAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQAJIO8SiNr9jpLQ\n" +
        "eUsFUmbueoxyI5L+P5eV92ceVOJ2tAlBA13vzF1NWlpSlrMmQcVUE/K4D01qtr0k\n" +
        "gDs6LUHvj2XXLpyEogitbBgipkQpwCTJVfC9bWYBwEotC7Y8mVjjEV7uXAT71GKT\n" +
        "x8XlB9maf+BTZGgyoulA5pTYJ++7s/xX9gzSWCa+eXGcjguBtYYXaAjjAqFGRAvu\n" +
        "pz1yrDWcA6H94HeErJKUXBakS0Jm/V33JDuVXY+aZ8EQi2kV82aZbNdXll/R6iGw\n" +
        "2ur4rDErnHsiphBgZB71C5FD4cdfSONTsYxmPmyUb5T+KLUouxZ9B0Wh28ucc1Lp\n" +
        "rbO7BnjW\n" +
        "-----END CERTIFICATE-----\n");
});
qz.security.setSignaturePromise(function (toSign) {
    return function (resolve, reject) {
        resolve();
    };
});

export default {
    components: {
        Autocomplete
    },

    props: {
        packers: {
            type: Array,
            default: [],
        },

        reviewers: {
            type: Array,
            default: [],
        },

        open: {
            type: Boolean,
            default: false
        },
        routeFiles: {
            type: String,
            default: "/labels_files/",
        },

        printer: {
            type: Object,
            default: {
                file: 'estrada_label.txt',
                printer: 'zds4m'
            }
        },

        urlLabelary: {
            type: String,
            default: "http://api.labelary.com/v1/printers/8dpmm/labels/4x1.3/0/",
        }
    },

    data() {
        return {
            url: '',
            cfg: null,
            printData: {
                type: 'raw',
                format: 'file',
                data: this.routeFiles + this.printer.printer
            },
            form: {
                code: '',
                description: '',
                lot: '',
                order: '',
                art: '',
                quantity: 1000,
                labels: 1,
                package_quantity: 0,
                labels_quantity: 0,
                reviewer: '',
                packer: '',
            },
        }
    },

    methods: {
        calculate_package_quantity() {
            if (this.form.package_quantity > this.form.quantity) {
                this.form.labels_quantity = 1
            } else {
                this.form.labels_quantity = Math.round(this.form.quantity / this.form.package_quantity)
            }
        },

        getProductInfo(obj){
            this.form.code = obj.code;
            this.form.description = obj.description;
        },

        generateLabel() {
            let label = `
                    ^XA\n
                    ^FO45,65\n
                    ^BCN,40,N,Y,N\n
                    ^FR^FD${this.form.code}^FS\n
                    ^CF0,25\n
                    ^FO45,35^FD${this.form.description}^FS\n
                    ^FO45,110^FD${this.form.code}^FS\n
                    ^FO45,135^FDR: ${this.form.reviewer}^FS\n
                    ^FO45,160^FDE: ${this.form.packer}^FS\n
                    ^FO460,56^FDLOTE: ${this.form.lot}^FS\n
                    ^FO460,80^FDARTE: ${this.form.art}^FS\n
                    ^FO460,105^FDCANTIDAD: ${this.form.package_quantity} unidades^FS\n
                    ^FO460,130^FDOV: ${this.form.order}^FS\n
                    ^FO460,155^FDFECHA: ${this.current_date}^FS\n
                    ^FO460,180^FDESTRADA VELASQUEZ^FS\n
                    ^XZ\n`;

            this.printData = [label]
        },

        updateURL(zpl) {
            this.url = this.urlLabelary + encodeURIComponent(zpl)
        },

        updateBarcode() {
            this.generateLabel()
            this.updateURL(this.printData)
        },

        generateDataLabel() {
            let result = [];
            let quantity = this.form.quantity;

            while (quantity > 0){
                result.push('^XA\n');
                result.push('^FO45,65\n')
                result.push('^BCN,40,N,Y,N\n')
                result.push('^FR^FD' + this.form.code + '^FS\n')
                result.push('^CF0,25\n')
                result.push('^FO45,35^FD' + this.form.description + '^FS\n')
                result.push('^FO45,110^FD' + this.form.code + '^FS\n')
                result.push('^FO45,135^FDR: ' + this.form.reviewer + '^FS\n')
                result.push('^FO45,160^FDE: ' + this.form.packer + '^FS\n')
                result.push('^FO460,56^FDLOTE: ' + this.form.lot + '^FS\n')
                result.push('^FO460,80^FDARTE: ' + this.form.art + '^FS\n')
                if (quantity > this.form.package_quantity){
                    result.push('^FO460,105^FDCANTIDAD: ' + this.form.package_quantity + ' unidades^FS\n')
                }else{
                    result.push('^FO460,105^FDCANTIDAD: ' + quantity + ' unidades^FS\n')
                }
                result.push('^FO460,130^FDOV: ' + this.form.order + '^FS\n')
                result.push('^FO460,155^FDFECHA: ' + this.current_date + '^FS\n')
                result.push('^FO460,180^FDESTRADA VELASQUEZ^FS\n')
                result.push('^XZ\n');

                quantity -= this.form.package_quantity
            }
            return result;
        },

        startConnection(config) {
            if (!qz.websocket.isActive()) {
                qz.websocket.connect(config);
            }
        },

        printFile(printer) {
            const config = this.getUpdatedConfig();

            this.setPrinter(printer);
            const data = this.generateDataLabel();

            qz.print(config, data).then(resp => {
                this.$swal({
                    icon: 'success',
                    title: 'Exito',
                    text: 'Imprimiendo etiquetas'
                })
                console.log(resp.data)
            }).catch(err => {
                console.log(err)
            });

            this.$inertia.visit(route('label.index'));
        },

        getUpdatedConfig() {
            if (this.cfg == null) {
                this.cfg = qz.configs.create(null);
            }
            this.updateConfig();
            return this.cfg
        },

        updateConfig() {
            const copies = 1;
            this.cfg.reconfigure({
                copies: copies
            });
        },

        setPrinter(printer) {
            const cf = this.getUpdatedConfig();
            cf.setPrinter(printer);

            if (typeof printer === 'object' && printer.name === undefined) {
                let shown;
                if (printer.file !== undefined) {
                    shown = "<em>FILE:</em> " + printer.file;
                }
                if (printer.host !== undefined) {
                    shown = "<em>HOST:</em> " + printer.host + ":" + printer.port;
                }

                this.$swal({
                    icon: 'success',
                    title: 'Imprimiendo…',
                    html: shown
                })

            } else {
                if (printer.name !== undefined) {
                    printer = printer.name;
                }

                if (printer === undefined) {
                    printer = 'NONE';
                }
                this.$swal({
                    icon: 'success',
                    title: 'test',
                    html: printer
                })
            }
        }
    },

    mounted() {
        this.startConnection();
    },

    watch: {
        form: {
            immediate: true,
            deep: true,
            handler: function () {
                this.updateBarcode()
                this.calculate_package_quantity()
            }
        },
    },

    computed: {
        current_date(){
            return dayjs().format('DD/MM/YYYY')
        }
    }
}
</script>
