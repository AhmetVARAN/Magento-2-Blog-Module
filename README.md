
Bu repo Magento 2 için blog modülü kapsamında post oluşturma, listeleme, silme işlemlerini kapsamaktadır.

## Modül Oluşturma

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

* Ahmet\Blog\registration.php
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

## Frontend

Ön panelde blog içeriklerini görüntüleyebilmek için bir adres tanımlaması yapmamız gerekli.
Bunun için öncelikle moduleName->etc->frontend->routes.xml dosyası oluşturuyoruz.

* Blog\etc\frontend\routes.xml 
```
<?xml version="1.0" ?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:App/etc/routes.xsd">
    <router id="standard">  <!-- for frontend routes-->
        <route frontName="ahmet" id="ahmet">    <!-- id will use for naming at routing frontend layout -->
            <module name="Ahmet_Blog"/>
        </route>
    </router>
</config>
```
Ardından controller oluşturmamız gerekli.
moduleName->Controller->controllerName->action.php

* Blog\Controller\Blog\Index.php 
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
Adreste görüntüleyeceğimiz sayfayı oluşturmak için <br>
moduleName->view->frontend->layout->routeName_controller_action.xml dosyası oluşturmamız gerekli.

* Ahmet\Blog\view\frontend\layout\ahmet_blog_index.xml  
```
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" layout="1column" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <referenceContainer name="content">
        <block class="Ahmet\Blog\Block\Index" name="ahmet_blog_index" template="Ahmet_Blog::index.phtml" />
    </referenceContainer>
</page>
```
Referans verdiğimiz template dosyası için view->frontend->templates->index.phtml dosyası oluşturuyoruz.

* Blog\view\frontend\templates\index.phtml
```
<h1>Blog Contents Will Show In Here</h1>

```
Layoutun çalışması için moduleName->Block->action.php dosyasını oluşturuyoruz.

