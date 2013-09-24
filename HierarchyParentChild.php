<?php
namespace MyLib;

/**
 * Class for constructing a hierarchy of parent child.
 *
 * Here is an inline example:
 * <code>
 * $tree = new HierarchyParentChild($inputArray);
 * $tree->getHierarchy($inputArray);
 * </code>
 *
 * @package  MyLib
 * @author   Aleksandr Markovskiy <shakal3351@gmail.com>
 * @see      http://www.example.com/pear
 */
Class HierarchyParentChild
{
    private $_data;

    private $_fieldName = array(
        'id'       => 'id',
        'parentId' => 'parentId',
        'children' => 'children',
    );

    public function __construct($array)
    {
        if (is_array($array) && !empty($array)) {
            $this->_data = $array;
        } else {
            throw new \InvalidArgumentException('Invalid input data must be not an empty array, received:' . gettype($array) . '');
        }
    }

    /**
     * The function returns a multidimensional array corresponding to the hierarchy of parent child.
     *
     * @return array|bool
     */
    public function getHierarchy()
    {
        $flat = $this->_createFlat();

        $return = false;

        if (is_array($flat) && !empty($flat)) {

            foreach ($flat as &$child) {
                if (!isset($child[$this->_fieldName['children']])) {
                    $child[$this->_fieldName['children']] = array();
                }

                if (isset($child[$this->_fieldName['id']])) {
                    $id = $child[$this->_fieldName['id']];
                }

                if (isset($child[$this->_fieldName['parentId']])) {
                    $pid = $child[$this->_fieldName['parentId']];
                }

                if (isset($pid) && $pid > 0) {
                    $flat[$pid][$this->_fieldName['children']][] = &$child;
                } else {
                    if (isset($id)) {
                        $return[$id] = &$child;
                    }
                }
            }

            if (isset($child)) {
                unset($child);
            }

        }

        return $return;
    }

    /**
     * Setting function corresponding field names in the array.
     *
     * @param $array
     */
    public function setFieldName($array)
    {
        $this->_fieldName = $array;
    }

    /**
     * The function prepares the array to build a hierarchy.
     *
     * @return array|bool
     */
    private function _createFlat()
    {
        $count = count($this->_data);

        $flat = array();

        for ($i = 0; $i < $count; $i++) {
            if (isset($this->_data[$i])) {
                $new = $this->_data[$i];
            }

            if (isset($new[$this->_fieldName['id']])) {
                $id = $new['id'];
            }

            if (isset($id)) {
                $flat[$id] = $new;
            }
        }

        return !empty($flat) ? $flat : false;
    }
}