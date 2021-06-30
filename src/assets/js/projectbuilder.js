/* ProjectBuilder app.js file */

class TableFields {

    constructor() {
        this.fields = {
            "item": {
                name: "#",
                style: {
                    centered: true,
                    bold: true,
                    width: "w-20",
                },
                buttons: [],
                href: {},
                arrval: {},
                size: 'single'
            }
        }
    }

    customField(key, name, arrval = {}, style = {}, buttons = {}, href = {}, size = 'single') {
        /* Style */
        if (!style.hasOwnProperty('centered')) {
            style['centered'] = false
        }
        if (!style.hasOwnProperty('bold')) {
            style['bold'] = false
        }
        if (!style.hasOwnProperty('width')) {
            style['width'] = ""
        }
        /* Buttons */
        /* Href */
        if (!href.hasOwnProperty('route') || !href.hasOwnProperty('id')) {
            href = {}
        }
        /* Return */
        this.fields[key] = {
            name: name,
            style: style,
            buttons: buttons,
            href: href,
            arrval: arrval,
            size: size,
        }
    }

    pushActions(buttons) {
        this.fields['actions'] = {
            name: "Actions",
            style: {
                centered: true,
                bold: false,
                width: "w-60",
            },
            buttons: {},
            href: {},
            arrval: {},
            size: null,
        }
        let actions = this.fields['actions'];
        let options = ['update', 'delete']
        options.forEach(function (option) {
            /* Enabled */
            if (!buttons[option].hasOwnProperty('enabled')) {
                buttons[option]['enabled'] = true
            }
            /* Text */
            if (!buttons[option].hasOwnProperty('text')) {
                buttons[option]['text'] = 'NO TEXT DEFINED'
            }
            /* ID */
            if (!buttons[option].hasOwnProperty('id')) {
                buttons[option]['id'] = true
            }
            /* Route */
            if (!buttons[option].hasOwnProperty('route')) {
                buttons[option]['route'] = "/"
            }
            /* Callback */
            if (!buttons[option].hasOwnProperty('callback')) {
                buttons[option]['callback'] = null
            }
            /* Style */
            if (!buttons[option].hasOwnProperty('style')) {
                buttons[option]['style'] = 'default'
            }
            /* Type */
            if (!buttons[option].hasOwnProperty('type')) {
                buttons[option]['type'] = 'form'
            }
            /* Method */
            if (!buttons[option].hasOwnProperty('method')) {
                buttons[option]['method'] = 'PUT'
            }
            /* Method */
            if (!buttons[option].hasOwnProperty('altforuser')) {
                buttons[option]['altforuser'] = {}
            }
            /* Method */
            if (!buttons[option].hasOwnProperty('allowed')) {
                buttons[option]['allowed'] = true
            }
            if (buttons[option]['allowed']) {
                console.log(option);
                console.log(buttons[option]);
                actions['buttons'][option] = {
                    enabled: buttons[option].enabled,
                    text: buttons[option].text,
                    route: buttons[option].route,
                    id: buttons[option].id,
                    callback: buttons[option].callback,
                    style: buttons[option].style,
                    type: buttons[option].type,
                    formitem: buttons[option].formitem,
                    method: buttons[option].method,
                    altforuser: buttons[option].altforuser,
                }
            }
        });
    }

    static onItemClicked(value, data, key) {
        for (let i in value) {
            if (i == "id") {
                data['item'] = value[i]
            } else {
                data[i] = value[i]
            }
        }
        key += 1
        return {
            key: key,
            data: data
        }
    }

    static existsFormButton(buttons) {
        if (buttons) {
            for (const [k, v] of Object.entries(buttons)) {
                if (v.enabled && (v.type === "form")) {
                    return true
                }
            }
        }
        return false
    }

    static buildHiddenId() {
        return 'hidden-form-' + this.generateRandom()
    }

    static generateRandom() {
        return Math.floor((Math.random() * 999999999) + 1)
    }

    static fixKey(index) {
        if (index == "item") {
            return "id"
        }
        return index
    }

    static isCentered(centered) {
        let ret = "";
        if (centered) {
            ret += " text-center"
        }
        return ret
    }

    static isBold(bold) {
        if (bold) {
            return " font-semibold";
        }
        return ""
    }

    static buildTdClasses(centered) {
        let clase = "border px-4 py-2"
        clase += this.isCentered(centered)
        return clase
    }

    static buildSpanClasses(bold, centered) {
        let clase = ""
        clase += this.isBold(bold)
        clase += this.isCentered(centered)
        return clase
    }

    static appendToSwal(id) {
        let hidden = document.getElementById(id);
        let formodal = document.getElementById('formmodal');
        formodal.append(hidden.childNodes[0]);
    }

    static removeFromSwal(id) {
        let hidden = document.getElementById(id);
        let formodal = document.getElementById('formmodal');
        hidden.append(formodal.childNodes[0]);
    }

    static buildSwalLoadFormConfig(button) {
        return {
            title: button.text + ' ' + button.formitem,
            html: '<div id="formmodal" class="p-12 shadow sm:px-20 bg-white border-b border-gray-200"></div>',
            confirmButtonText: button.text,
            showCloseButton: true,
            showCancelButton: false,
            showConfirmButton: false,
            width: 800
        }
    }

    static buildSwalConfirmAndSubmitConfig(button) {
        return {
            title: button.text + ' ' + button.formitem,
            text: 'Are you sure you want to proceed?',
            icon: 'warning',
            confirmButtonText: button.text
        }
    }
}

class Helpers {

    static refineURL(url) {
        return url.split("?")[0];
    }

    static proxyToObject(proxy) {
        return Object.assign({}, proxy);
    }
}

module.exports = {
    TableFields,
    Helpers
}
