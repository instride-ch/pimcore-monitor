pimcore.registerNS('pimcore.plugin.PimcoreMonitorBundle');

pimcore.plugin.PimcoreMonitor = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return 'pimcore.plugin.PimcoreMonitorBundle';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function () {
        const user = pimcore.globalmanager.get('user');

        if (user.admin) {
            const systemHealthStatus = new Ext.Action({
                text: t('pimcore_monitor_system_health_status'),
                iconCls: 'pimcore_monitor_nav_icon_health_status',
                handler: this.openSystemHealthStatusPage,
            });

            layoutToolbar.extrasMenu.add(systemHealthStatus);
        }
    },

    openSystemHealthStatusPage: function () {
        const systemHealthStatusPanelId = 'pimcore_monitor_system_health_status';

        try {
            pimcore.globalmanager.get(systemHealthStatusPanelId).activate();
        } catch (e) {
            pimcore.globalmanager.add(
                systemHealthStatusPanelId,
                new pimcore.tool.genericiframewindow(
                    systemHealthStatusPanelId,
                    Routing.generate('pimcore_monitor_system_health_status'),
                    'pimcore_monitor_nav_icon_health_status',
                    t('pimcore_monitor_system_health_status')
                )
            );
        }
    },
});

const PimcoreMonitorPlugin = new pimcore.plugin.PimcoreMonitor();
