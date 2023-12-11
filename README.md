# Zero1_StoresCli

This module aims to extend the default store module to allow creation of stores from the CLI.
The main reason for this is that creating a store can take a long time, and any iteruption during this process can cause issues (such as missing sequence tables), running from the CLI wil reduce this risk.

This module also supplies a "fix" command that should re-trigger the original Magento logic and create any missing sequence tables.
(Thanks to  [@andrewhowdencom](https://github.com/andrewhowdencom): [Magento issue 12318](https://github.com/magento/magento2/issues/12318))

As with all commands, we strongly recommend runng away from production initially to test the outcome.

## Create a new store
```bash
php bin/magento store:store:create --group-id=10 --name="New Store" --code=new_store
```

## Fix a store
```bash
php bin/magento store:store:fix --store-id=9
```
