import helper from "./helper";
import lodash from "./lodash";
import colors from "./colors";
import dayjs_library from "./dayjs-library"

export default (app) => {
    app.use(helper);
    app.use(lodash);
    app.use(colors);
    app.use(dayjs_library)
};
