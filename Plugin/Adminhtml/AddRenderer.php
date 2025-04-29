<?php

namespace PinBlooms\OrderItemsImage\Plugin\Adminhtml;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class AddRenderer
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;
    /**
     * @var \PinBlooms\OrderItemsImage\Helper\Image
     */
    protected $_imageHelper;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;
    /**
     * @var RequestInterface
     */
    protected $_request;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Constructor for AddRenderer plugin.
     *
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param \PinBlooms\OrderItemsImage\Helper\Image $imageHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \PinBlooms\OrderItemsImage\Helper\Image $imageHelper,
        OrderRepositoryInterface $orderRepository,
        RequestInterface $request,
        ProductRepositoryInterface $productRepository
    ) {
        $this->backendHelper = $backendHelper;
        $this->_coreRegistry = $registry;
        $this->_imageHelper = $imageHelper;
        $this->_orderRepository = $orderRepository;
        $this->_request = $request;
        $this->productRepository = $productRepository;
    }

    /**
     * Modify the columns array by adding a new column for images.
     *
     * @param mixed $defaultRenderer The default renderer.
     * @param array $result The original columns array.
     * @return array Modified columns array with an added image column.
     */
    public function afterGetColumns($defaultRenderer, $result)
    {
        if (is_array($result)) {
            $newResult['image'] = 'col-image';
            foreach ($result as $key => $value) {
                $newResult[$key] = $value;
            }
            $result = $newResult;
        }
        return $result;
    }

    /**
     * Prepare data before rendering the column HTML.
     *
     * @param mixed $defaultRenderer The default renderer.
     * @param \Magento\Framework\DataObject $item The data object for the current row.
     * @param string $column The column name.
     * @param string|null $field Optional field name.
     * @return array Modified arguments for the column rendering.
     */
    public function beforeGetColumnHtml($defaultRenderer, \Magento\Framework\DataObject $item, $column, $field = null)
    {
        $html = '';
        switch ($column) {
            case 'image':
                $this->_coreRegistry->register('is_image_renderer', 1);
                $this->_coreRegistry->register('pinblooms_current_order_item', $item);
                break;
        }
        return [$item, $column, $field];
    }

    /**
     * Modify the column HTML after it is generated.
     *
     * @param mixed $defaultRenderer The default renderer.
     * @param string $result The original column HTML.
     * @return string Modified column HTML.
     */
    public function afterGetColumnHtml($defaultRenderer, $result)
    {
        $is_image = $this->_coreRegistry->registry('is_image_renderer');
        $currentItem = $this->_coreRegistry->registry('pinblooms_current_order_item');
        $this->_coreRegistry->unregister('is_image_renderer');
        $this->_coreRegistry->unregister('pinblooms_current_order_item');

        if ($is_image == 1 && $currentItem) {
            // Retrieve the product from the current item
            $product = $currentItem->getProduct();
            if ($product) {
                if ($product->getTypeId() == Configurable::TYPE_CODE) {
                    // For configurable products, get the associated simple product
                    $simpleProduct = $this->getChildProduct($currentItem);
                    if ($simpleProduct) {
                        return $this->renderImage($simpleProduct);
                    }
                } else {
                    return $this->renderImage($product);
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve the associated simple product for a configurable product item.
     *
     * @param \Magento\Framework\DataObject $item The order item object.
     * @return \Magento\Catalog\Api\Data\ProductInterface|null The simple product or null if not found.
     */
    protected function getChildProduct($item)
    {
        $options = $item->getProductOptions();
        if (isset($options['simple_sku'])) {
            $simpleSku = $options['simple_sku'];
            return $this->productRepository->get($simpleSku);
        }
        return null;
    }

    /**
     * Render the HTML for the product image.
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product The product object.
     * @return string|null The HTML string for the product image or null if no image is found.
     */
    protected function renderImage($product)
    {
        // Load and display the image for the product
        $this->_imageHelper->addGallery($product);
        $images = $this->_imageHelper->getGalleryImages($product);

        foreach ($images as $image) {
            $item = $image->getData();
            if (isset($item['media_type']) && $item['media_type'] == 'image') {
                $imgPath = isset($item['small_image_url']) ? $item['small_image_url'] : null;
                return "<a href='" . $this->backendHelper->getUrl(
                    'catalog/product/edit',
                    ['id' => $product->getId()]
                ) . "' target='_blank'><img src='" . $imgPath . "' alt='" . $product->getName() . "'></a>";
            }
        }

        return null;
    }
}
