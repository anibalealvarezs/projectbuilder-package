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
                arrval: {}
            }
        }
    }

    customField(key, name, arrval = {}, style = {}, buttons = {}, href = {}) {
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
            arrval: arrval
        }
    }

    pushActions(buttons) {
        /* Enabled */
        if (!buttons.update.hasOwnProperty('enabled')) {
            buttons.update['enabled'] = true
        }
        if (!buttons.delete.hasOwnProperty('enabled')) {
            buttons.delete['enabled'] = true
        }
        /* Text */
        if (!buttons.update.hasOwnProperty('text')) {
            buttons.update['text'] = 'Update'
        }
        if (!buttons.delete.hasOwnProperty('text')) {
            buttons.delete['text'] = 'Delete'
        }
        /* ID */
        if (!buttons.update.hasOwnProperty('id')) {
            buttons.update['id'] = true
        }
        if (!buttons.delete.hasOwnProperty('id')) {
            buttons.delete['id'] = true
        }
        /* Callback */
        if (!buttons.update.hasOwnProperty('callback')) {
            buttons.update['callback'] = null
        }
        if (!buttons.delete.hasOwnProperty('callback')) {
            buttons.delete['callback'] = null
        }
        /* Style */
        if (!buttons.update.hasOwnProperty('style')) {
            buttons.update['style'] = 'secondary'
        }
        if (!buttons.delete.hasOwnProperty('style')) {
            buttons.delete['style'] = 'danger'
        }
        /* Type */
        if (!buttons.update.hasOwnProperty('type')) {
            buttons.update['type'] = 'form'
        }
        if (!buttons.delete.hasOwnProperty('type')) {
            buttons.delete['type'] = 'form'
        }
        /* Method */
        if (!buttons.update.hasOwnProperty('method')) {
            buttons.update['method'] = 'PUT'
        }
        if (!buttons.delete.hasOwnProperty('method')) {
            buttons.delete['method'] = 'DELETE'
        }
        /* Method */
        if (!buttons.update.hasOwnProperty('altforuser')) {
            buttons.update['altforuser'] = {}
        }
        if (!buttons.delete.hasOwnProperty('altforuser')) {
            buttons.delete['altforuser'] = {}
        }
        this.fields['actions'] = {
            name: "Actions",
            style: {
                centered: true,
                bold: false,
                width: "w-60",
            },
            buttons: {
                'update': {
                    enabled: buttons.update.enabled,
                    text: buttons.update.text,
                    route: buttons.update.route,
                    id: buttons.update.id,
                    callback: buttons.update.callback,
                    style: buttons.update.style,
                    type: buttons.update.type,
                    formitem: buttons.update.formitem,
                    method: buttons.update.method,
                    altforuser: buttons.update.altforuser
                },
                'delete': {
                    enabled: buttons.delete.enabled,
                    text: buttons.delete.text,
                    route: buttons.delete.route,
                    id: buttons.delete.id,
                    callback: buttons.delete.callback,
                    style: buttons.delete.style,
                    type: buttons.delete.type,
                    formitem: buttons.delete.formitem,
                    method: buttons.delete.method,
                    altforuser: buttons.delete.altforuser
                }
            },
            href: {},
            arrval: {}
        }
    }

    static onItemClicked(value, data , key) {
        for(let i in value) {
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
                if ( v.enabled && (v.type === "form")) {
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
            html: '<div id="formmodal" class="p-12 sm:px-20 bg-white border-b border-gray-200"></div>',
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

    static refineURL(url)
    {
        return url.split("?")[0];
    }

    static proxyToObject(proxy)
    {
        return Object.assign({}, proxy);
    }
}

module.exports = {
    TableFields,
    Helpers
}
