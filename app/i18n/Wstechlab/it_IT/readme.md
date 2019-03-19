Use this command to deploy a language changes

``
rm -rf pub/static/frontend; rm -rf var/cache/ var/generation/ var/page_cache/ var/view_preprocessed/; php bin/magento setup:static-content:deploy -f 
``
