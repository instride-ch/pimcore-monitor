class PimcoreMonitor {
    init() {
        const user = pimcore.globalmanager.get('user');

        if (user.admin) {
            const systemHealthStatus = new Ext.Action({
                id: 'pimcore_monitor',
                text: t('pimcore_monitor_system_health_status'),
                iconCls: 'pimcore_monitor_nav_icon_health_status',
                handler: this.openSystemHealthStatusPage.bind(this),
            });

            if (layoutToolbar.extrasMenu) {
                layoutToolbar.extrasMenu.add(systemHealthStatus);
            }
        }
    }

    openSystemHealthStatusPage() {
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
    }
}

const pimcoreMonitorHandler = new PimcoreMonitor();

document.addEventListener(pimcore.events.pimcoreReady, pimcoreMonitorHandler.init.bind(pimcoreMonitorHandler));
