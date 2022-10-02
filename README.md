
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
Ardından controller oluşturmamız gerekli.
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
Sayfamıza template giydirmek için view->frontend->layout->routeName_controller_action.xml dosyası oluşturmamız gerekli.
```
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" layout="1column" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <referenceContainer name="content">
        <block class="Ahmet\Blog\Block\Index" name="ahmet_blog_index" template="Ahmet_Blog::index.phtml" />
    </referenceContainer>
</page>

```
Referans verdiğimiz template dosyası için view->frontend->template->index.phtml dosyası oluşturuyoruz.
```
<h1>Blog Contents Will Show In Here</h1>

```
Layoutun çalışması için moduleName->Block->action.php dosyasını oluşturuyoruz.
```
<?php

namespace Ahmet\Blog\Block;

class Index extends \Magento\Framework\View\Element\Template
{

}

```
Oluşan adresimiz routeName/controller/action şeklinde olacaktır.
routeName frontend->routes.xml dosyasında tanımlanmıştı.
Artık adrese erişim sağlayabiliriz.

![image](https://user-images.githubusercontent.com/102433124/193461232-0eb2d047-d51e-4972-9744-1d9d66a25932.png)

Blog içeriklerimizi oluşturabilmek için veritabanı tablosu oluşturmamız gerekli.
Bunun için etc->db_schema.xml dosyası oluşturarak veritabanı tablomuzu tanımlıyoruz.

```
<schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd">
    <table name="ahmet_blog_blog_entity" resource="default" engine="innodb" comment="Blog Entity">
        <column xsi:type="int" name="post_id" unsigned="false" nullable="false" identity="true" comment="Post ID"/>
        <column xsi:type="varchar" name="title" nullable="false" length="255" comment="Post Title"/>
        <column xsi:type="text" name="content" nullable="true"  comment="Content Area"/>
        <column xsi:type="text" name="url_key" nullable="true"  comment="Url Key for Post" />
        <column xsi:type="timestamp" name="created_at"  nullable="true" default="CURRENT_TIMESTAMP" on_update="false" comment="Created Date" />
        <constraint xsi:type="primary" referenceId="PRIMARY">
            <column name="post_id"/>
        </constraint>
    </table>
</schema>

```
Admin panel için adres tanımlaması yapmak için etc->adminhtml->routes.xml dosyası oluşturuyoruz.
```
<?xml version="1.0" ?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:App/etc/routes.xsd">
    <router id="admin">
        <route frontName="ahmet_blog" id="ahmet_blog">
            <module name="Ahmet_Blog"/>
        </route>
    </router>
</config>

```
Controller->adminhtml->controllerName->action.php şeklinde controller dosyasını oluşturuyoruz.
```
<?php

namespace Ahmet\Blog\Controller\adminhtml\post;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Posts')));

        return $resultPage;
    }
}

```

Ardından oluşturduğumuz adrese erişmek için bir menü oluşturuyoruz.

etc->adminhtml->menu.xml
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Backend:etc/menu.xsd">
    <menu>
        <add id="Ahmet_Blog::ahmet" title="Ahmet" module="Ahmet_Blog" sortOrder="1" resource="Ahmet_Blog::ahmet"/>
        <add id="Ahmet_Blog::blog" title="Blog" module="Ahmet_Blog" sortOrder="10" action="ahmet_blog/post" resource="Ahmet_Blog::post" parent="Ahmet_Blog::ahmet"/>
    </menu>
</config>
```
Oluşan menü ve sayfa görünümü aşağıda yer almaktadır.
![](C:\Users\a\Desktop\3.png)
![](C:\Users\a\Desktop\2.png)

Veritabanı içeriklerine ulaşabilmek için etc->di.xml dosyası oluşturuyoruz.

```
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../lib/internal/Magento/Framework/ObjectManager/etc/config.xsd">
    <type name="Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory">
        <arguments>
            <argument name="collections" xsi:type="array">
                <item name="ahmet_blog_blog_listing_data_source" xsi:type="string">Ahmet\Blog\Model\ResourceModel\Blog\Grid\Collection</item>
            </argument>
        </arguments>
    </type>
    <virtualType name="Ahmet\Blog\Model\ResourceModel\Blog\Grid\Collection" type="Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult">
        <arguments>
            <argument name="mainTable" xsi:type="string">ahmet_blog_blog_entity</argument>
            <argument name="resourceModel" xsi:type="string">Ahmet\Blog\Model\ResourceModel\Blog</argument>
        </arguments>
    </virtualType>
</config>
```

di.xml dosyasında belirttiğimiz model kaynaklarına ulabilmek için sırasıyla aşağıdaki dosyaları oluşturuyoruz.
* moduleName->Model->Blog.php
```
<?php

namespace Ahmet\Blog\Model;

class Blog extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'ahmet_blog_blog_entity';

    protected $_cacheTag = 'ahmet_blog_blog_entity';

    protected $_eventPrefix = 'ahmet_blog_blog_entity';

    protected function _construct()
    {
        $this->_init('Ahmet\Blog\Model\ResourceModel\Blog');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}

```
* moduleName->Model->ResourceModel->Blog.php
```
<?php

namespace Ahmet\Blog\Model\ResourceModel;

class Blog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('ahmet_blog_blog_entity', 'post_id');
    }

}

```
* moduleName->Model->ResourceModel->Blog->Collection.php
```
<?php

namespace Ahmet\Blog\Model\ResourceModel\Blog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'post_id';
    protected $_eventPrefix = 'ahmet_blog_blog_entity';
    protected $_eventObject = 'blog_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ahmet\Blog\Model\Blog', 'Ahmet\Blog\Model\ResourceModel\Blog');
    }

}

