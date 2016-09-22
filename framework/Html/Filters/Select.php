<?php

namespace T4\Html\Filters;

use T4\Html\Filter;
use T4\Orm\Model;
use T4\Orm\ModelDataProvider;

class Select
    extends Filter
{

    protected $data = [];

    protected $dataValueColumn = Model::PK;
    protected $dataTitleColumn = 'title';

    public function __construct($name, $value, $options = [])
    {
        parent::__construct($name, $value, $options);
        if (!empty($options['dataValueColumn'])) {
            $this->dataValueColumn = $options['dataValueColumn'];
        }
        if (!empty($options['dataTitleColumn'])) {
            $this->dataTitleColumn = $options['dataTitleColumn'];
        }
        if ( isset($options['class']) ) {
            if ( is_subclass_of($options['class'], Model::class) ) {
                $this->setData($options['class']::findAll());
            } elseif ( is_subclass_of($options['class'], ModelDataProvider::class) ) {
                $this->setData( (new $options['class'])->getAll() );
            }
        }
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getQueryOptions($options = []) : array
    {
        if ('' === $this->value || null === $this->value) {
            return $options;
        }
        if (empty($options['where'])) {
            $options['where'] = 'TRUE';
        }
        $options['where'] .= ' AND ' . $this->name . ' LIKE ' . $this->getConnection()->quote('%' . $this->value . '%') . '';
        return $options;
    }
}