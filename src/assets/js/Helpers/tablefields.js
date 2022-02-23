/* ProjectBuilder app.js file */

class TableFields {

    constructor(showid = true, sort = false) {
        this.fields = {}
        if (sort) {
            this.fields['sorthandle'] = {
                key: "sort",
                name: "Sort",
                style: {
                    centered: true,
                    bold: true,
                    width: "w-12",
                },
                buttons: [],
                href: {},
                arrval: {},
                size: 'single'
            }
        }
        if (showid) {
            this.fields['item'] = {
                key: "id",
                name: "#",
                style: {
                    centered: true,
                    bold: true,
                    width: "w-12",
                },
                buttons: [],
                href: {},
                arrval: {},
                size: 'single'
            }
        }
    }

    customField(key, name, arrval = {}, style = {}, buttons = {}, href = {}, size = 'single', status = false, orderable = false) {
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
        if ((!href.hasOwnProperty('route') || !href.hasOwnProperty('id')) && !href.hasOwnProperty('custom')) {
            href = {}
        }
        /* Return */
        this.fields[key] = {
            key: key,
            name: name,
            style: style,
            buttons: buttons,
            href: href,
            arrval: arrval,
            size: size,
            status: status,
            orderable: orderable,
        }
    }

    pushActions(buttons) {
        this.fields['actions'] = {
            name: "Actions",
            style: {
                centered: true,
                bold: false,
                width: "w-20",
            },
            buttons: {},
            href: {},
            arrval: {},
            size: null,
        }
        let actions = this.fields['actions'];
        let options = ['update', 'delete']
        options.forEach(function (option) {
            if (buttons.hasOwnProperty(option)) {
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
                if (!buttons[option].hasOwnProperty('altformodel')) {
                    buttons[option]['altformodel'] = {}
                }
                /* Method */
                if (!buttons[option].hasOwnProperty('allowed')) {
                    buttons[option]['allowed'] = true
                }
                if (buttons[option]['allowed']) {
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
                        altformodel: buttons[option].altformodel,
                    }
                }
            }
        });
    }

    static onItemClicked(value, data, key) {
        for (let i in value) {
            if (i === "id") {
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

    static buildHiddenIdTag(data = null) {
        return 'hidden-form-' + ((data && data.hasOwnProperty('item') && data.item) ? 'edit' : 'create') + '-' + this.generateRandom()
    }

    static generateRandom() {
        return Math.floor((Math.random() * 999999999) + 1).toString()
    }

    static generateRandomTag(data = null) {
        return ((data && data.hasOwnProperty('item') && data.item) ? 'edit' : 'create') + '-' + this.generateRandom
    }

    static fixKey(index) {
        if (index === "item") {
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

    static isFlex(flex) {
        if (flex) {
            return " flex";
        }
        return ""
    }

    static buildTdClasses(centered) {
        let clase = "border px-4 py-2 align-top"
        clase += this.isCentered(centered)
        return clase
    }

    static buildSpanClasses(bold = false, centered = false, flex = true) {
        let clase = "inline-flex items-center mt-2 mb-1"
        clase += this.isFlex(flex)
        clase += this.isBold(bold)
        clase += this.isCentered(centered)
        return clase
    }

    static buildHandlerClasses(bold = true, centered = true) {
        let clase = "border px-4 py-2 align-top cursor-pointer sort-handle"
        clase += this.isCentered(centered)
        return clase
    }

    buildTableFields(listing) {
        for (const [k, v] of Object.entries(listing)) {
            if ((v.key !== 'item') && (v.key !== 'actions') && (v.key !== 'sorthandle')) {
                this.customField(v.key, v.name, v.arrval, v.style, v.buttons, v.href, v.size, v.status, v.orderable)
            } else if (v.key === 'actions') {
                this.pushActions(v.buttons);
            }
        }
        return this.fields
    }

    static appendToSwal(id) {
        let hidden = document.getElementById(id);
        let formodal = document.getElementById('formmodal');
        if (!!formodal) {
            formodal.append(hidden.childNodes[0]);
        }
    }

    static removeFromSwal(id) {
        let hidden = document.getElementById(id);
        let formodal = document.getElementById('formmodal');
        if (!!hidden) {
            hidden.append(formodal.childNodes[0]);
        }
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

    static getRowPos(sort, el) {
        if (sort && el.hasOwnProperty('position')) {
            return el.position
        }
        return 0
    }

    static getSortingOptions() {
        return {
            animation: 150,
            ghostClass: 'blue-background-class',
            /* delay: 500,
            delayOnTouchOnly: true, */
            easing: "cubic-bezier(1, 0, 0, 1)",
            handle: ".sort-handle",
            direction: 'vertical',
        }
    }
}

module.exports = {
    TableFields,
}
