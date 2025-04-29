<?php

namespace PinBlooms\OrderItemsImage\Plugin\Adminhtml;

class AddImage
{

    /**
     * Modify the columns to add an image column.
     *
     * @param mixed $items The items being processed.
     * @param array $result The original columns array.
     * @return array Modified columns array with an added image column.
     */
    public function afterGetColumns($items, $result)
    {
        if (is_array($result)) {
            $newResult['image'] = 'Image';
            foreach ($result as $key => $value) {
                $newResult[$key]  = $value;
            }
            $result = $newResult;
        }

        return $result;
    }
}