* Blog\Block\Index.php 
```
<?php

namespace Ahmet\Blog\Block;

class Index extends \Magento\Framework\View\Element\Template
{

}

```
Oluşan adresimiz routeName/controller/action şeklinde olacaktır.
routeName : frontend->routes.xml dosyasında tanımlanmıştı.
Artık adrese erişim sağlayabiliriz.
![frontlayout](https://user-images.githubusercontent.com/102433124/193470997-c16994f0-996f-4e26-a504-526de5db26b6.png)

## Veritabanı Oluşturma

Blog içeriklerimizi oluşturabilmek için veritabanı tablosu oluşturmamız gerekli.
Bunun için moduleName->etc->db_schema.xml dosyası oluşturarak veritabanı tablomuzu tanımlıyoruz.

* Blog\etc\db_schema.xml

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
* veritabanı ismi : ahmet_blog_blog_entity
* post_id
* title
* content
* url_key
* created_at

kolonlarını oluşturduk.

## Admin Panel

Admin panelde blog içerikleri için adres tanımlaması yapmak için  
moduleName->etc->adminhtml->routes.xml dosyası oluşturuyoruz.

* Blog\etc\adminhtml\routes.xml 
```
<?xml version="1.0" ?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:App/etc/routes.xsd">
    <router id="admin">     <!-- for admin routes-->
        <route frontName="ahmet_blog" id="ahmet_blog">      <!-- id will use for naming at routing admin layout -->
            <module name="Ahmet_Blog"/>
        </route>
    </router>
</config>

```
<b>NOT</b> router id="admin" satırı layoutun admin panele ait olduğunu göstermektedir.

Ardından adres yönlendirmesi için <br>moduleName->Controller->adminhtml->controllerName->action.php şeklinde controller dosyasını oluşturuyoruz.<br>

* Blog\Controller\adminhtml\post\Index.php
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

Ardından oluşturduğumuz adrese erişmek admin panelde görünecek bir menü oluşturuyoruz.

* etc\adminhtml\menu.xml
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

![3](https://user-images.githubusercontent.com/102433124/193471095-412da8fa-3abc-4f6b-919a-e3f25e372d70.png)
![2](https://user-images.githubusercontent.com/102433124/193471097-f14e804b-a9df-4294-b05d-1ef8b818159c.png)


Veritabanı içeriklerine ulaşabilmek için etc\di.xml dosyası oluşturuyoruz.

* etc\di.xml 

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

di.xml dosyasında belirttiğimiz model kaynaklarına ulaşabilmek için sırasıyla aşağıdaki dosyaları oluşturuyoruz.

* Blog\Model\Blog.php

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

* Blog\Model\ResourceModel\Blog.php

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

* Blog\Model\ResourceModel\Blog\Collection.php

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

Veritabanından çekilen verileri admin panelde görmek için layout belirtiyoruz.

* view\adminhtml\layout\ahmet_blog_post_index.xml

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

Ardından görsele dökebilmek için ve çeşitli eklentiler uygulayabilmek için 

* view\adminhtml\ui_component\ahmet_blog_blog_listing.xml <br>
dosyasını oluşturuyoruz.

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
                                <item name="required-entry" xsi:type=boolean">true</item>
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

![6](https://user-images.githubusercontent.com/102433124/193471084-a31ee85c-3012-4f6b-9b2a-f3d5a52602a1.png)

## MassDelete İşlemi

Admin panelde listelenen içerikleri silme işlemi yapmak için öncelikle ui_component altındaki xml dosyasında değişiklik yapmamız gerekli.

* Ahmet\Blog\view\adminhtml\ui_component\ahmet_blog_blog_listing.xml

```
<argument name="requestFieldName" xsi:type="string">id</argument>
```
satırını
```
<argument name="requestFieldName" xsi:type="string">post_id</argument>

```
olarak değiştiriyoruz.

Böylelikle oluşturacağımız controllerda işlem için gerekli olan istek alanının 'post_id' olduğunu belirtmiş oluyoruz.

Ardından silme işlemi için controller oluşturmak gerekli.

* Ahmet\Blog\Controller\adminhtml\post\MassDelete.php
```
<?php

namespace Ahmet\Blog\Controller\adminhtml\post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Ahmet\Blog\Model\ResourceModel\Blog\CollectionFactory;

class MassDelete extends Action
{
    public $collectionFactory;

    public $filter;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            $count = 0;
            foreach ($collection as $model) {

                $model->delete();
                $count++;
            }
            $this->messageManager->addSuccess(__('A total of %1 blog(s) have been deleted.', $count));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}

```

```
$this->filter->getCollection($this->collectionFactory->create());
```
satırı ile tüm blog içeriklerini alıyoruz ve getCollection metodu ile eylem filtrelemesinden geçiriyoruz.
Böylelikle silme işlemi gerçekleşirken seçilen tüm satırlar işleme tabi tutulacaktır.
Her model yenilenerek teker teker silinmektedir.

MassDelete işlemine ait ekran görüntüsü

![massDelete](https://user-images.githubusercontent.com/102433124/193725312-efe390f3-f839-4582-ba86-09c601a08950.png)

## Tekli Silme İşlemi

Çoklu veri silme haricinde tekli veri silme işlemi de gerekebileceği için uygulanması gereken 3 adım vardır.

1. Grid üzerine silme eylemi eklemek
2. DeleteAction metodu oluşturmak
3. Delete Controller oluşturmak

İlk olarak ```Ahmet\Blog\view\adminhtml\ui_component\ahmet_blog_blog_listing.xml``` dosyasında silme eylemi için alan ekliyoruz.

```
<actionsColumn name="delete_action" class="Ahmet\Blog\Ui\Component\Listing\Columns\DeleteAction">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="indexField" xsi:type="string">post_id</item>
                    <item name="viewUrlPath" xsi:type="string">ahmet_blog/post/delete</item>
                    <item name="urlEntityParamName" xsi:type="string">post_id</item>
                    <item name="sortOrder" xsi:type="number">50</item>
                </item>
            </argument>
            <settings>
                <label translate="true">Action</label>
            </settings>
</actionsColumn>  
```

```
<actionsColumn name="delete_action" class="Ahmet\Blog\Ui\Component\Listing\Columns\DeleteAction">
```
satırı silme işlemi için gerekli sınıfı referans göstermektedir.

```
post_id
```
silme işleminde referans alınacak değeri işaret etmektedir.

2. DeleteAction Sınıfı Oluşturmak 

* app/code/Ahmet/Blog/Ui/Component/Listing/Columns/DeleteAction.php
dosyasını oluşturuyoruz.

```
<?php
namespace Ahmet\Blog\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class DeleteAction extends \Magento\Ui\Component\Listing\Columns\Column
{
    public $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['post_id'])) {
                    $viewUrlPath = $this->getData('config/viewUrlPath');
                    $urlEntityParamName = $this->getData('config/urlEntityParamName');
                    $item[$this->getData('name')] = [
                        'view' => [
                            'href' => $this->urlBuilder->getUrl(
                                $viewUrlPath,
                                [
                                    $urlEntityParamName => $item['post_id'],
                                ]
                            ),
                            'label' => __('Delete'),
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}

```

prepareDataSource fonksiyonu ile satır satır veriler dolaşılmaktadır.
```
$dataSource['data']['items']
```
satırı ile her bir veri için ```[ ‘view’ => [ ‘href’ => ‘#’ , ‘label’ => ‘Link’ ] ]``` formatında url ayarlanmaktadır.

```getData()``` fonksiyonu etiketteki ui bileşeninden iletilen değerleri almayı sağlamaktadır.

3. Controller Oluşturmak

```Ui/Component/Listing/Columns/DeleteAction.php``` dosyası için Delete Controller oluşturmak gerekmektedir.

```
<?php

namespace Ahmet\Blog\Controller\adminhtml\post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{
    public $blogFactory;

    public function __construct(
        Context $context,
        \Ahmet\Blog\Model\BlogFactory $blogFactory
    ) {
        $this->blogFactory = $blogFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('post_id');
        try {
            $blogModel = $this->blogFactory->create();
            $blogModel->load($id);
            $blogModel->delete();
            $this->messageManager->addSuccessMessage(__('You deleted the blog.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ahmet_Blog::delete');
    }
}

```

```post_id``` bilgisi ```getParam('post_id')``` ile alınmakta ve model yüklenmektedir.Olası başarı yada hata durumlarına karşılık mesaj eklendi ve silme işleminden sonra sayfa tekrar listeleme sayfasına yönlendirildi.

Tekli silme işlemine ait ekran görüntüsü aşağıda yer almaktadır.

![singleDelete](https://user-images.githubusercontent.com/102433124/193952582-84d6bb86-0c4d-480c-845a-4717e6fddafe.png)

## Form ile Blog Kaydetme

Admin panelde blog içeriği kaydedebilmek için öncelikle blog index sayfasından controllera path vermemiz gerekli.Bunun için ```ahmet_blog_blog_listing.xml``` sayfasında oluşturacağımız controller için yol veriyoruz.

Ardından app/code/Ahmet/Blog/Controller/adminhtml/post/ altında ```AddRow.php``` isimli controller oluşturuyoruz.

* app/code/Ahmet/Blog/Controller/adminhtml/post/AddRow.php

```
<?php

namespace Ahmet\Blog\Controller\adminhtml\post;
use Magento\Framework\Controller\ResultFactory;
class AddRow extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Ahmet\Blog\Model\BlogFactory
     */
    private $blogFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Ahmet\Blog\Model\BlogFactory $blogFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ahmet\Blog\Model\BlogFactory $blogFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->blogFactory = $blogFactory;
    }

    /**
     * Mapped Blog List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->blogFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getTitle();
            if (!$rowData->getPostId()) {
                $this->messageManager->addError(__('row data no longer exist.'));
                $this->_redirect('ahmet/blog/rowdata');
                return;
            }
        }

        $this->coreRegistry->register('row_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Row Data ').$rowTitle : __('Add Post');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ahmet_blog::add_row');
    }
}

```

Ardından app/code/Ahmet/Blog/view/adminhtml/layout/ altında ```ahmet_blog_post_addrow.xml```  dosyasını oluşturuyoruz.

* app/code/Ahmet/Blog/view/adminhtml/layout/ahmet_blog_post_addrow.xml
```
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" layout="admin-1column"
      xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="content">
            <block class="Ahmet\Blog\Block\Adminhtml\Blog\AddRow" name="add_row" />
        </referenceContainer>
    </body>
</page>

```

Sonraki adımda app/code/Ahmet/Blog/Block/Adminhtml/Blog/ altında ```AddRow.php``` dosyasını oluşturuyoruz. Böylelikle oluşturacağımız form ile verileri alabileceğiz.

* app/code/Ahmet/Blog/Block/Adminhtml/Blog/AddRow.php

```
<?php

namespace Ahmet\Blog\Block\adminhtml\Blog;

class AddRow extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Imagegallery Images Edit Block.
     */

    protected function _construct()
    {
        $this->_objectId = 'row_id';
        $this->_blockGroup = 'Ahmet_Blog';
        $this->_controller = 'adminhtml_blog';
        parent::_construct();
        if ($this->_isAllowedAction('Ahmet_Blog::add_row')) {
            $this->buttonList->update('save', 'label', __('Save'));
        } else {
            $this->buttonList->remove('save');
        }
        $this->buttonList->remove('reset');
    }

    /**
     * Retrieve text for header element depending on loaded image.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Add Post Data');
    }

    /**
     * Check permission for passed action.
     *
     * @param string $resourceId
     *
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Get form action URL.
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }

        return $this->getUrl('*/*/save');
    }
}

```

Verileri alacağımız formu oluşturmak için app/code/Ahmet/Blog/Block/Adminhtml/Blog/Edit/ altında ```Form.php``` dosyasını oluşturuyoruz. Bu dosyada kayıt formunda olmasını istediğimiz alanları oluşturuyoruz.

* app/code/Ahmet/Blog/Block/Adminhtml/Blog/Edit/Form.php
```
<?php
namespace Ahmet\Blog\Block\Adminhtml\Blog\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form',
                'enctype' => 'multipart/form-data',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
            ]
        );

        $form->setHtmlIdPrefix('smb_');
        if ($model->getPostId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Post Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('post_id', 'hidden', ['name' => 'post_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Post Data'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'id' => 'title',
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'content',
            'text',
            [
                'name' => 'content',
                'label' => __('Content'),
                'id' => 'content',
                'title' => __('Content'),
                'class' => 'required-entry',
                'required' => false,
            ]
        );
        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('Url Key'),
                'id' => 'url_key',
                'title' => __('Url Key'),
                'class' => 'required-entry',
                'required' => false,
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

```

Son olarak kayıt işlemini gerçekleştirmek için app/code/Ahmet/Blog/Controller/adminhtml/post/ altında ```Save.php``` dosyasını oluşturuyoruz.

* app/code/Ahmet/Blog/Controller/adminhtml/post/Save.php

```
<?php

namespace Ahmet\Blog\Controller\adminhtml\post;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ahmet\Blog\Model\BlogFactory
     */
    var $blogFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ahmet\Blog\Model\BlogFactory $blogFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ahmet\Blog\Model\BlogFactory $blogFactory
    ) {
        parent::__construct($context);
        $this->blogFactory = $blogFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('ahmet/blog/addrow');
            return;
        }
        try {
            $rowData = $this->blogFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setPostId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Post has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('ahmet/blog/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ahmet_Blog::save');
    }
}

```
Yeni post kayıt işlemine ait görsel aşağıda yer almaktadır.

![image](https://user-images.githubusercontent.com/102433124/194035921-1b081784-a145-48f3-a6b5-4aea8abdc1ca.png)
![image](https://user-images.githubusercontent.com/102433124/194036302-31a9755a-5c3f-4ac7-ba05-b74dcc549849.png)

## Frontend Blog Listeleme

Blog içeriklerini ön panel de göstermek için öncelikle moduleName->Block->action.php dosyasını düzenliyoruz.

* Blog\Block\Index.php dosyası 

```
<?php

namespace Ahmet\Blog\Block;

class Index extends \Magento\Framework\View\Element\Template
{
    protected BlogFactory $blogFactory;

    public function __construct(
        BlogFactory $blogFactory,
        Template\Context $context, array $data = [])
    {
        $this->blogFactory = $blogFactory;
        parent::__construct($context, $data);
    }
    
    //Function for getting blog data
    public function getBlogEntities(){
        $blog = $this->blogFactory->create();
        $colletcion = $blog->getCollection();
        if ($colletcion){
            return $colletcion;
        }
        return [];
    }
}
```
Factory design pattern yapısı kullanarak ```getBlogEntities()``` fonksiyonu ile veritabanından veri çekiyoruz.

Çektiğimiz verileri ön panelde göstermek için moduleName->view->frontend->templates->index.phtml dosyasını düzenliyoruz.

* Blog\view\frontend\templates\index.phtml
 
```
<?php
/** @var $block Ahmet\Blog\Block\Index */
?>

<style>
    table {  font-family: arial, sans-serif;  border-collapse: collapse;  with: 100%;  margin-top: 30px;}
    td, th {  border: 1px solid #dddddd;  text-align: left;  padding: 8px;  }
    tr:nth-child(even) { background-color: #dddddd; }
    .post-id{width:2%} .post-name{width:30%}

</style>

<table>
    <tr>
        <th class="post-id">Id</th>
        <th class="post-name">Title</th>
        <th>Content</th>
        <th>Url_Key</th>
        <th>Created Time</th>
    </tr>
    <?php
    foreach ($block->getBlogEntities() as $key=>$post){
        echo '<tr>
                    <td>'.$post->getPostId().'</td>
                    <td>'.$post->getTitle().'</td>
                    <td>'.$post->getContent().'</td>
                    <td>'.$post->getUrlKey().'</td>
                    <td>'.$post->getCreatedAt().'</td>
                  </tr>';
    }
    ?>
</table>
```
Verilerin ön panelde gösterimi görselde yer almaktadır.
![5](https://user-images.githubusercontent.com/102433124/193470968-794dafdc-cac6-4da8-ab1a-bc9239ded90d.png)
