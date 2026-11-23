// MGD Plugin-Baukasten - Admin-Seite "MGD Barrierefrei" fertig geschrieben, kein Build nötig
// Daten aus /api/_action/mgd-kit/mgd-barrierefrei-shopware/... , keine fremden Bilder/Schriften
(function (root) {
    'use strict';

    var PRODUCT = 'mgd-barrierefrei-shopware';
    var MODULE = 'mgd-kit-' + PRODUCT;
    var API = '/_action/mgd-kit/' + PRODUCT;
    var STYLE_ID = MODULE + '-style';
    // Links nur auf diese Adressen (Server prüft auch)
    var LINK_PREFIXES = ['https://theme.michael-gahn.de/', 'https://github.com/MichaelGahnDESIGN/'];
    var PLUGIN_PRIVS = ['system.plugin_maintain', 'system.plugin_upload'];

    var STYLE = [
        '.mgd-kit-sw p{margin:0 0 10px;line-height:1.5}',
        '.mgd-kit-sw .mgd-kit-note{padding:12px;border-left:4px solid #b45309;background:#fffbeb;color:#1f2d3d}',
        '.mgd-kit-sw .mgd-kit-error{padding:12px;border-left:4px solid #b91c1c;background:#fef2f2;color:#1f2d3d}',
        '.mgd-kit-sw .mgd-kit-ok{padding:12px;border-left:4px solid #15803d;background:#f0fdf4;color:#1f2d3d}',
        '.mgd-kit-sw ol{margin:0 0 10px 20px}',
        '.mgd-kit-sw .mgd-kit-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}',
        '.mgd-kit-sw .mgd-kit-card{padding:16px;border:1px solid #d1d9e0;border-radius:6px;background:#fff}',
        '.mgd-kit-sw .mgd-kit-card h3{margin:0 0 8px;font-size:16px}',
        '.mgd-kit-sw .mgd-kit-small{font-size:13px;color:#3d4c5c}',
        '.mgd-kit-sw label{display:block;margin:0 0 6px;font-weight:600}',
        '.mgd-kit-sw input[type=password]{width:100%;max-width:420px;min-height:44px;padding:8px;border:1px solid #6b7c8d;border-radius:4px}',
        '.mgd-kit-sw a:focus-visible,.mgd-kit-sw input:focus-visible{outline:3px solid #1d4ed8;outline-offset:2px}'
    ].join('');

    var TEMPLATE = [
        '<sw-page class="mgd-kit-sw">',
        '  <template #smart-bar-header><h2>{{ title }}</h2></template>',
        '  <template #content>',
        '    <sw-card-view>',
        '      <p v-if="isLoading" role="status">Wird geladen …</p>',
        '      <p v-else-if="loadError" class="mgd-kit-error" role="alert">Die Seite konnte gerade nicht geladen werden. Bitte lade sie neu.</p>',
        '      <template v-else>',
        '        <sw-card title="Info">',
        '          <p>Plugin: {{ status.name }}</p>',
        '          <p>Version: {{ updates.installed || "unbekannt" }}</p>',
        '        </sw-card>',
        '        <sw-card title="Updates">',
        '          <p v-if="!updates.available">Die Update-Prüfung hat gerade nicht geklappt. Versuche es später noch einmal.</p>',
        '          <p v-else-if="!updates.newer">Du hast die aktuelle Version.</p>',
        '          <template v-else>',
        '            <p>Version {{ updates.newest }} ist verfügbar.</p>',
        '            <p v-if="updates.changelog" class="mgd-kit-small">{{ updates.changelog }}</p>',
        '            <sw-button v-if="status.canUpdate && !fallback" variant="primary" :disabled="isUpdating" :isLoading="isUpdating" @click="runUpdate">Jetzt aktualisieren</sw-button>',
        '            <p v-if="!status.canUpdate && !fallback" class="mgd-kit-small">Den Knopf „Jetzt aktualisieren“ sehen nur Admins mit dem Recht, Erweiterungen hochzuladen und zu verwalten.</p>',
        '          </template>',
        '          <p v-if="updates.notice" class="mgd-kit-note">{{ updates.notice.text }} <a v-if="isLink(updates.notice.url)" :href="updates.notice.url" target="_blank" rel="noopener noreferrer">Zur aktuellen Version</a></p>',
        '          <p v-if="message" :class="messageOk ? \'mgd-kit-ok\' : \'mgd-kit-error\'" role="status">{{ message }}</p>',
        '          <template v-if="fallback || (updates.newer && !status.canUpdate)">',
        '            <p>So aktualisierst du von Hand:</p>',
        '            <ol><li v-for="(step, i) in steps" :key="i">{{ step }}</li></ol>',
        '            <p v-if="isLink(downloadLink)"><a :href="downloadLink" target="_blank" rel="noopener noreferrer">ZIP-Datei herunterladen</a></p>',
        '          </template>',
        '        </sw-card>',
        '        <sw-card title="Weitere Plugins">',
        '          <p v-if="recStale" class="mgd-kit-small">Die Liste konnte gerade nicht aktualisiert werden. Du siehst den letzten bekannten Stand.</p>',
        '          <p v-if="recommendations.length === 0">Gerade gibt es keine weiteren Plugins.</p>',
        '          <div v-else class="mgd-kit-cards">',
        '            <article v-for="item in recommendations" :key="item.slug" class="mgd-kit-card">',
        '              <h3>{{ item.name }}</h3>',
        '              <p>{{ item.description }}</p>',
        '              <p v-if="item.version" class="mgd-kit-small">Version {{ item.version }}</p>',
        '              <p><a :href="detailsUrl(item)" target="_blank" rel="noopener noreferrer">Mehr erfahren</a></p>',
        '            </article>',
        '          </div>',
        '        </sw-card>',
        '        <sw-card v-if="status.install" title="Installation melden">',
        '          <sw-switch-field :label="status.install.label" :value="status.install.enabled" :disabled="isSaving" @update:value="setInstall" @change="setInstall"></sw-switch-field>',
        '          <p class="mgd-kit-small">{{ status.install.hint }}</p>',
        '          <p class="mgd-kit-small" role="status">{{ status.install.status }}</p>',
        '        </sw-card>',
        '        <sw-card v-if="status.license" title="Lizenz">',
        '          <label :for="licenseId">Lizenzschlüssel</label>',
        '          <input :id="licenseId" v-model="licenseToken" type="password" autocomplete="off" spellcheck="false">',
        '          <p v-if="status.license.hasToken" class="mgd-kit-small">Eingetragen ist ein Schlüssel, der auf {{ status.license.hint }} endet.</p>',
        '          <p role="status">{{ status.license.status }}</p>',
        '          <p v-if="licenseMessage" role="status">{{ licenseMessage }}</p>',
        '          <sw-button variant="primary" :disabled="isSaving" @click="saveLicense(\'save\')">Speichern</sw-button>',
        '          <sw-button v-if="status.license.hasToken" :disabled="isSaving" @click="saveLicense(\'remove\')">Entfernen</sw-button>',
        '          <p class="mgd-kit-small">Zur Prüfung gehen nur der Lizenzschlüssel und die Domain deines Shops an theme.michael-gahn.de.</p>',
        '        </sw-card>',
        '      </template>',
        '    </sw-card-view>',
        '  </template>',
        '</sw-page>'
    ].join('\n');

    function isLink(url) {
        return typeof url === 'string' && url.length < 500 && !/[\s"'<>\\]/.test(url) && LINK_PREFIXES.some(function (p) {
            return url.indexOf(p) === 0;
        });
    }

    // Kennung prüfen, Link immer selbst bauen
    function detailsUrl(item) {
        var slug = item && typeof item.slug === 'string' && /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(item.slug) ? item.slug : '';

        return 'https://theme.michael-gahn.de/plugins/' + slug;
    }

    function addStyle(doc) {
        if (!doc || doc.getElementById(STYLE_ID)) {
            return;
        }
        var el = doc.createElement('style');
        el.id = STYLE_ID;
        el.textContent = STYLE;
        doc.head.appendChild(el);
    }

    var component = {
        template: TEMPLATE,
        inject: ['loginService', 'acl'],
        data: function () {
            return {
                isLoading: true, loadError: false, isUpdating: false, isSaving: false,
                status: {}, recommendations: [], recStale: false,
                message: '', messageOk: false, fallback: null,
                licenseToken: '', licenseMessage: ''
            };
        },
        computed: {
            title: function () {
                return 'MGD Barrierefrei';
            },
            updates: function () {
                return this.status.updates || {};
            },
            steps: function () {
                var s = this.fallback && Array.isArray(this.fallback.steps) ? this.fallback.steps : this.status.fallbackSteps;

                return Array.isArray(s) ? s : [];
            },
            downloadLink: function () {
                return this.fallback && this.fallback.url ? this.fallback.url : this.updates.downloadUrl;
            },
            licenseId: function () {
                return MODULE + '-license';
            }
        },
        created: function () {
            addStyle(root.document);
            this.load();
        },
        methods: {
            isLink: isLink,
            detailsUrl: detailsUrl,
            http: function () {
                return Shopware.Application.getContainer('init').httpClient;
            },
            opts: function () {
                return { headers: { Authorization: 'Bearer ' + this.loginService.getToken(), 'Content-Type': 'application/json' } };
            },
            load: function () {
                var vm = this;
                vm.isLoading = true;

                return vm.http().get(API + '/status', vm.opts()).then(function (r) {
                    vm.status = r && r.data ? r.data : {};
                    vm.loadError = false;

                    return vm.http().get(API + '/recommendations', vm.opts()).then(function (r2) {
                        var d = r2 && r2.data ? r2.data : {};
                        vm.recommendations = Array.isArray(d.items) ? d.items : [];
                        vm.recStale = d.source === 'stale' || d.source === 'fallback';
                    }).catch(function () {
                        vm.recommendations = [];
                        vm.recStale = true;
                    });
                }).catch(function () {
                    vm.loadError = true;
                }).then(function () {
                    vm.isLoading = false;
                });
            },
            // 2 Schritte: einspielen, dann eigener Aufruf fürs Shopware-Update (neuer Code geladen)
            runUpdate: function () {
                var vm = this;
                if (vm.isUpdating || !PLUGIN_PRIVS.every(function (p) { return vm.acl.can(p); })) {
                    return null;
                }
                vm.isUpdating = true;
                vm.message = '';
                vm.fallback = null;

                return vm.http().post(API + '/update', {}, vm.opts()).then(function (r) {
                    var d = r && r.data ? r.data : {};
                    if (d.ok === true && d.step === 'uploaded') {
                        vm.show(d);

                        return vm.http().post(API + '/update/finish', {}, vm.opts()).then(function (r2) {
                            vm.show(r2 && r2.data ? r2.data : {});
                        });
                    }
                    vm.show(d);

                    return null;
                }).catch(function (e) {
                    vm.show(e && e.response && e.response.data ? e.response.data : {});
                }).then(function () {
                    vm.isUpdating = false;
                });
            },
            show: function (d) {
                this.messageOk = d.ok === true;
                this.message = typeof d.message === 'string' ? d.message : 'Das Update hat nicht geklappt.';
                this.fallback = d.ok === true ? null : (d.fallback || { url: this.updates.downloadUrl, steps: this.status.fallbackSteps });
            },
            // 6.5 schickt change, ab 6.6 update:value > doppelt abfangen
            setInstall: function (on) {
                var vm = this;
                if (typeof on !== 'boolean' || !vm.status.install || on === vm.status.install.enabled || vm.isSaving) {
                    return null;
                }
                vm.isSaving = true;

                return vm.http().post(API + '/install-report', { enabled: on }, vm.opts()).then(function (r) {
                    if (r && r.data && r.data.install) {
                        vm.status.install = r.data.install;
                    }
                }).catch(function () {
                    vm.status.install = Object.assign({}, vm.status.install, { status: 'Das Speichern hat nicht geklappt.' });
                }).then(function () {
                    vm.isSaving = false;
                });
            },
            saveLicense: function (action) {
                var vm = this;
                vm.isSaving = true;

                return vm.http().post(API + '/license', { action: action, token: action === 'save' ? vm.licenseToken : '' }, vm.opts()).then(function (r) {
                    var d = r && r.data ? r.data : {};
                    vm.licenseMessage = d.message || '';
                    if (d.license) {
                        vm.status.license = d.license;
                    }
                }).catch(function (e) {
                    var d = e && e.response && e.response.data ? e.response.data : {};
                    vm.licenseMessage = d.message || 'Das hat nicht geklappt.';
                }).then(function () {
                    vm.licenseToken = '';
                    vm.isSaving = false;
                });
            }
        }
    };

    var api = { PRODUCT: PRODUCT, MODULE: MODULE, isLink: isLink, detailsUrl: detailsUrl, component: component };
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = api;
    }
    if (typeof Shopware === 'undefined') {
        return;
    }

    Shopware.Component.register(MODULE + '-page', component);
    Shopware.Module.register(MODULE, {
        type: 'plugin',
        name: MODULE,
        title: 'MGD Barrierefrei',
        description: 'Updates, weitere Plugins und Einstellungen',
        color: '#1f2d3d',
        icon: 'regular-cog',
        routes: {
            index: { component: MODULE + '-page', path: 'index', meta: { parentPath: 'sw.settings.index.plugins', privilege: 'system_config:read' } }
        },
        // Einstellungen > Erweiterungen
        settingsItem: [{
            name: MODULE,
            to: MODULE.replace(/-/g, '.') + '.index',
            label: 'MGD Barrierefrei',
            group: 'plugins',
            icon: 'regular-cog',
            privilege: 'system_config:read'
        }]
    });
})(typeof window !== 'undefined' ? window : this);
