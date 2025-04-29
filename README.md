# Magento 2 - PinBlooms Order Items Image

## Overview
**PinBlooms_OrderItemsImage** is a Magento 2 custom module that enhances the order detail view in the admin panel by displaying product images. It supports both simple products and child items of configurable products, making it easier for store admins to visually identify ordered items.

## Features
- Adds product images to the "Items Ordered" section of the order view page.
- Works with simple products and child products of configurables.
- Requires no configuration — just install and enable the module.

## Installation
### 1. Download and Extract
Clone or download the module into your Magento 2 installation:
```sh
cd <magento_root>/app/code/PinBlooms/OrderItemsImage
```

### 2. Enable the Module
Run the following commands to enable and set up the module:
```sh
php bin/magento module:enable PinBlooms_OrderItemsImage
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```
### 3. Usage
After a successful installation, you can find this module under:
- Navigate to Admin > Sales > Orders.
- Click View on any order.
- Product images will be visible in the Items Ordered section.

  ![image (25)](https://github.com/user-attachments/assets/08af0510-a0e5-430b-b81d-35e780ffd04a)

### 4. Support
For issues or feature requests, please create a GitHub issue or contact us at https://pinblooms.com/contact-us/.

### 5. License
This module is released under the MIT License.
