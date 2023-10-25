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

const helpers = {
    /**
     *
     * @param text
     * @param length
     * @returns {string|*}
     */
    cutText(text, length) {
        if (text.split(" ").length > 1) {
            const string = text.substring(0, length);
            const splitText = string.split(" ");
            splitText.pop();
            return splitText.join(" ") + "...";
        } else {
            return text;
        }
    },

    /**
     *
     * @param str
     * @returns {*}
     */
    toSnakeCase(str) {
        return str && str
            .match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
            .map(x => x.toLowerCase())
            .join('_')
    },

    /**
     *
     * @param str
     * @param length
     * @returns {string|*}
     */
    truncateString(str, length) {
        if (str.length <= length) {
            return str
        }
        return str.slice(0, length) + '…'
    },

    /**
     *
     * @param str
     * @param length
     * @returns {string|*}
     */
    truncateStringReverse(str, length) {
        if (str.length <= length) {
            return str
        }
        console.log(str.length)
        return '…' + str.slice(-length)
    },

    /**
     *
     * @param date
     * @param format
     * @returns {string}
     */
    formatDate(date, format) {
        return dayjs(date).format(format ?? 'DD-MM-YYYY hh:mm A');
    },

    /**
     *
     * @param string
     * @returns {string}
     */
    capitalizeFirstLetter(string) {
        if (string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        } else {
            return "";
        }
    },

    /**
     *
     * @param string
     * @returns {string|*}
     */
    onlyNumber(string) {
        if (string) {
            return string.replace(/\D/g, "");
        } else {
            return "";
        }
    },

    /**
     *
     * @param number
     * @param decimals
     * @returns {string|*}
     */
    formatCurrency(number, decimals = 0) {
        const number_value = Number(parseFloat(number).toFixed(2))

        if (typeof number_value === "undefined") {
            return number
        }
        const formatter = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: decimals
        });
        return formatter.format(number_value);
    },

    /**
     *
     * @param number
     * @param trm
     * @returns {string|*}
     */
    formatCurrencyUSD(number, trm) {
        const total = parseFloat(number) / parseFloat(trm);
        let number_value = Number(total.toFixed(2))

        if (typeof number_value === "undefined") {
            return number
        }

        const formatter = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 2
        });

        return `USD ` + formatter.format(number_value);
    },

    /**
     *
     * @param time
     * @returns {string|string|string|false}
     */
    timeAgo(time) {
        const date = new Date(
            (time || "").replace(/-/g, "/").replace(/[TZ]/g, " ")
        );
        const diff = (new Date().getTime() - date.getTime()) / 1000;
        const dayDiff = Math.floor(diff / 86400);

        if (isNaN(dayDiff) || dayDiff < 0 || dayDiff >= 31) {
            return dayjs(time).format("MMMM DD, YYYY");
        }

        return (
            (dayDiff === 0 &&
                ((diff < 60 && "justo ahora") ||
                    (diff < 120 && "hace 1 minuto") ||
                    (diff < 3600 && "hace " + Math.floor(diff / 60)) + " minutos" ||
                    (diff < 7200 && "hace 1 hora") ||
                    (diff < 86400 && "hace " + Math.floor(diff / 3600) + " horas"))) ||
            (dayDiff === 1 && "Ayer") ||
            (dayDiff < 7 && "hace " + dayDiff + " dias") ||
            (dayDiff < 31 && "hace " + Math.ceil(dayDiff / 7) + " semanas")
        );
    },

    /**
     *
     * @param time
     * @returns {{hours: (string|number), seconds: (string|number), minutes: (string|number), days: (string|number)}}
     */
    diffTimeByNow(time) {
        const startDate = dayjs(dayjs().format("YYYY-MM-DD HH:mm:ss").toString());
        const endDate = dayjs(dayjs(time).format("YYYY-MM-DD HH:mm:ss").toString());

        const duration = dayjs.duration(endDate.diff(startDate));
        const milliseconds = Math.floor(duration.asMilliseconds());

        const days = Math.round(milliseconds / 86400000);
        const hours = Math.round((milliseconds % 86400000) / 3600000);
        let minutes = Math.round(((milliseconds % 86400000) % 3600000) / 60000);
        const seconds = Math.round(
            (((milliseconds % 86400000) % 3600000) % 60000) / 1000
        );

        if (seconds < 30 && seconds >= 0) {
            minutes += 1;
        }

        return {
            days: days.toString().length < 2 ? "0" + days : days,
            hours: hours.toString().length < 2 ? "0" + hours : hours,
            minutes: minutes.toString().length < 2 ? "0" + minutes : minutes,
            seconds: seconds.toString().length < 2 ? "0" + seconds : seconds,
        };
    },

    /**
     *
     * @param start
     * @param end
     * @returns {{hours: (string|number), seconds: (string|number), minutes: (string|number), days: (string|number)}}
     */
    diffTime(start, end) {
        const startDate = dayjs(dayjs(start).format("YYYY-MM-DD HH:mm:ss").toString());
        const endDate = dayjs(dayjs(end).format("YYYY-MM-DD HH:mm:ss").toString());

        const duration = dayjs.duration(endDate.diff(startDate));
        const milliseconds = Math.floor(duration.asMilliseconds());

        const days = Math.round(milliseconds / 86400000);
        const hours = Math.round((milliseconds % 86400000) / 3600000);
        let minutes = Math.round(((milliseconds % 86400000) % 3600000) / 60000);
        const seconds = Math.round(
            (((milliseconds % 86400000) % 3600000) % 60000) / 1000
        );

        return {
            days: days.toString().length < 2 ? "0" + days : days,
            hours: hours.toString().length < 2 ? "0" + hours : hours,
            minutes: minutes.toString().length < 2 ? "0" + minutes : minutes,
            seconds: seconds.toString().length < 2 ? "0" + seconds : seconds,
        };
    },

    /**
     *
     * @param obj
     * @returns {boolean|number}
     */
    isset(obj) {
        if (obj !== null && obj !== undefined) {
            if (typeof obj === "object" || Array.isArray(obj)) {
                return Object.keys(obj).length;
            } else {
                return obj.toString().length;
            }
        }

        return false;
    },

    /**
     *
     * @param obj
     * @returns {any}
     */
    toRaw(obj) {
        return JSON.parse(JSON.stringify(obj));
    },

    /**
     *
     * @param from
     * @param to
     * @param length
     * @returns {number[]}
     */
    randomNumbers(from, to, length) {
        const numbers = [0];
        for (let i = 1; i < length; i++) {
            numbers.push(Math.ceil(Math.random() * (from - to) + to));
        }

        return numbers;
    },

    /**
     *
     * @param colors
     * @returns {*}
     */
    toRGB(colors) {
        const tempColors = Object.assign({}, colors);
        const rgbColors = Object.entries(tempColors);
        for (const [key, value] of rgbColors) {
            if (typeof value === "string") {
                if (value.replace("#", "").length == 6) {
                    const aRgbHex = value.replace("#", "").match(/.{1,2}/g);
                    tempColors[key] = (opacity = 1) =>
                        `rgb(${parseInt(aRgbHex[0], 16)} ${parseInt(
                            aRgbHex[1],
                            16
                        )} ${parseInt(aRgbHex[2], 16)} / ${opacity})`;
                }
            } else {
                tempColors[key] = helpers.toRGB(value);
            }
        }
        return tempColors;
    },

    /**
     *
     * @param number
     * @returns {string}
     */
    numberTwoPlaces(number) {
        return new Intl.NumberFormat('es-CO').format(number);
    },

    /**
     *
     * @param number
     * @returns {number}
     */
    numberRound(number) {
        return Math.round(number)
    },

    /**
     *
     * @param code_list
     * @param length
     * @returns {string}
     */
    alphaNumericIncrement(code_list, length) {
        let incremental = 0;
        let chart_string_range = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        let vector = [];
        let t = 0;
        let numberf = 0;

        for (let i = 0; i < code_list.length; i++) {
            if (code_list[i].length === 2) {
                const string = code_list[i];
                let text = string.split('').reverse().join('');
                text = text.split('');

                for (let k = 0; k < length; k++) {
                    for (var j = 0; j < 36; j++) {
                        if (text[k] === chart_string_range[j]) {
                            break;
                        }
                    }
                    numberf += j * Math.pow(36, k);
                }
                vector[t] = numberf;
                t++;
                numberf = 0;
            }
        }

        let maxvector = Math.max.apply(Math, vector); //saca el valor maximo de un arreglo
        if (maxvector >= 0) {
            incremental = maxvector + 1;
        }

        let text2 = '';
        let incretemp = incremental;
        for (let i = 0; i < length; i++) {
            incretemp = Math.floor(incretemp) / 36;
            text2 += chart_string_range.charAt(Math.round((incretemp - Math.floor(incretemp)) * 36));
        }
        return text2.split('').reverse().join('');
    },

    /**
     *
     * @param code_list
     * @param code
     * @param length
     * @param min_length
     * @param finish_length
     * @returns {string}
     */
    alphaNumericIncrementWithCode(code_list, code, length, min_length, finish_length) {
        let incremental = 0;
        let chart_string_range = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        let vector = [];
        let t = 0;
        let numberf = 0;

        for (let i = 0; i < code_list.length; i++) {
            if (code === code_list[i].substring(0, min_length) && code_list[i].length === finish_length) {
                const string = code_list[i].substring(2);
                let text = string.split('').reverse().join('');
                text = text.split('');

                for (let k = 0; k < length; k++) {
                    for (var j = 0; j < 36; j++) {
                        if (text[k] === chart_string_range[j]) {
                            break;
                        }
                    }
                    numberf += j * Math.pow(36, k);
                }
                vector[t] = numberf;
                t++;
                numberf = 0;
            }
        }

        let maxvector = Math.max.apply(Math, vector);
        if (maxvector >= 0) {
            incremental = maxvector + 1;
        }

        let text2 = '';
        let incretemp = incremental;
        for (let i = 0; i < length; i++) {
            incretemp = Math.floor(incretemp) / 36;
            text2 += chart_string_range.charAt(Math.round((incretemp - Math.floor(incretemp)) * 36));
        }
        text2 = text2.split('').reverse().join('');
        return code + text2;
    },

    /**
     *
     * @param array
     * @returns {string}
     */
    denominationCreator(array) {
        let regex = new RegExp(/(\d+(?:\.\d+)?)(\/\d+(?:\.\d+)?)*$/gm)

        const group = array.reduce(function (rv, x) {
            (rv[x['unit'].code] = rv[x['unit'].code] || []).push(x);
            return rv;
        }, {})

        const measurement = [];

        for (const groupKey in group) {
            let regx = group[groupKey].map(function (x) {
                if (x.value && x.value > 0 || regex.test(x.value)) {
                    return `${x.characteristic.code}:${parseFloat(x.value)}`
                }
            }, null);
            regx = regx.filter(x => x !== undefined);

            if (regx.length > 0) {
                measurement.push(`${regx.length > 1 ? regx.join(' ') : regx.join('')}${groupKey}`)
            }
        }
        return measurement.length > 1 ? measurement.join(' ') : measurement.join('');
    },

    /**
     *
     * @param array
     * @param glue
     * @returns {*}
     */
    gluePermission(array, glue) {
        return array.join(glue)
    },

    /**
     *
     * @param type
     * @returns {string}
     */
    translate_types_maintenance(type) {
        switch (type) {
            case "preventive":
                return 'Preventivo'
            case "corrective":
                return 'Correctivo'
            case "locative":
                return 'Locativo'
            case "improvement":
                return 'Mejorativo'

        }
    },

    /**
     *
     * @param state
     * @returns {string}
     */
    proposalState(state) {
        switch (state) {
            case "0":
                return '<span class="badge badge-danger">Anulado</span>'
            case "1":
                return '<span class="badge badge-warning">En Proceso</span>'
            case "2":
                return '<span class="badge badge-warning">Pendiente 3D</span>'
            case "3":
                return '<span class="badge badge-warning">Pendiente Planos</span>'
            case "4":
                return '<span class="badge badge-pink">Pendiente Aprobación</span>'
            case "5":
                return '<span class="badge badge-danger">Por Corregir</span>'
            case "6":
                return '<span class="badge badge-purple">Aprobado</span>'
            case "7":
                return '<span class="badge badge-success">Finalizado</span>'
        }
    },

    /**
     *
     * @returns {string}
     */
    loadIcon() {
        let el = document.getElementById('swalHTML');
        return el.innerHTML;
    },

    /**
     *
     * @param document
     * @returns {string|number|number}
     */
    calculeDV(document) {
        var vpri, x, y, z;

        var myNit = document.toString();

        // Se limpia el Nit
        myNit = myNit.replace(/\s/g, ""); // Espacios
        myNit = myNit.replace(/,/g, ""); // Comas
        myNit = myNit.replace(/\./g, ""); // Puntos
        myNit = myNit.replace(/-/g, ""); // Guiones

        // Se valida el nit
        if (isNaN(myNit)) {
            return "";
        }

        // Procedimiento
        vpri = new Array(16);
        z = myNit.length;

        vpri[1] = 3;
        vpri[2] = 7;
        vpri[3] = 13;
        vpri[4] = 17;
        vpri[5] = 19;
        vpri[6] = 23;
        vpri[7] = 29;
        vpri[8] = 37;
        vpri[9] = 41;
        vpri[10] = 43;
        vpri[11] = 47;
        vpri[12] = 53;
        vpri[13] = 59;
        vpri[14] = 67;
        vpri[15] = 71;

        x = 0;
        y = 0;
        for (let i = 0; i < z; i++) {
            y = (myNit.substr(i, 1));
            x += (y * vpri [z - i]);
        }
        y = x % 11;
        return (y > 1) ? 11 - y : y;
    },

};

const install = (app) => {
    app.config.globalProperties.$h = helpers;
};

export {install as default, helpers as helper};

