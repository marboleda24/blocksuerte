<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            ['id' => 1,  'name' => 'Home'],
            ['id' => 2,  'name' => 'Blog'],
            ['id' => 3,  'name' => 'Accesos Remotos'],
            ['id' => 4,  'name' => 'Mesa de Ayuda'],
            ['id' => 5,  'name' => 'Aplicaciones - Editor NF'],
            ['id' => 6,  'name' => 'Aplicaciones - Artes'],
            ['id' => 7,  'name' => 'Aplicaciones - Facturación Electronica'],
            ['id' => 8,  'name' => 'Aplicaciones - Productos'],
            ['id' => 9,  'name' => 'Aplicaciones - Pedidos'],
            ['id' => 10, 'name' => 'Aplicaciones - Terceros - Clientes'],
            ['id' => 11, 'name' => 'Aplicaciones - Terceros - Recibos de Caja'],
            ['id' => 12, 'name' => 'Aplicaciones - Terceros - Anticipos'],
            ['id' => 13, 'name' => 'Aplicaciones - Terceros - Pronosticos'],
            ['id' => 14, 'name' => 'Aplicaciones - Gestión Ambiental'],
            ['id' => 15, 'name' => 'Aplicaciones - Mantenimiento'],
            ['id' => 16, 'name' => 'Otras Aplicaciones - Registro Materia Prima'],
            ['id' => 17, 'name' => 'Administración'],
        ];

        $permissions_home = [
            [
                'group_id' => 1,
                'name' => 'dashboard.view',
                'description' => 'Dashboard',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_blog = [
            [
                'group_id' => 2,
                'name' => 'blog.post.create',
                'description' => 'Post - Crear',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.post.edit',
                'description' => 'Post - Editar',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.post.delete',
                'description' => 'Post - Eliminar',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.post.view',
                'description' => 'Post - Ver',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.tag.create',
                'description' => 'Tag - Crear',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.tag.edit',
                'description' => 'Tag - Editar',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.tag.delete',
                'description' => 'Tag - Eliminar',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.tag.view',
                'description' => 'Tag - Ver',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.category.create',
                'description' => 'Categoría - Crear',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.category.edit',
                'description' => 'Categoría - Editar',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.category.delete',
                'description' => 'Categoría - Eliminar',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 2,
                'name' => 'blog.category.view',
                'description' => 'Categoría - Ver',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_remote_access = [
            [
                'group_id' => 3,
                'name' => 'remote-access',
                'description' => 'Accesos Remotos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_help_desk = [
            [
                'group_id' => 4,
                'name' => 'help-desk.requirement-admin',
                'description' => 'Requerimientos Admon',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_applications = [
            [
                'group_id' => 5,
                'name' => 'applications.editor-nf',
                'description' => 'Editor NF',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],

            [
                'group_id' => 6,
                'name' => 'applications.arts',
                'description' => 'Artes',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],

            [
                'group_id' => 7,
                'name' => 'application.electronic-billing.national.invoices',
                'description' => 'Nacionales - Facturas',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],

            [
                'group_id' => 7,
                'name' => 'application.electronic-billing.national.credit-notes',
                'description' => 'Nacionales - Notas Crédito',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],

            [
                'group_id' => 7,
                'name' => 'application.electronic-billing.exports.invoices',
                'description' => 'Exportación - Facturas',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 7,
                'name' => 'application.electronic-billing.exports.credit-notes',
                'description' => 'Exportación - Notas Crédito',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 7,
                'name' => 'application.electronic-billing.management',
                'description' => 'Administración',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 7,
                'name' => 'application.electronic-billing.settings',
                'description' => 'Configuración',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_products = [
            [
                'group_id' => 8,
                'name' => 'application.encoder.codes',
                'description' => 'Codificador',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 8,
                'name' => 'application.encoder.cloner',
                'description' => 'Clonador',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 8,
                'name' => 'application.encoder.master.products-types',
                'description' => 'Maestros - Tipo de Productos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 8,
                'name' => 'application.encoder.master.lines',
                'description' => 'Maestros - Lineas',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 8,
                'name' => 'application.encoder.master.sublines',
                'description' => 'Maestros - Sublineas',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 8,
                'name' => 'application.encoder.master.features',
                'description' => 'Maestros - Caracteristicas',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 8,
                'name' => 'application.encoder.master.materials',
                'description' => 'Maestros - Materiales',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 8,
                'name' => 'application.encoder.master.measurements',
                'description' => 'Maestros - Medidas',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 8,
                'name' => 'application.encoder.master.galvanic-finishes',
                'description' => 'Maestros - Acabados Galvanicos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 8,
                'name' => 'application.encoder.master.decorative-options',
                'description' => 'Maestros - Opciones Decorativas',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_orders = [
            [
                'group_id' => 9,
                'name' => 'application.orders.create',
                'description' => 'Crear Pedido',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 9,
                'name' => 'application.orders',
                'description' => 'Mis Pedidos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 9,
                'name' => 'application.orders.wallet',
                'description' => 'Gestion Cartera',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 9,
                'name' => 'application.orders.costs',
                'description' => 'Gestion Costos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 9,
                'name' => 'application.orders.production',
                'description' => 'Gestion Produccion',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 9,
                'name' => 'application.orders.cellar',
                'description' => 'Gestion Bodega',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 9,
                'name' => 'application.orders.dies',
                'description' => 'Gestion Troqueles',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 9,
                'name' => 'application.orders.customer-product-prices',
                'description' => 'Precios Clientes',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_third_parties_customer = [
            [
                'group_id' => 10,
                'name' => 'application.third-parties.customers',
                'description' => 'Clientes',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 10,
                'name' => 'application.third-parties.customers.create',
                'description' => 'Crear Cliente',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 10,
                'name' => 'application.third-parties.customers.credit-limit-gestione',
                'description' => 'Asignacion de Cupos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_third_parties_cash_register_receipts = [
            [
                'group_id' => 11,
                'name' => 'application.third-parties.cash-register-receipts',
                'description' => 'Recibos de Caja',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 11,
                'name' => 'application.third-parties.cash-register-receipts.create',
                'description' => 'Crear Recibo de Caja',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_third_parties_advances = [
            [
                'group_id' => 12,
                'name' => 'application.third-parties.advances',
                'description' => 'Anticipos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 12,
                'name' => 'application.third-parties.advances.create',
                'description' => 'Crear Anticipo',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 12,
                'name' => 'application.third-parties.management-advances-receipt',
                'description' => 'Gestion Anticipos - Recibos de Caja',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_third_parties_forecast = [
            [
                'group_id' => 13,
                'name' => 'application.third-parties.forecasts',
                'description' => 'Gestion de Pronosticos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_applications_environmental_management = [
            [
                'group_id' => 14,
                'name' => 'application.environmental-management.chimney-gas',
                'description' => 'Chimenea y Gas',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 14,
                'name' => 'application.environmental-management.machines',
                'description' => 'Maquinas',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 14,
                'name' => 'application.environmental-management.binnacle-omff.registry.p0xx',
                'description' => 'Bitacora OMFF - Registro P0XX',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 14,
                'name' => 'application.environmental-management.binnacle-omff.registry.hl1',
                'description' => 'Bitacora OMFF - Registro HL1',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 14,
                'name' => 'application.environmental-management.binnacle-omff.management',
                'description' => 'Bitacora OMFF - Gestion',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_applications_maintenance = [
            [
                'group_id' => 15,
                'name' => 'application.maintenance.create',
                'description' => 'Solicitar Mantenimiento',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 15,
                'name' => 'application.maintenance.my-requests',
                'description' => 'Mis solicitudes',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 15,
                'name' => 'application.maintenance.work-orders',
                'description' => 'Ordenes de Trabajo',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 15,
                'name' => 'application.maintenance.masters.assets',
                'description' => 'Maestros - Activos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 15,
                'name' => 'application.maintenance.masters.asset-classifications',
                'description' => 'Maestros - Clasificacion de Activos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 15,
                'name' => 'application.maintenance.masters.work-centers',
                'description' => 'Maestros - Centros de Trabajo',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $permissions_applications_other = [
            [
                'group_id' => 16,
                'name' => 'other-applications.raw-material',
                'description' => 'Registro Materia Prima',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        $admin = [
            [
                'group_id' => 17,
                'name' => 'administration.users',
                'description' => 'Usuarios',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 17,
                'name' => 'administration.roles.view',
                'description' => 'Roles',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 17,
                'name' => 'administration.roles.create',
                'description' => 'Crear Rol',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 17,
                'name' => 'administration.permissions.view',
                'description' => 'Permisos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 17,
                'name' => 'administration.permissions.create',
                'description' => 'Crear Permiso',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 17,
                'name' => 'administration.permissions-group.view',
                'description' => 'Grupo de Permisos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 17,
                'name' => 'administration.permissions-group.create',
                'description' => 'Crear Grupo de Permisos',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
            [
                'group_id' => 17,
                'name' => 'administration.backups',
                'description' => 'Backups',
                'guard_name' => 'sanctum',
                'protected' => 0,
            ],
        ];

        PermissionGroup::insert($groups);

        Permission::insert($permissions_home);
        Permission::insert($permissions_blog);
        Permission::insert($permissions_remote_access);
        Permission::insert($permissions_help_desk);
        Permission::insert($permissions_applications);
        Permission::insert($permissions_products);
        Permission::insert($permissions_orders);
        Permission::insert($permissions_third_parties_customer);
        Permission::insert($permissions_third_parties_cash_register_receipts);
        Permission::insert($permissions_third_parties_advances);
        Permission::insert($permissions_third_parties_forecast);
        Permission::insert($permissions_applications_environmental_management);
        Permission::insert($permissions_applications_maintenance);
        Permission::insert($permissions_applications_other);
        Permission::insert($admin);
    }
}
