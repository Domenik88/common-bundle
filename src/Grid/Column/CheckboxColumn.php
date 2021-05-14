<?php
namespace KMGi\CommonBundle\Grid\Column;

use APY\DataGridBundle\Grid\Column\BooleanColumn;

class CheckboxColumn extends BooleanColumn
{
    /**
     * {@inheritDoc}
     */
    public function renderCell($value, $row, $router)
    {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'checkbox';
    }
}
