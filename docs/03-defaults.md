# Defaults

```yaml
pimcore_monitor:
    checks:
        app_environment:
            enabled: true
            skip: false
            environment: '%kernel.environment%'
        disk_usage:
            enabled: true
            skip: false
            warning_threshold: 90 # percentage
            critical_threshold: 95 # percentage
            path: '%kernel.project_dir%'
        doctrine_migrations:
            enabled: true
            skip: false
        hosting_size:
            enabled: true
            skip: false
            warning_threshold: 48318382080 # 45 GB
            critical_threshold: 53687091200 # 50 GB
            path: '%kernel.project_dir%'
        database_size:
            enabled: true
            skip: false
            warning_threshold: 964689920 # 920 MB
            critical_threshold: 1073741824 # 1 GB
        database_table_size:
            enabled: true
            skip: false
            warning_threshold: 94371840 # 90 MB
            critical_threshold: 104857600 # 100 MB
        https_connection:
            enabled: true
            skip: false
        mysql_version:
            enabled: true
            skip: false
            version: '10.5'
            operator: '>='
        php_version:
            enabled: true
            skip: false
            version: '8.0'
            operator: '>='
        pimcore_areabricks:
            enabled: true
            skip: false
        pimcore_bundles:
            enabled: true
            skip: false
        pimcore_element_count:
            enabled: true
            skip: false
            warning_threshold: 100000
            critical_threshold: 150000
        pimcore_maintenance:
            enabled: true
            skip: false
        pimcore_users:
            enabled: true
            skip: false
        pimcore_version:
            enabled: true
            skip: false
```
