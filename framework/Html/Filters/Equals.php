<?php

namespace T4\Html\Filters;

use T4\Dbal\Query;
use T4\Html\Filter;

class Equals
    extends Filter
{
    public function modifyQuery(Query $query) : Query
    {
        if ('' === $this->value || null === $this->value) {
            return $query;
        }
        if (empty($query->where)) {
            $query->where('TRUE');
        }
        $query->where($query->where . ' AND ' . $this->name . ' = :' . $this->name);
        $query->param(':' . $this->name, $this->value);
        return $query;
    }

    public function getQueryOptions($options = []) : array
    {
        if ('' === $this->value || null === $this->value) {
            return $options;
        }
        if (empty($options['where'])) {
            $options['where'] = 'TRUE';
        }
        $options['where'] .= ' AND ' . $this->name . ' = :' . $this->name;
        $options['params'][':' . $this->name] = $this->value;
        return $options;
    }
}