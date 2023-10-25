    var Ziggy = {
        namedRoutes: {"login":{"uri":"login","methods":["GET","HEAD"],"domain":null},"logout":{"uri":"logout","methods":["POST"],"domain":null},"password.request":{"uri":"forgot-password","methods":["GET","HEAD"],"domain":null},"password.reset":{"uri":"reset-password\/{token}","methods":["GET","HEAD"],"domain":null},"password.email":{"uri":"forgot-password","methods":["POST"],"domain":null},"password.update":{"uri":"reset-password","methods":["POST"],"domain":null},"user-profile-information.update":{"uri":"user\/profile-information","methods":["PUT"],"domain":null},"user-password.update":{"uri":"user\/password","methods":["PUT"],"domain":null},"password.confirm":{"uri":"user\/confirm-password","methods":["GET","HEAD"],"domain":null},"password.confirmation":{"uri":"user\/confirmed-password-status","methods":["GET","HEAD"],"domain":null},"two-factor.login":{"uri":"two-factor-challenge","methods":["GET","HEAD"],"domain":null},"profile.show":{"uri":"user\/profile","methods":["GET","HEAD"],"domain":null},"other-browser-sessions.destroy":{"uri":"user\/other-browser-sessions","methods":["DELETE"],"domain":null},"current-user-photo.destroy":{"uri":"user\/profile-photo","methods":["DELETE"],"domain":null},"api-tokens.index":{"uri":"user\/api-tokens","methods":["GET","HEAD"],"domain":null},"api-tokens.store":{"uri":"user\/api-tokens","methods":["POST"],"domain":null},"api-tokens.update":{"uri":"user\/api-tokens\/{token}","methods":["PUT"],"domain":null},"api-tokens.destroy":{"uri":"user\/api-tokens\/{token}","methods":["DELETE"],"domain":null},"blog":{"uri":"\/","methods":["GET","HEAD"],"domain":null},"post":{"uri":"post\/{post}","methods":["GET","HEAD"],"domain":null},"tag":{"uri":"post\/tag\/{tag}","methods":["GET","HEAD"],"domain":null},"category":{"uri":"post\/category\/{category}","methods":["GET","HEAD"],"domain":null},"change_theme":{"uri":"change_theme","methods":["POST"],"domain":null},"dashboard":{"uri":"dashboard","methods":["GET","HEAD"],"domain":null},"arts":{"uri":"arts","methods":["GET","HEAD"],"domain":null},"remote_access":{"uri":"remote_access","methods":["GET","HEAD"],"domain":null},"help_desk.admon_requirements":{"uri":"help_desk\/admon_requirements","methods":["GET","HEAD"],"domain":null},"electronic_billing.national.invoices":{"uri":"electronic_billing\/national\/invoices","methods":["GET","HEAD"],"domain":null},"electronic_billing.national.credit_notes":{"uri":"electronic_billing\/national\/credit_notes","methods":["GET","HEAD"],"domain":null},"electronic_billing.exports.invoices":{"uri":"electronic_billing\/exports\/invoices","methods":["GET","HEAD"],"domain":null},"electronic_billing.exports.credit_notes":{"uri":"electronic_billing\/exports\/credit_notes","methods":["GET","HEAD"],"domain":null},"electronic_billing.management":{"uri":"electronic_billing\/management","methods":["GET","HEAD"],"domain":null},"electronic_billing.settings":{"uri":"electronic_billing\/settings","methods":["GET","HEAD"],"domain":null},"settings.invoices.store":{"uri":"electronic_billing\/settings\/invoices","methods":["PUT"],"domain":null},"settings.credit_notes.store":{"uri":"electronic_billing\/settings\/credit_notes","methods":["PUT"],"domain":null},"codes.index":{"uri":"encoder\/codes","methods":["GET","HEAD"],"domain":null},"codes.store":{"uri":"encoder\/codes","methods":["POST"],"domain":null},"codes.update":{"uri":"encoder\/codes\/{code}","methods":["PUT","PATCH"],"domain":null},"codes.destroy":{"uri":"encoder\/codes\/{code}","methods":["DELETE"],"domain":null},"codes.get-lines":{"uri":"encoder\/codes\/get-lines","methods":["GET","HEAD"],"domain":null},"codes.get-sublines":{"uri":"encoder\/codes\/get-sublines","methods":["GET","HEAD"],"domain":null},"codes.get-other-inputs":{"uri":"encoder\/codes\/get-other-inputs","methods":["GET","HEAD"],"domain":null},"codes.get-list-codes":{"uri":"encoder\/codes\/get-list-codes","methods":["GET","HEAD"],"domain":null},"products-types.index":{"uri":"encoder\/masters\/products-types","methods":["GET","HEAD"],"domain":null},"products-types.create":{"uri":"encoder\/masters\/products-types\/create","methods":["GET","HEAD"],"domain":null},"products-types.store":{"uri":"encoder\/masters\/products-types","methods":["POST"],"domain":null},"products-types.show":{"uri":"encoder\/masters\/products-types\/{products_type}","methods":["GET","HEAD"],"domain":null},"products-types.edit":{"uri":"encoder\/masters\/products-types\/{products_type}\/edit","methods":["GET","HEAD"],"domain":null},"products-types.update":{"uri":"encoder\/masters\/products-types\/{products_type}","methods":["PUT","PATCH"],"domain":null},"products-types.destroy":{"uri":"encoder\/masters\/products-types\/{products_type}","methods":["DELETE"],"domain":null},"lines.index":{"uri":"encoder\/masters\/lines","methods":["GET","HEAD"],"domain":null},"lines.create":{"uri":"encoder\/masters\/lines\/create","methods":["GET","HEAD"],"domain":null},"lines.store":{"uri":"encoder\/masters\/lines","methods":["POST"],"domain":null},"lines.show":{"uri":"encoder\/masters\/lines\/{line}","methods":["GET","HEAD"],"domain":null},"lines.edit":{"uri":"encoder\/masters\/lines\/{line}\/edit","methods":["GET","HEAD"],"domain":null},"lines.update":{"uri":"encoder\/masters\/lines\/{line}","methods":["PUT","PATCH"],"domain":null},"lines.destroy":{"uri":"encoder\/masters\/lines\/{line}","methods":["DELETE"],"domain":null},"sublines.index":{"uri":"encoder\/masters\/sublines","methods":["GET","HEAD"],"domain":null},"sublines.create":{"uri":"encoder\/masters\/sublines\/create","methods":["GET","HEAD"],"domain":null},"sublines.store":{"uri":"encoder\/masters\/sublines","methods":["POST"],"domain":null},"sublines.show":{"uri":"encoder\/masters\/sublines\/{subline}","methods":["GET","HEAD"],"domain":null},"sublines.edit":{"uri":"encoder\/masters\/sublines\/{subline}\/edit","methods":["GET","HEAD"],"domain":null},"sublines.update":{"uri":"encoder\/masters\/sublines\/{subline}","methods":["PUT","PATCH"],"domain":null},"sublines.destroy":{"uri":"encoder\/masters\/sublines\/{subline}","methods":["DELETE"],"domain":null},"features.index":{"uri":"encoder\/masters\/features","methods":["GET","HEAD"],"domain":null},"features.store":{"uri":"encoder\/masters\/features","methods":["POST"],"domain":null},"features.update":{"uri":"encoder\/masters\/features\/{feature}","methods":["PUT","PATCH"],"domain":null},"features.destroy":{"uri":"encoder\/masters\/features\/{feature}","methods":["DELETE"],"domain":null},"features.get-sublines":{"uri":"encoder\/masters\/features\/get-sublines","methods":["GET","HEAD"],"domain":null},"features.get-latest-code":{"uri":"encoder\/masters\/features\/get-latest-code","methods":["GET","HEAD"],"domain":null},"materials.index":{"uri":"encoder\/masters\/materials","methods":["GET","HEAD"],"domain":null},"materials.create":{"uri":"encoder\/masters\/materials\/create","methods":["GET","HEAD"],"domain":null},"materials.store":{"uri":"encoder\/masters\/materials","methods":["POST"],"domain":null},"materials.show":{"uri":"encoder\/masters\/materials\/{material}","methods":["GET","HEAD"],"domain":null},"materials.edit":{"uri":"encoder\/masters\/materials\/{material}\/edit","methods":["GET","HEAD"],"domain":null},"materials.update":{"uri":"encoder\/masters\/materials\/{material}","methods":["PUT","PATCH"],"domain":null},"materials.destroy":{"uri":"encoder\/masters\/materials\/{material}","methods":["DELETE"],"domain":null},"measurements.index":{"uri":"encoder\/masters\/measurements","methods":["GET","HEAD"],"domain":null},"measurements.store":{"uri":"encoder\/masters\/measurements","methods":["POST"],"domain":null},"measurements.update":{"uri":"encoder\/masters\/measurements\/{measurement}","methods":["PUT","PATCH"],"domain":null},"measurements.destroy":{"uri":"encoder\/masters\/measurements\/{measurement}","methods":["DELETE"],"domain":null},"measurements.get-units-measurements":{"uri":"encoder\/masters\/measurements\/get-units-measurements","methods":["GET","HEAD"],"domain":null},"measurements.get-latest-code":{"uri":"encoder\/masters\/measurements\/get-latest-code","methods":["GET","HEAD"],"domain":null},"galvanic-finishes.index":{"uri":"encoder\/masters\/galvanic-finishes","methods":["GET","HEAD"],"domain":null},"galvanic-finishes.store":{"uri":"encoder\/masters\/galvanic-finishes","methods":["POST"],"domain":null},"galvanic-finishes.update":{"uri":"encoder\/masters\/galvanic-finishes\/{galvanic_finish}","methods":["PUT","PATCH"],"domain":null},"galvanic-finishes.destroy":{"uri":"encoder\/masters\/galvanic-finishes\/{galvanic_finish}","methods":["DELETE"],"domain":null},"galvanic-finishes.get-latest-code":{"uri":"encoder\/masters\/galvanic-finishes\/get-latest-code","methods":["GET","HEAD"],"domain":null},"decorative-options.index":{"uri":"encoder\/masters\/decorative-options","methods":["GET","HEAD"],"domain":null},"decorative-options.store":{"uri":"encoder\/masters\/decorative-options","methods":["POST"],"domain":null},"decorative-options.update":{"uri":"encoder\/masters\/decorative-options\/{decorative_option}","methods":["PUT","PATCH"],"domain":null},"decorative-options.destroy":{"uri":"encoder\/masters\/decorative-options\/{decorative_option}","methods":["DELETE"],"domain":null},"decorative-options.get-latest-code":{"uri":"encoder\/masters\/decorative-options\/get-latest-code","methods":["GET","HEAD"],"domain":null},"cash-registrer-receipts.index":{"uri":"third-parties\/cash-registrer-receipts","methods":["GET","HEAD"],"domain":null},"cash-registrer-receipts.store":{"uri":"third-parties\/cash-registrer-receipts","methods":["POST"],"domain":null},"cash-registrer-receipts.update":{"uri":"third-parties\/cash-registrer-receipts\/{cash_registrer_receipt}","methods":["PUT","PATCH"],"domain":null},"cash-registrer-receipts.destroy":{"uri":"third-parties\/cash-registrer-receipts\/{cash_registrer_receipt}","methods":["DELETE"],"domain":null},"cash-registrer-receipts.create":{"uri":"third-parties\/cash-registrer-receipts\/create","methods":["GET","HEAD"],"domain":null},"posts.index":{"uri":"blog\/posts","methods":["GET","HEAD"],"domain":null},"posts.create":{"uri":"blog\/posts\/create","methods":["GET","HEAD"],"domain":null},"posts.store":{"uri":"blog\/posts","methods":["POST"],"domain":null},"posts.show":{"uri":"blog\/posts\/{post}","methods":["GET","HEAD"],"domain":null},"posts.edit":{"uri":"blog\/posts\/{post}\/edit","methods":["GET","HEAD"],"domain":null},"posts.update":{"uri":"blog\/posts\/{post}","methods":["PUT","PATCH"],"domain":null},"posts.destroy":{"uri":"blog\/posts\/{post}","methods":["DELETE"],"domain":null},"change-password.update":{"uri":"change-password","methods":["PUT"],"domain":null},"roles.index":{"uri":"roles","methods":["GET","HEAD"],"domain":null},"roles.create":{"uri":"roles\/create","methods":["GET","HEAD"],"domain":null},"roles.store":{"uri":"roles","methods":["POST"],"domain":null},"roles.show":{"uri":"roles\/{role}","methods":["GET","HEAD"],"domain":null},"roles.edit":{"uri":"roles\/{role}\/edit","methods":["GET","HEAD"],"domain":null},"roles.update":{"uri":"roles\/{role}","methods":["PUT","PATCH"],"domain":null},"roles.destroy":{"uri":"roles\/{role}","methods":["DELETE"],"domain":null},"permissions.index":{"uri":"permissions","methods":["GET","HEAD"],"domain":null},"permissions.create":{"uri":"permissions\/create","methods":["GET","HEAD"],"domain":null},"permissions.store":{"uri":"permissions","methods":["POST"],"domain":null},"permissions.show":{"uri":"permissions\/{permission}","methods":["GET","HEAD"],"domain":null},"permissions.edit":{"uri":"permissions\/{permission}\/edit","methods":["GET","HEAD"],"domain":null},"permissions.update":{"uri":"permissions\/{permission}","methods":["PUT","PATCH"],"domain":null},"permissions.destroy":{"uri":"permissions\/{permission}","methods":["DELETE"],"domain":null},"permission-groups.index":{"uri":"permission-groups","methods":["GET","HEAD"],"domain":null},"permission-groups.create":{"uri":"permission-groups\/create","methods":["GET","HEAD"],"domain":null},"permission-groups.store":{"uri":"permission-groups","methods":["POST"],"domain":null},"permission-groups.show":{"uri":"permission-groups\/{permission_group}","methods":["GET","HEAD"],"domain":null},"permission-groups.edit":{"uri":"permission-groups\/{permission_group}\/edit","methods":["GET","HEAD"],"domain":null},"permission-groups.update":{"uri":"permission-groups\/{permission_group}","methods":["PUT","PATCH"],"domain":null},"permission-groups.destroy":{"uri":"permission-groups\/{permission_group}","methods":["DELETE"],"domain":null},"users.index":{"uri":"users","methods":["GET","HEAD"],"domain":null},"users.create":{"uri":"users\/create","methods":["GET","HEAD"],"domain":null},"users.store":{"uri":"users","methods":["POST"],"domain":null},"users.show":{"uri":"users\/{user}","methods":["GET","HEAD"],"domain":null},"users.edit":{"uri":"users\/{user}\/edit","methods":["GET","HEAD"],"domain":null},"users.update":{"uri":"users\/{user}","methods":["PUT","PATCH"],"domain":null},"users.destroy":{"uri":"users\/{user}","methods":["DELETE"],"domain":null},"backup.download":{"uri":"backup\/download\/{file_name}","methods":["GET","HEAD"],"domain":null},"backup.delete":{"uri":"backup\/delete\/{file_name}","methods":["GET","HEAD"],"domain":null},"backup.index":{"uri":"backup","methods":["GET","HEAD"],"domain":null},"backup.create":{"uri":"backup\/create","methods":["GET","HEAD"],"domain":null},"backup.store":{"uri":"backup","methods":["POST"],"domain":null},"orders.index":{"uri":"orders","methods":["GET","HEAD"],"domain":null},"orders.create":{"uri":"orders\/create","methods":["GET","HEAD"],"domain":null},"orders.store":{"uri":"orders","methods":["POST"],"domain":null},"orders.wallet":{"uri":"orders\/wallet","methods":["GET","HEAD"],"domain":null},"orders.costs":{"uri":"orders\/costs","methods":["GET","HEAD"],"domain":null},"orders.production":{"uri":"orders\/production","methods":["GET","HEAD"],"domain":null},"orders.cellar":{"uri":"orders\/cellar","methods":["GET","HEAD"],"domain":null},"orders.dies":{"uri":"orders\/dies","methods":["GET","HEAD"],"domain":null},"orders.send_wallet":{"uri":"orders\/send-wallet","methods":["POST"],"domain":null},"orders.cancel":{"uri":"orders\/cancel","methods":["POST"],"domain":null},"orders.reopen":{"uri":"orders\/reopen","methods":["POST"],"domain":null},"orders.view":{"uri":"orders\/view","methods":["GET","HEAD"],"domain":null},"orders.log_data":{"uri":"orders\/log-data","methods":["GET","HEAD"],"domain":null},"orders.wallet.approve":{"uri":"orders\/wallet\/approve","methods":["POST"],"domain":null},"orders.wallet.refuse":{"uri":"orders\/wallet\/refuse","methods":["POST"],"domain":null},"orders.costs.approve":{"uri":"orders\/costs\/approve","methods":["POST"],"domain":null},"orders.costs.refuse":{"uri":"orders\/costs\/refuse","methods":["POST"],"domain":null},"orders.cellar.refuse":{"uri":"orders\/cellar\/refuse","methods":["POST"],"domain":null},"orders.production.refuse":{"uri":"orders\/production\/refuse","methods":["POST"],"domain":null},"orders.dies.refuse":{"uri":"orders\/dies\/refuse","methods":["POST"],"domain":null},"orders.finalize_order":{"uri":"orders\/finalize-order","methods":["POST"],"domain":null}},
        baseUrl: 'http://evpiu-vue.test/',
        baseProtocol: 'http',
        baseDomain: 'evpiu-vue.test',
        basePort: false,
        defaultParameters: []
    };

    if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
        for (var name in window.Ziggy.namedRoutes) {
            Ziggy.namedRoutes[name] = window.Ziggy.namedRoutes[name];
        }
    }

    export {
        Ziggy
    }