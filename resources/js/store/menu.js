import {defineStore} from "pinia";

export const useTopMenuStore = defineStore("topMenu", {
    state: () => ({
        menu: [
            {
                icon: "home",
                pageName: "/dashboard",
                title: "Home",
                permission: ['dashboard.view']
            },
            {
                icon: "cloud-download-alt",
                pageName: "",
                title: "Documentos Electrónicos",
                permission: [
                    'electronic-billing.documents',
                    'goja.electronic-billing.documents'
                ],
                subMenu: [
                    {
                        icon: "",
                        pageName: "/electronic-billing/CIEV/documents",
                        title: "Estrada Velasquez",
                        permission: ['electronic-billing.documents'],
                    },
                    {
                        icon: "",
                        pageName: "/electronic-billing/GOJA/documents",
                        title: "Plásticos Goja",
                        permission: ['goja.electronic-billing.documents'],
                    },
                ]
            },
            {
                icon: "paperclip",
                pageName: "/remote_access",
                title: "Accesos remotos",
                permission: ['remote-access']
            },
            {
                icon: "cube",
                pageName: "MAX",
                title: "MAX",
                permission: [
                    'production-sheet',
                    'max.lot-change',
                    'max-update.inventory-issue-chemical',
                    'max-update.purchase-orders',
                    'max-update.post-operation-completion',
                    'update-structure',
                    'reports',
                    'packing-order-exportation.index',
                    'packing-order-exportation.pack-list',
                    'packing-order-exportation.list',
                    'other-applications.production-order',
                    'op-ov-relationship',
                    'queries.production-order-status'
                ],
                subMenu: [
                    {
                        icon: "",
                        pageName: "/queries/production-order-status",
                        title: "Estado OP",
                        permission: ['queries.production-order-status']
                    },
                    {
                        icon: "",
                        pageName: "/op-ov-relationship",
                        title: "Relacion OP - OV",
                        permission: ['op-ov-relationship'],
                    },
                    {
                        icon: "search",
                        pageName: "/production-order",
                        title: "Consulta OP",
                        permission: ['other-applications.production-order']
                    },
                    {
                        icon: "",
                        pageName: "/production-sheet",
                        title: "Hoja de produccion",
                        permission: ['production-sheet'],
                    },
                    {
                        icon: "",
                        pageName: "/reports",
                        title: "Reportes",
                        permission: ['reports'],
                    },
                    {
                        icon: "",
                        pageName: "/update-structure",
                        title: "Edicion de estructuras",
                        permission: ['update-structure'],
                    },
                    {
                        icon: "",
                        pageName: "/reason-validation",
                        title: "Validacion de razon",
                        permission: ['reason-validation'],
                    },
                    {
                        icon: "",
                        pageName: "/max-update/lot-change",
                        title: "Modificación de lote",
                        permission: ['max.lot-change'],
                    },
                    {
                        icon: "flask",
                        pageName: "/max-update/inventory-issue",
                        title: "Salida productos químicos",
                        permission: ['max-update.inventory-issue-chemical'],
                    },
                    {
                        icon: "stream",
                        pageName: "/max-update/purchase-orders",
                        title: "Ingreso OC",
                        permission: ['max-update.purchase-orders'],
                    },
                    {
                        icon: "stream",
                        pageName: "/max-update/post-operation-completion",
                        title: "Conclusion de operación",
                        permission: ['max-update.post-operation-completion'],
                    },

                    {
                        icon: "",
                        pageName: "Empaque exportaciones",
                        title: "Empaque exportaciones",
                        permission: [
                            'packing-order-exportation.index',
                            'packing-order-exportation.pack-list',
                            'packing-order-exportation.list'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/packing-order-exportation",
                                title: "Lista de ordenes",
                                permission: ['packing-order-exportation.index'],
                            },
                            {
                                icon: "",
                                pageName: "/packing-order-exportation/pack-list",
                                title: "Empaque",
                                permission: ['packing-order-exportation.pack-list'],
                            },
                            {
                                icon: "",
                                pageName: "/packing-order-exportation/list",
                                title: "Listas generadas",
                                permission: ['packing-order-exportation.list'],
                            },
                        ]
                    }
                ]
            },
            {
                icon: "th",
                pageName: "Aplicaciones",
                title: "Aplicaciones",
                permission: [
                    'application.editor-nf',
                    'application.arts',
                    'application.electronic-billing.national.invoices',
                    'application.electronic-billing.national.credit-notes',
                    'application.electronic-billing.national.debit-notes',
                    'application.electronic-billing.exports.invoices',
                    'application.electronic-billing.exports.credit-notes',

                    'application.encoder.codes',
                    'application.encoder.cloner',
                    'application.encoder.master.products-types',
                    'application.encoder.master.lines',
                    'application.encoder.master.sublines',
                    'application.encoder.master.features',
                    'application.encoder.master.materials',
                    'application.encoder.master.measurements',
                    'application.encoder.master.galvanic-finishes',
                    'application.encoder.master.decorative-options',
                    'application.inventory',

                    'application.orders.create',
                    'application.orders',
                    'application.orders.wallet',
                    'application.orders.costs',
                    'application.orders.production',
                    'application.orders.cellar',
                    'application.orders.dies',
                    'application.orders.customer-product-prices',
                    'application.third-parties.forecasts',

                    'application.third-parties.customers',
                    'application.third-parties.customers.create',
                    'application.third-parties.customers.credit-limit-gestione',
                    'application.third-parties.account-status',
                    'application.third-parties.cash-register-receipts',
                    'application.third-parties.cash-register-receipts.create',
                    'application.third-parties.advances',
                    'application.third-parties.advances.create',
                    'application.third-parties.management-advances-receipt',
                    'application.third-parties.forecasts',

                    'application.environmental-management.chimney-gas',
                    'application.environmental-management.machines',
                    'application.environmental-management.binnacle-omff.registry.p0xx',
                    'application.environmental-management.binnacle-omff.registry.hl1',
                    'application.environmental-management.binnacle-omff.management',

                    'application.maintenance.create',
                    'application.maintenance.my-requests',
                    'application.maintenance.work-orders',
                    'application.maintenance.masters.assets',
                    'application.maintenance.masters.asset-classifications',
                    'application.maintenance.masters.work-centers',
                    'application.maintenance.report',

                    'application.replace-data',

                    'design-requirements.create',
                    'design-requirements.list',
                    'design-requirements.proposals.gestion-3d',
                    'design-requirements.management-blueprints',
                    'blueprints',
                    'design-requirements.arts',

                    'supplier-purchases.index',
                    'supplier-purchases.work-center',
                    'supplier-purchases.audit',
                    'price-per-customer'
                ],
                subMenu: [
                    {
                        icon: "file-contract",
                        pageName: "/editor-notes-invoices",
                        title: "Editor NF",
                        permission: ['application.editor-nf'],
                    },
                    {
                        icon: "palette",
                        pageName: "/arts",
                        title: "Artes",
                        permission: ['application.arts'],
                    },
                    {
                        icon: "pen-ruler",
                        pageName: "Requerimientos",
                        title: "Requerimientos",
                        permission: [
                            'design-requirements.create',
                            'design-requirements.list',
                            'design-requirements.proposals.gestion-3d',
                            'design-requirements.management-blueprints',
                            'blueprints',
                            'design-requirements.arts'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/design-requirements/create",
                                title: "Nuevo",
                                permission: ['design-requirements.create'],
                            },
                            {
                                icon: "",
                                pageName: "/design-requirements",
                                title: "Requerimientos",
                                permission: ['design-requirements.list'],
                            },
                            {
                                icon: "",
                                pageName: "/design-requirements/proposals/gestion-3d",
                                title: "Pendientes 3D",
                                permission: ['design-requirements.proposals.gestion-3d'],
                            },
                            {
                                icon: "",
                                pageName: "/design-requirements/blueprint-management",
                                title: "Pendientes Planos",
                                permission: ['design-requirements.management-blueprints'],
                            },
                            {
                                icon: "",
                                pageName: "/blueprints",
                                title: "Planos",
                                permission: ['blueprints'],
                            },
                            {
                                icon: "",
                                pageName: "/design-requirements/arts",
                                title: "Artes",
                                permission: ['design-requirements.arts'],
                            },
                        ]
                    },
                    {
                        icon: "file-invoice-dollar",
                        pageName: "Facturación electronica",
                        title: "Facturación electronica",
                        permission: [
                            'application.electronic-billing.national.invoices',
                            'application.electronic-billing.national.credit-notes',
                            'application.electronic-billing.national.debit-notes',
                            'application.electronic-billing.exports.invoices',
                            'application.electronic-billing.exports.credit-notes',
                            'supplier-purchases.index',
                            'supplier-purchases.work-center',
                            'supplier-purchases.audit',
                            'application.electronic-billing.support-document'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "Nacionales",
                                title: "Nacionales",
                                permission: [
                                    'application.electronic-billing.national.invoices',
                                    'application.electronic-billing.national.credit-notes',
                                    'application.electronic-billing.national.debit-notes'
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/electronic-billing/CIEV/national/invoices",
                                        title: "Facturas",
                                        permission: ['application.electronic-billing.national.invoices'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/electronic-billing/CIEV/national/credit-notes",
                                        title: "Notas Crédito",
                                        permission: ['application.electronic-billing.national.credit-notes'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/electronic-billing/CIEV/national/debit-notes",
                                        title: "Notas Debito",
                                        permission: ['application.electronic-billing.national.debit-notes'],
                                    }
                                ]
                            },
                            {
                                icon: "",
                                pageName: "Exportaciones",
                                title: "Exportaciones",
                                permission: [
                                    'application.electronic-billing.exports.invoices',
                                    'application.electronic-billing.exports.credit-notes',
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/electronic-billing/CIEV/exports/invoices",
                                        title: "Facturas",
                                        permission: ['application.electronic-billing.exports.invoices']
                                    },
                                    {
                                        icon: "",
                                        pageName: "/electronic-billing/CIEV/exports/credit-notes",
                                        title: "Notas Crédito",
                                        permission: ['application.electronic-billing.exports.credit-notes']
                                    }
                                ]
                            },
                            {
                                icon: "",
                                pageName: "Documentos proveedores",
                                title: "Documentos proveedores",
                                permission: [
                                    'supplier-purchases.index',
                                    'supplier-purchases.work-center',
                                    'supplier-purchases.audit'
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/supplier-purchases",
                                        title: "Compras",
                                        permission: [
                                            'supplier-purchases.index'
                                        ]
                                    },
                                    {
                                        icon: "",
                                        pageName: "/supplier-purchases/work-center",
                                        title: "Gestión por area",
                                        permission: [
                                            'supplier-purchases.work-center'
                                        ]
                                    },
                                    {
                                        icon: "",
                                        pageName: "/supplier-purchases/audit",
                                        title: "Auditoria",
                                        permission: [
                                            'supplier-purchases.audit'
                                        ]
                                    },

                                ]
                            },
                            {
                                icon: "",
                                pageName: "/support-document/CIEV",
                                title: "Documento Soporte",
                                permission: [
                                    'application.electronic-billing.support-document'
                                ]
                            }
                        ]
                    },
                    {
                        icon: "box",
                        pageName: "Productos",
                        title: "Productos",
                        permission: [
                            'application.encoder.codes',
                            'application.encoder.cloner',
                            'application.encoder.master.products-types',
                            'application.encoder.master.lines',
                            'application.encoder.master.sublines',
                            'application.encoder.master.features',
                            'application.encoder.master.materials',
                            'application.encoder.master.measurements',
                            'application.encoder.master.galvanic-finishes',
                            'application.encoder.master.decorative-options',
                            'encoder.products',
                            'application.inventory',
                            'quality-control',
                            'application.replace-data'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/encoder/codes",
                                title: "Codificador",
                                permission: ['application.encoder.codes'],
                            },
                            {
                                icon: "",
                                pageName: "/cloner",
                                title: "Clonador",
                                permission: ['application.encoder.cloner'],
                            },
                            {
                                icon: "",
                                pageName: "Maestros",
                                title: "Maestros",
                                permission: [
                                    'application.encoder.master.products-types',
                                    'application.encoder.master.lines',
                                    'application.encoder.master.sublines',
                                    'application.encoder.master.features',
                                    'application.encoder.master.materials',
                                    'application.encoder.master.measurements',
                                    'application.encoder.master.galvanic-finishes',
                                    'application.encoder.master.decorative-options',
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/encoder/masters/products-types",
                                        title: "Tipos de producto",
                                        permission: ['application.encoder.master.products-types'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/encoder/masters/lines",
                                        title: "Lineas",
                                        permission: ['application.encoder.master.lines'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/encoder/masters/sublines",
                                        title: "Sublineas",
                                        permission: ['application.encoder.master.sublines'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/encoder/masters/features",
                                        title: "Características",
                                        permission: ['application.encoder.master.features'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/encoder/masters/materials",
                                        title: "Materiales",
                                        permission: ['application.encoder.master.materials'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/encoder/masters/measurements",
                                        title: "Medidas",
                                        permission: ['application.encoder.master.measurements'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/encoder/masters/galvanic-finishes",
                                        title: "Acabados galvánicos",
                                        permission: ['application.encoder.master.galvanic-finishes'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/encoder/masters/decorative-options",
                                        title: "Opciones decorativas",
                                        permission: ['application.encoder.master.decorative-options'],
                                    }
                                ]
                            },
                            {
                                icon: "",
                                pageName: "/encoder/products",
                                title: "Productos Nuevos - Viejos",
                                permission: ['encoder.products']
                            },
                            {
                                icon: "",
                                pageName: "/third-parties/inventory",
                                title: "Inventarios",
                                permission: ['application.inventory']
                            },
                            {
                                icon: "",
                                pageName: "/quality-control",
                                title: "Calidad - Revision OP",
                                permission: ['quality-control']
                            },
                            {
                                icon: "",
                                pageName: "/replace-data",
                                title: "Actualización Descripcion",
                                permission: ['application.replace-data']
                            }
                        ]
                    },
                    {
                        icon: "shopping-cart",
                        pageName: "Pedidos",
                        title: "Pedidos",
                        permission: [
                            'application.orders.create',
                            'application.orders',
                            'application.orders.wallet',
                            'application.orders.costs',
                            'application.orders.production',
                            'application.orders.cellar',
                            'application.orders.dies',
                            'application.orders.customer-product-prices',
                            'application.third-parties.forecasts'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/orders/create",
                                title: "Crear pedido",
                                permission: ['application.orders.create']
                            },
                            {
                                icon: "",
                                pageName: "/orders",
                                title: "Mis pedidos",
                                permission: ['application.orders']
                            },
                            {
                                icon: "",
                                pageName: "/orders/wallet",
                                title: "Cartera",
                                permission: ['application.orders.wallet']
                            },
                            {
                                icon: "",
                                pageName: "/orders/costs",
                                title: "Costos",
                                permission: ['application.orders.costs']
                            },
                            {
                                icon: "",
                                pageName: "/orders/production",
                                title: "Producción",
                                permission: ['application.orders.production']
                            },
                            {
                                icon: "",
                                pageName: "/orders/cellar",
                                title: "Bodega",
                                permission: ['application.orders.cellar']
                            },
                            {
                                icon: "",
                                pageName: "/orders/dies",
                                title: "Troqueles",
                                permission: ['application.orders.dies']
                            },
                            {
                                icon: "",
                                pageName: "/orders/customer-product-prices",
                                title: "Precios Clientes",
                                permission: ['application.orders.customer-product-prices']
                            },
                            {
                                icon: "umbrella",
                                pageName: "/third-parties/forecasts",
                                title: "Pronósticos",
                                permission: ['application.third-parties.forecasts']
                            },
                        ]
                    },
                    {
                        icon: "address-book",
                        pageName: "Terceros",
                        title: "Terceros",
                        permission: [
                            'application.third-parties.customers',
                            'application.third-parties.customers.create',
                            'application.third-parties.customers.credit-limit-gestione',
                            'application.third-parties.account-status',
                            'application.third-parties.cash-register-receipts',
                            'application.third-parties.cash-register-receipts.create',
                            'application.third-parties.advances',
                            'application.third-parties.advances.create',
                            'application.third-parties.management-advances-receipt'
                        ],
                        subMenu: [
                            {
                                icon: "user-shield",
                                pageName: "/third-parties/customers",
                                title: "Clientes",
                                permission: [
                                    'application.third-parties.customers',
                                    'application.third-parties.customers.create',
                                    'application.third-parties.customers.credit-limit-gestione',
                                    'application.third-parties.account-status'
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/third-parties/cash-register-receipts/account-status",
                                        title: "Estado de cuenta",
                                        permission: ['application.third-parties.account-status']

                                    },
                                    {
                                        icon: "user-shield",
                                        pageName: "/third-parties/customers",
                                        title: "Todos los clientes",
                                        permission: ['application.third-parties.customers']
                                    },
                                    {
                                        icon: "user-plus",
                                        pageName: "/third-parties/customers/create",
                                        title: "Crear cliente",
                                        permission: ['application.third-parties.customers.create']

                                    },
                                    {
                                        icon: "user-check",
                                        pageName: "/third-parties/customers-credit-limit-gestion",
                                        title: "Asignación de cupos",
                                        permission: ['application.third-parties.customers.credit-limit-gestione']

                                    }
                                ]
                            },
                            {
                                icon: "money-check",
                                pageName: "Recibos de caja",
                                title: "Recibos de caja",
                                permission: [
                                    'application.third-parties.cash-register-receipts',
                                    'application.third-parties.cash-register-receipts.create',
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/third-parties/cash-register-receipts",
                                        title: "Recibos de caja",
                                        permission: ['application.third-parties.cash-register-receipts']

                                    },
                                    {
                                        icon: "",
                                        pageName: "/third-parties/cash-register-receipts/create",
                                        title: "Nuevo recibo de caja",
                                        permission: ['application.third-parties.cash-register-receipts.create']
                                    }
                                ]
                            },
                            {
                                icon: "money-check",
                                pageName: "Anticipos",
                                title: "Anticipos",
                                permission: [
                                    'application.third-parties.advances',
                                    'application.third-parties.advances.create'
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/third-parties/advances",
                                        title: "Anticipos",
                                        permission: ['application.third-parties.advances']
                                    },
                                    {
                                        icon: "",
                                        pageName: "/third-parties/advances/create",
                                        title: "Nuevo anticipo",
                                        permission: ['application.third-parties.advances.create']
                                    }
                                ]
                            },
                            {
                                icon: "tasks",
                                pageName: "/third-parties/management-advances-receipt",
                                title: "Gestión RC y Anticipos",
                                permission: ['application.third-parties.management-advances-receipt']
                            }

                        ]
                    },
                    {
                        icon: "tree",
                        pageName: "Gestión ambiental",
                        title: "Gestión ambiental",
                        permission: [
                            'application.environmental-management.chimney-gas',
                            'application.environmental-management.machines',
                            'application.environmental-management.binnacle-omff.registry.p0xx',
                            'application.environmental-management.binnacle-omff.registry.hl1',
                            'application.environmental-management.binnacle-omff.management'
                        ],
                        subMenu: [
                            {
                                icon: "smog",
                                pageName: "/environmental-management/chimney-gas",
                                title: "Chimenea y gas",
                                permission: ['application.environmental-management.chimney-gas']
                            },
                            {
                                icon: "folder-open",
                                pageName: "/environmental-management/machines",
                                title: "Maquinas",
                                permission: ['application.environmental-management.machines']
                            },
                            {
                                icon: "file-signature",
                                pageName: "Bitácora OMFF",
                                title: "Bitácora OMFF", //
                                permission: [
                                    'application.environmental-management.binnacle-omff.registry.p0xx',
                                    'application.environmental-management.binnacle-omff.registry.hl1',
                                    'application.environmental-management.binnacle-omff.management'
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/environmental-management/binnacle-omff/registry/p0xx",
                                        title: "Registro P0XX",
                                        permission: ['application.environmental-management.binnacle-omff.registry.p0xx']
                                    },
                                    {
                                        icon: "",
                                        pageName: "/environmental-management/binnacle-omff/registry/hl1",
                                        title: "Registro HL1",
                                        permission: ['application.environmental-management.binnacle-omff.registry.hl1']
                                    },
                                    {
                                        icon: "",
                                        pageName: "/environmental-management/binnacle-omff/management",
                                        title: "Gestión",
                                        permission: ['application.environmental-management.binnacle-omff.management']
                                    }
                                ]
                            },
                        ]
                    },
                    {
                        icon: "wrench",
                        pageName: "Mantenimiento",
                        title: "Mantenimiento",
                        permission: [
                            'application.maintenance.create',
                            'application.maintenance.my-requests',
                            'application.maintenance.work-orders',
                            'application.maintenance.masters.assets',
                            'application.maintenance.masters.asset-classifications',
                            'application.maintenance.masters.work-centers',
                            'application.maintenance.report',
                            'application.maintenance.schedule.preventive'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/maintenance/create",
                                title: "Solicitar mantenimiento",
                                permission: ['application.maintenance.create']
                            },
                            {
                                icon: "",
                                pageName: "/maintenance/my-requests",
                                title: "Mis solicitudes",
                                permission: ['application.maintenance.my-requests']
                            },
                            {
                                icon: "",
                                pageName: "/maintenance/work-orders",
                                title: "Órdenes de trabajo",
                                permission: ['application.maintenance.work-orders']
                            },
                            {
                                icon: "wrench",
                                pageName: "Maestros",
                                title: "Maestros",
                                permission: [
                                    'application.maintenance.masters.assets',
                                    'application.maintenance.masters.asset-classifications',
                                    'application.maintenance.masters.work-centers'
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/maintenance/assets",
                                        title: "Activos",
                                        permission: ['application.maintenance.masters.assets']
                                    },
                                    {
                                        icon: "",
                                        pageName: "/maintenance/asset-classifications",
                                        title: "Clasificación de activos",
                                        permission: ['application.maintenance.masters.asset-classifications']
                                    },
                                    {
                                        icon: "",
                                        pageName: "/maintenance/work-centers",
                                        title: "Centros de trabajo",
                                        permission: ['application.maintenance.masters.work-centers']
                                    },
                                ]
                            },
                            {
                                icon: "",
                                pageName: "/maintenance/report",
                                title: "Indicadores de calidad",
                                permission: ['application.maintenance.report']
                            },
                            {
                                icon: "",
                                pageName: "/maintenance/schedule/preventive",
                                title: "Cronograma de preventivos",
                                permission: ['application.maintenance.schedule.preventive']
                            }
                        ]
                    }
                ]
            },
            {
                icon: "folder-open",
                pageName: "Otros Aplicativos",
                title: "Otros Aplicativos",
                permission: [
                    'other-applications.temperature-control',
                    'other-applications.raw-material',
                    'human-resource.gestion-working-letter',
                    'human-resource.proof-payment',
                    'human-resource.peace-safe',
                    'queries.invoices-per-seller',
                    'queries.open-sell-orders',
                    'queries.order-shipped-not-invoiced',
                    'queries.delivery-commitment',
                    'queries.customers',
                    'queries.billing-per-day',
                    'queries.replace-data',
                    'queries.customer-product-oc',
                    'human-resource.vacation-request',
                    'security-health-work.sociodemographic',
                    'security-health-work.reports',
                    'claims.index',
                    'claims.cost',
                    'claims.quality',
                    'claims.cellar',
                    'claims.wallet',
                    'application.remission',
                    'application.labels',
                    'galvano-bath-parameters.register',
                    'galvano-bath-parameters.index',
                    'price-per-customer'
                ],
                subMenu: [
                    /**{
                        icon: "heartbeat",
                        pageName: "/temperature-control",
                        title: "Ingreso de personal",
                        permission: ['other-applications.temperature-control']
                    },*/
                    {
                        icon: "box-open",
                        pageName: "/raw-material",
                        title: "Reg. Materia Prima",
                        permission: ['other-applications.raw-material']
                    },
                    {
                        icon: "user-check",
                        pageName: "Recursos Humanos",
                        title: "Recursos Humanos",
                        permission: [
                            'human-resource.gestion-working-letter',
                            'human-resource.proof-payment',
                            'human-resource.vacation-request',
                            'human-resource.peace-safe'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/human-resource/gestion-working-letter",
                                title: "Solicitudes Carta Laboral",
                                permission: ['human-resource.gestion-working-letter']
                            },
                            {
                                icon: "",
                                pageName: "/human-resource/proof-payment",
                                title: "Comprobantes Nomina",
                                permission: ['human-resource.proof-payment']
                            },
                            {
                                icon: "",
                                pageName: "/vacation-request-human-resource",
                                title: "Solicitud de Vacaciones",
                                permission: ['human-resource.vacation-request']
                            },
                            {
                                icon: "",
                                pageName: "/human-resource/peace-safe",
                                title: "Cartas Paz y Salvo",
                                permission: ['human-resource.peace-safe']
                            },
                            {
                                icon: "",
                                pageName: "/payroll",
                                title: "Nómina electronica",
                                permission: ['human-resource.payroll']
                            },
                        ]
                    },
                    {
                        icon: "user-check",
                        pageName: "SST",
                        title: "SST",
                        permission: [
                            'security-health-work.sociodemographic',
                            'security-health-work.reports'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/security-health-work/sociodemographic",
                                title: "Reporte socio-demográfico",
                                permission: ['security-health-work.sociodemographic']
                            },
                            {
                                icon: "",
                                pageName: "/security-health-work/reports",
                                title: "Reportes",
                                permission: ['security-health-work.reports']
                            },
                        ]
                    },
                    {
                        icon: "",
                        pageName: "Reclamos",
                        title: "Reclamos",
                        permission: [
                            'claims.create',
                            'claims.index',
                            'claims.cost',
                            'claims.quality',
                            'claims.cellar',
                            'claims.wallet',
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/claim/create",
                                title: "Nuevo reclamo",
                                permission: ['claims.create']
                            },
                            {
                                icon: "",
                                pageName: "/claim",
                                title: "Mis reclamos",
                                permission: ['claims.index']
                            },
                            {
                                icon: "",
                                pageName: "/claim/cost",
                                title: "Costos",
                                permission: ['claims.cost']
                            },
                            {
                                icon: "",
                                pageName: "/claim/quality",
                                title: "Calidad",
                                permission: ['claims.quality']
                            },
                            {
                                icon: "",
                                pageName: "/claim/cellar",
                                title: "Bodega",
                                permission: ['claims.cellar']
                            },
                            {
                                icon: "",
                                pageName: "/claim/wallet",
                                title: "Cartera",
                                permission: ['claims.wallet']
                            },
                        ]
                    },
                    {
                        icon: "file-invoice",
                        pageName: "/remission",
                        title: "Remisiones",
                        permission: ['application.remission']

                    },
                    {
                        icon: "",
                        pageName: "Consultas",
                        title: "Consultas",
                        permission: [
                            'queries.codes-list',
                            'queries.invoices-per-seller',
                            'queries.open-sell-orders',
                            'queries.close-sell-orders',
                            'queries.order-shipped-not-invoiced',
                            'queries.delivery-commitment',
                            'queries.customers',
                            'queries.billing-per-day',
                            'queries.replace-data',
                            'queries.customer-product-oc',
                            'price-per-customer'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/queries/codes-list",
                                title: "Productos codificados",
                                permission: ['queries.codes-list']
                            },
                            {
                                icon: "",
                                pageName: "/queries/invoices-per-seller",
                                title: "Facturación por vendedor",
                                permission: ['queries.invoices-per-seller']
                            },
                            {
                                icon: "",
                                pageName: "/queries/CIEV/open-sell-orders",
                                title: "OV Abiertas",
                                permission: ['queries.open-sell-orders']
                            },
                            {
                                icon: "",
                                pageName: "/queries/close-sell-order",
                                title: "OV Cerradas",
                                permission: ['queries.close-sell-orders']
                            },
                            {
                                icon: "",
                                pageName: "/queries/order-shipped-not-invoiced",
                                title: "OV enviadas no Fac",
                                permission: ['queries.order-shipped-not-invoiced']
                            },
                            {
                                icon: "",
                                pageName: "/queries/delivery-commitment",
                                title: "Comp. Entrega OV",
                                permission: ['queries.delivery-commitment']
                            },
                            {
                                icon: "",
                                pageName: "/queries/customers",
                                title: "Clientes",
                                permission: ['queries.customers']
                            },
                            {
                                icon: "",
                                pageName: "/queries/replace-data",
                                title: "Reemplazar Información",
                                permission: ['queries.replace-data']
                            },

                            {
                                icon: "",
                                pageName: "/queries/customer-product-oc",
                                title: "CPC - OC",
                                permission: ['queries.customer-product-oc']
                            },
                            {
                                icon: "",
                                pageName: "/price-per-customer",
                                title: "Precios por cliente",
                                permission: ['price-per-customer']
                            },

                        ]
                    },
                    {
                        icon: "tags",
                        pageName: "/labels",
                        title: "Etiquetado",
                        permission: ['application.labels']
                    },
                    {
                        icon: "",
                        pageName: "Param. Baños Galvano",
                        title: "Param. Baños Galvano",
                        permission: [
                            'galvano-bath-parameters.register',
                            'galvano-bath-parameters.index'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/galvano-bath-parameters/register",
                                title: "Registrar",
                                permission: ['galvano-bath-parameters.register']
                            },
                            {
                                icon: "",
                                pageName: "/galvano-bath-parameters",
                                title: "Listado",
                                permission: ['galvano-bath-parameters.index']
                            },
                        ]
                    },
                ]
            },

            {
                icon: "star",
                pageName: "Goja",
                title: "Goja",
                permission: [
                    'goja.application.electronic-billing.national.invoices',
                    'goja.application.electronic-billing.national.credit-notes',
                    'goja.application.electronic-billing.national.debit-notes',
                    'goja.application.electronic-billing.exports.invoices',
                    'goja.application.electronic-billing.exports.credit-notes',
                    'goja.human-resource.proof-payment',
                    'goja.payroll',
                    'goja.supplier-purchases.index',
                    'goja.application.support-document',
                    'goja.application.employee-documents',
                    "goja.reports.sale.per-day",
                    "goja.reports.sales-per-product",
                    "goja.queries.open-sell-orders",
                    "goja.import-document-max-dms"
                ],
                subMenu: [
                    {
                        icon: "file-invoice-dollar",
                        pageName: "Facturación electronica",
                        title: "Facturación electronica",
                        permission: [
                            'goja.application.electronic-billing.national.invoices',
                            'goja.application.electronic-billing.national.credit-notes',
                            'goja.application.electronic-billing.national.debit-notes',
                            'goja.application.electronic-billing.exports.invoices',
                            'goja.application.electronic-billing.exports.credit-notes',
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "Nacionales",
                                title: "Nacionales",
                                permission: [
                                    'goja.application.electronic-billing.national.invoices',
                                    'goja.application.electronic-billing.national.credit-notes',
                                    'goja.application.electronic-billing.national.debit-notes'
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/electronic-billing/GOJA/national/invoices",
                                        title: "Facturas",
                                        permission: ['goja.application.electronic-billing.national.invoices'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/electronic-billing/GOJA/national/credit-notes",
                                        title: "Notas Crédito",
                                        permission: ['goja.application.electronic-billing.national.credit-notes'],
                                    },
                                    {
                                        icon: "",
                                        pageName: "/electronic-billing/GOJA/national/debit-notes",
                                        title: "Notas Debito",
                                        permission: ['goja.application.electronic-billing.national.debit-notes'],
                                    }
                                ]
                            },
                            {
                                icon: "",
                                pageName: "Exportaciones",
                                title: "Exportaciones",
                                permission: [
                                    'goja.application.electronic-billing.exports.invoices',
                                    'goja.application.electronic-billing.exports.credit-notes',
                                ],
                                subMenu: [
                                    {
                                        icon: "",
                                        pageName: "/electronic-billing/GOJA/exports/invoices",
                                        title: "Facturas",
                                        permission: ['goja.application.electronic-billing.exports.invoices']
                                    },
                                    {
                                        icon: "",
                                        pageName: "/electronic-billing/GOJA/exports/credit-notes",
                                        title: "Notas Crédito",
                                        permission: ['goja.application.electronic-billing.exports.credit-notes']
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        icon: "",
                        pageName: "Reportes",
                        title: "Reportes ",
                        permission: [
                            "goja.reports.sale.per-day",
                            "goja.queries.open-sell-orders",
                            "goja.reports.sales-per-product"
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/reports/sales/GOJA/per-day",
                                title: "Facturación por día",
                                permission: ["goja.reports.sale.per-day"],
                            },
                            {
                                icon: "",
                                pageName: "/goja/reports/sales-per-product",
                                title: "Ventas por producto",
                                permission: ["goja.reports.sales-per-product"],
                            },
                            {
                                icon: "",
                                pageName: "/queries/GOJA/open-sell-orders",
                                title: "OV Abiertas",
                                permission: ['goja.queries.open-sell-orders']
                            },
                        ]
                    },
                    {
                        icon: "user-friends",
                        pageName: "Recursos Humanos",
                        title: "Recursos Humanos",
                        permission: [
                            'goja.human-resource.proof-payment',
                            'goja.payroll'
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/goja/human-resource/proof-payment",
                                title: "Comprobantes de nomina",
                                permission: ["goja.human-resource.proof-payment"],
                            },
                            {
                                icon: "",
                                pageName: "/goja/payroll",
                                title: "Nómina electronica",
                                permission: ["goja.payroll"],
                            },
                        ]
                    },
                    {
                        icon: "",
                        pageName: "/goja/supplier-purchases",
                        title: "Documentos proveedores",
                        permission: ["goja.supplier-purchases.index"]
                    },
                    {
                        icon: "",
                        pageName: "/support-document/GOJA",
                        title: "Documento Soporte",
                        permission: [
                            'goja.application.support-document'
                        ]
                    },
                    {
                        icon: "",
                        pageName: "/goja/employee-documents",
                        title: "Documento Empleados",
                        permission: [
                            'goja.application.employee-documents'
                        ]
                    },
                    {
                        icon: "file-import",
                        pageName: "/GOJA/import-document-max-dms",
                        title: "Importación MAX > DMS",
                        permission: ["goja.import-document-max-dms"],
                    },
                ]
            },

            {
                icon: "cogs",
                pageName: "Administración",
                title: "Administración",
                permission: [
                    'invoice-edition',
                    'import-document-max-dms',
                    'job-monitor',
                    'log-postmark',
                    'administration.users',
                    'administration.roles.create',
                    'administration.roles.view',
                    'administration.permissions.create',
                    'administration.permissions.view',
                    'administration.permissions-group.create',
                    'administration.permissions-group.view',
                    'administration.backups'
                ],
                subMenu: [
                    {
                        icon: "file-pen",
                        pageName: "/invoice-edition",
                        title: "Edicion de facturas",
                        permission: ["invoice-edition"],
                    },
                    {
                        icon: "file-import",
                        pageName: "/CIEV/import-document-max-dms",
                        title: "Importación MAX > DMS",
                        permission: ["import-document-max-dms"],
                    },
                    {
                        icon: "chart-pie",
                        pageName: "/custom/monitor",
                        title: "Monitor de jobs",
                        permission: ["job-monitor"],
                    },
                    {
                        icon: "file",
                        pageName: "/postmark",
                        title: "Log Postmark",
                        permission: ["log-postmark"],
                    },
                    {
                        icon: "users",
                        pageName: "/users",
                        title: "Usuarios",
                        permission: ["administration.users"],
                    },
                    {
                        icon: "user-lock",
                        pageName: "Roles",
                        title: "Roles",
                        permission: [
                            'administration.roles.create',
                            'administration.roles.view',
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/roles/create",
                                title: "Crear rol",
                                permission: ["administration.roles.create"],
                            },
                            {
                                icon: "",
                                pageName: "/roles",
                                title: "Mostrar roles",
                                permission: ["administration.roles.view"],
                            }
                        ]
                    },
                    {
                        icon: "fingerprint",
                        pageName: "Permisos",
                        title: "Permisos",
                        permission: [
                            'administration.permissions.create',
                            'administration.permissions.view',
                            'administration.permissions-group.create',
                            'administration.permissions-group.view',
                        ],
                        subMenu: [
                            {
                                icon: "",
                                pageName: "/permissions/create",
                                title: "Crear permiso",
                                permission: ["administration.permissions.create"],
                            },
                            {
                                icon: "",
                                pageName: "/permissions",
                                title: "Mostrar permisos",
                                permission: ["administration.permissions.view"],
                            },
                            {
                                icon: "",
                                pageName: "/permission-groups/create",
                                title: "Crear grupo de permisos",
                                permission: ["administration.permissions-group.create"],
                            },
                            {
                                icon: "",
                                pageName: "/permission-groups",
                                title: "Grupos de permisos",
                                permission: ["administration.permissions-group.view"],
                            }
                        ]
                    },
                    {
                        icon: 'database',
                        pageName: "/backup",
                        title: "Backups",
                        permission: ["administration.backups"],
                    }
                ]
            }
        ]
    })
})