```

Veritabanından çekilen verileri adminpanelde görmek için layout belirtiyoruz.
view->adminhtml->layout->ahmet_blog_post_index.xml dosyası oluşturuyoruz.

```
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../../../lib/internal/Magento/Framework/View/Layout/etc/page_configuration.xsd">
    <update handle="styles"/>
    <body>
        <referenceContainer name="content">
            <uiComponent name="ahmet_blog_blog_listing"/>
        </referenceContainer>
    </body>
</page>

```
Ardından görsele dökebilmek için view->adminhtml->ui_component->ahmet_blog_blog_listing.xml dosyasını oluşturuyoruz.

```
    <listing xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Ui:etc/ui_configuration.xsd">

        <argument name="data" xsi:type="array">
            <item name="js_config" xsi:type="array">
                <item name="provider" xsi:type="string">ahmet_blog_blog_listing.ahmet_blog_blog_listing_data_source</item>
                <item name="deps" xsi:type="string">ahmet_blog_blog_listing.ahmet_blog_blog_listing_data_source</item>
            </item>
            <item name="spinner" xsi:type="string">spinner_columns</item>
            <item name="buttons" xsi:type="array">
                <item name="add" xsi:type="array">
                    <item name="name" xsi:type="string">add</item>
                    <item name="label" xsi:type="string" translate="true">Add New Post</item>
                    <item name="class" xsi:type="string">primary</item>
                    <item name="url" xsi:type="string">*/*/new</item>
                </item>
            </item>
        </argument>
        <dataSource name="nameOfDataSource">
            <argument name="dataProvider" xsi:type="configurableObject">
                <argument name="class" xsi:type="string">Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider</argument>
                <argument name="name" xsi:type="string">ahmet_blog_blog_listing_data_source</argument>
                <argument name="primaryFieldName" xsi:type="string">post_id</argument>
                <argument name="requestFieldName" xsi:type="string">id</argument>
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="component" xsi:type="string">Magento_Ui/js/grid/provider</item>
                        <item name="update_url" xsi:type="url" path="mui/index/render"/>
                        <item name="storageConfig" xsi:type="array">
                            <item name="indexField" xsi:type="string">post_id</item>
                        </item>
                    </item>
                </argument>
            </argument>
        </dataSource>
        <listingToolbar name="listing_top">
            <!-- export button -->
            <exportButton name="export_button"/>
            <!-- ... other block of code -->
            <massaction name="listing_massaction">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="component" xsi:type="string">Magento_Ui/js/grid/tree-massactions</item>
                    </item>
                </argument>
                <action name="delete">
                    <argument name="data" xsi:type="array">
                        <item name="config" xsi:type="array">
                            <item name="type" xsi:type="string">delete</item>
                            <item name="label" xsi:type="string" translate="true">Delete</item>
                            <item name="url" xsi:type="url" path="ahmet_blog/post/massDelete"/>
                            <item name="confirm" xsi:type="array">
                                <item name="title" xsi:type="string" translate="true">Delete Post</item>
                                <item name="message" xsi:type="string" translate="true">Are you sure you wan't to delete selected items?</item>
                            </item>
                        </item>
                    </argument>
                </action>
            </massaction>
        </listingToolbar>

        <columns name="spinner_columns">
            <selectionsColumn name="ids">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="resizeEnabled" xsi:type="boolean">false</item>
                        <item name="resizeDefaultWidth" xsi:type="string">55</item>
                        <item name="indexField" xsi:type="string">post_id</item>
                    </item>
                </argument>
            </selectionsColumn>
            <column name="post_id">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="filter" xsi:type="string">textRange</item>
                        <item name="sorting" xsi:type="string">asc</item>
                        <item name="label" xsi:type="string" translate="true">ID</item>
                    </item>
                </argument>
            </column>
            <column name="title">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="filter" xsi:type="string">text</item>
                        <item name="editor" xsi:type="array">
                            <item name="editorType" xsi:type="string">text</item>
                            <item name="validation" xsi:type="array">
                                <item name="required-entry" xsi:type="boolean">true</item>
                            </item>
                        </item>
                        <item name="label" xsi:type="string" translate="true">Title</item>
                    </item>
                </argument>
            </column>
            <column name="content">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="filter" xsi:type="string">text</item>
                        <item name="editor" xsi:type="array">
                            <item name="editorType" xsi:type="string">text</item>
                            <item name="validation" xsi:type="array">
                                <item name="required-entry" xsi:type="boolean">true</item>
                            </item>
                        </item>
                        <item name="label" xsi:type="string" translate="true">Content</item>
                    </item>
                </argument>
            </column>
            <column name="url_key">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="filter" xsi:type="string">text</item>
                        <item name="editor" xsi:type="array">
                            <item name="editorType" xsi:type="string">text</item>
                            <item name="validation" xsi:type="array">
                                <item name="required-entry" xsi:type="boolean">true</item>
                            </item>
                        </item>
                        <item name="label" xsi:type="string" translate="true">URL</item>
                    </item>
                </argument>
            </column>
            <column name="created_at" class="Magento\Ui\Component\Listing\Columns\Date">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="filter" xsi:type="string">dateRange</item>
                        <item name="component" xsi:type="string">Magento_Ui/js/grid/columns/date</item>
                        <item name="dataType" xsi:type="string">date</item>
                        <item name="label" xsi:type="string" translate="true">Created Date</item>
                    </item>
                </argument>
            </column>
        </columns>
    </listing>

```
Veritabanından çekilen içeriklerin admin panelde gösterimi

![](C:\Users\a\Desktop\4.png)
