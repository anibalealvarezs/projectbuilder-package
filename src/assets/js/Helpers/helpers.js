/* ProjectBuilder app.js file */

const {usePage} = require("@inertiajs/inertia-vue3");

class Helpers {

    static refineURL(url) {
        return url.split("?")[0];
    }

    static proxyToObject(proxy) {
        return Object.assign({}, proxy);
    }

    static getModelIdsList(model) {
        let list = [];
        if (model) {
            for (const [k, v] of Object.entries(model)) {
                list.push(v.id);
            }
        }
        return list;
    }

    static removeIdsFromNavigations(navs, item) {
        let n = this.proxyNavsToObject(navs)
        let navigations = []
        let excluded = [item]
        excluded = excluded.concat(this.getNavChildren(n, item))
        for (const [k, v] of Object.entries(n)) {
            if (!excluded.includes(v.id)) {
                navigations.push(v)
            }
        }
        return navigations
    }

    static proxyNavsToObject(navs) {
        let navigations = {}
        let objNavs = this.proxyToObject(navs.value);
        for (const [k, v] of Object.entries(objNavs)) {
            navigations[k] = this.proxyToObject(v)
        }
        return navigations
    }

    static getNavChildren(navs, el) {
        let arr = []
        for (const [k, v] of Object.entries(navs)) {
            if (v.parent === el) {
                arr.push(v.id)
                arr = arr.concat(this.getNavChildren(navs, v.id))
            }
        }
        return arr
    }

    static isDebugEnabled() {
        return usePage().props.value.shared.debug_enabled
    }

    static stringToBoolean(string) {
        if (!string) {
            string = "false"
        }
        return JSON.parse(string)
    }

    static buildRoute(r, id) {
        if (id) {
            return route(r == '/' ? 'root' : r, id)
        }
        return route(r)
    }
}

module.exports = {
    Helpers,
}
