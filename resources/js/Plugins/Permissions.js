export default {
    install: (app) => {
        app.mixin({
            mounted() {
                if (this.$page.props.user) {
                    this.$gates.setRoles(this.$page.props.roles);
                    this.$gates.setPermissions(this.$page.props.permissions);
                }else {
                    this.$gates.setRoles([])
                    this.$gates.setPermissions([])
                }
            }
        })
    }
}
