Configuration Reference
=======================

Here you will find all configuration options of `abenmada_export_plugin`.

```yaml
abenmada_export_plugin:
    resource:
        sylius_admin_customer:
            model: sylius.customer
            download:
                type: xlsx # default xlsx (could be xlsx, xls, csv, pdf, ods, html)
                enabled: true # default true
                auto_size: true # default true
                prefix_timestamp: false # default false
                suffix_timestamp: false # default false
                file_name: export_customer # default export
                metadata:
                    creator: Ayman Benmada
                    lastModifiedBy: Ayman Benmada
                    title: The latest customers
                    subject: Registration
                    description: The last six customers registered on the shop
                    keywords: export sylius customer
                    category: Customer
                    manager: Ayman Benmada
                    company: Abenmada
                style:
                    size: 11
                security:
                    enabled: true # default false
                    password: 123
            save:
                type: csv
                enabled: false
                auto_size: true
                prefix_timestamp: true
                suffix_timestamp: false
                file_name: export_customer_save
                path: public/media
                metadata:
                    creator: Ayman Benmada
                    lastModifiedBy: Ayman Benmada
                    title: The latest customers
                    subject: Registration
                    description: The last six customers registered on the shop
                    keywords: export sylius customer
                    category: Customer
                    manager: Ayman Benmada
                    company: Abenmada
                style:
                    size: 11
                security:
                    enabled: true
                    password: 123
            repository:
                method: findLatest # default findAll (could return QueryBuilder or collection or array)
                arguments:
                    - 6
            properties:
                firstName:
                    label: sylius.ui.first_name
                lastName:
                    label: sylius.ui.last_name
                    position: 2
                email:
                    label: sylius.ui.email
                phoneNumber:
                    label: sylius.ui.phone_number
                    enabled: false # default true
                createdAt:
                    label: sylius.ui.registration_date
                    options:
                        format: d-m-Y H:i # could be used when the getter return DateTime
                enabled:
                    label: sylius.ui.enabled
                    path: getUser().isEnabled() # will return empty value if one of the getters return null
                verified:
                    label: sylius.ui.verified
                    path: getUser().isVerified()
