<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace PinBlooms\OrderItemsImage\Helper;

use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Handles reading the product's image gallery
     *
     * @var \Magento\Catalog\Model\Product\Gallery\ReadHandler
     */
    protected $galleryReadHandler;
    /**
     * Catalog Image Helper
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;
    /**
     * Constructor
     *
     * @param GalleryReadHandler $galleryReadHandler Handles reading the product's image gallery
     * @param \Magento\Framework\App\Helper\Context $context Context of the helper
     * @param \Magento\Catalog\Helper\Image $imageHelper Catalog image helper
     */
    public function __construct(
        GalleryReadHandler $galleryReadHandler,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Helper\Image $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
        $this->galleryReadHandler = $galleryReadHandler;
        parent::__construct($context);
    }

    /**
     * Adds the gallery images to the product
     *
     * @param \Magento\Catalog\Model\Product $product The product to which the gallery images will be added
     * @return void
     */
    public function addGallery($product)
    {
        $this->galleryReadHandler->execute($product);
    }
    /**
     * Retrieves the gallery images for the given product and adds small image URLs.
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product The product whose gallery images are to be retrieved
     * @return \Magento\Framework\Data\Collection|null The collection of gallery images or null if not available
     */
    public function getGalleryImages(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $images = $product->getMediaGalleryImages();
        if ($images instanceof \Magento\Framework\Data\Collection) {
            foreach ($images as $image) {
                /** @var $image \Magento\Catalog\Model\Product\Image */
                $image->setData(
                    'small_image_url',
                    $this->imageHelper->init($product, 'product_page_image_small')
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
            }
        }
        return $images;
    }
}
