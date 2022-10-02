
Bu repo Magento 2 için blog modülü hazırlamayı kapsamaktadır.

Bir modülün çalışabilmesi için mutlaka olması gereken 2 dosya vardır.
* registration.php
* module.xml

VendorName->ModuleName->etc->module.xml dosyasında modül ismini belirtiyoruz.
```
<?xml version="1.0"?>

<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Ahmet_Blog" />
</config>

```
VendorName->ModuleName->registration.php dosyasında modülümüzü tanımlıyoruz.
```
<?php
    /**
    * Copyright © Magento, Inc. All rights reserved.
    * See COPYING.txt for license details.
    */

    use Magento\Framework\Component\ComponentRegistrar;

    ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Ahmet_Blog', __DIR__);

```

Modülü oluşturduktan aktif hale getirmek için 
```php bin/magento module:enable VendorName_ModuleName```
komutunu çalıştırıyoruz.
![img.png](img.png)

Ön panelde blog içeriklerini görüntüleyebilmek için bir adres tanımlaması yapmamız gerekli.
Bunun için öncelikle moduleName->etc->frontend->routes.xml dosyası oluşturuyoruz.
```
<?xml version="1.0" ?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:App/etc/routes.xsd">
    <router id="standard">
        <route frontName="ahmet" id="ahmet">
            <module name="Ahmet_Blog"/>
        </route>
    </router>
</config>
```
Ardından adres yolunu tanımlamak için controller oluşturmamız gerekli.
moduleName->Controller->controllerName->action.php
Blog->Controller->Blog->Index.php dosyasını oluşturuyoruz.
```
<?php

namespace Ahmet\Blog\Controller\Blog;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        return $this->_pageFactory->create();
    }
}
```

Oluşan adresimiz routeName/controller/action şeklinde olacaktır.
routeName frontend->routes.xml dosyasında tanımlanmıştı.
Artık adrese erişim sağlayabiliriz.

<<<<<<< HEAD
![img_1.png](img_1.png)![](C:\Users\a\Desktop\1.png)

=======
![image](https://user-images.githubusercontent.com/102433124/193461232-0eb2d047-d51e-4972-9744-1d9d66a25932.png)
"# Magento-Blog-Module" 
>>>>>>> origin/main
