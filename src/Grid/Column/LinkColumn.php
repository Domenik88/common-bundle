<?php
namespace KMGi\CommonBundle\Grid\Column;

use APY\DataGridBundle\Grid\Column\TextColumn;

class LinkColumn extends TextColumn
{
    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'link';
    }
}