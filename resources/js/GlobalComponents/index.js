import Litepicker from "./LitePicker/Main.vue";
import Tippy from "./Tippy.vue"
import TippyContent from "./TippyContent.vue";
import TomSelect from "./TomSelect/Main.vue";
import LoadingIcon from "./LoadingIcon.vue";
import Dropzone from "./dropzone/Main.vue";

import * as featherIcons from "@zhuowenli/vue-feather-icons";

export default (app) => {
    app.component("Litepicker", Litepicker);
    app.component("Tippy", Tippy);
    app.component("TippyContent", TippyContent);
    app.component("TomSelect", TomSelect);
    app.component("LoadingIcon", LoadingIcon);
    app.component("Dropzone", Dropzone);

    for (const [key, icon] of Object.entries(featherIcons)) {
        icon.props.size.default = "24";
        app.component(key, icon);
    }
}








