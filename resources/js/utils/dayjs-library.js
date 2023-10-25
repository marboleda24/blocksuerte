import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
import utc from "dayjs/plugin/utc";
import timezone from "dayjs/plugin/timezone";
import 'dayjs/locale/es'
dayjs.extend(duration);
dayjs.extend(utc);
dayjs.extend(timezone);

dayjs.tz.setDefault('America/Bogota')
dayjs.locale('es')

const install = (app) => {
    app.config.globalProperties.$dayjs = dayjs;
};

export {install as default};