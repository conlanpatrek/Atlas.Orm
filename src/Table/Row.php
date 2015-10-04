<?php
namespace Atlas\Table;

use Atlas\Exception;

/**
 * @todo Should $primary really be a constructor param? Or should it figure it
 * out the same way the table does? Or should it be an Identity object?
 */
class Row
{
    protected $init = []; // initial data
    protected $data = []; // current data, including default values
    protected $primaryCol; // primary column

    public function __construct(array $data, $primaryCol)
    {
        $this->data = array_merge($this->data, $data);
        $this->primaryCol = $primaryCol;

        if (! array_key_exists($this->primaryCol, $this->data)) {
            $this->data[$this->primaryCol] = null;
        }

        $this->init();
    }

    public function __get($col)
    {
        $this->assertHas($col);
        return $this->data[$col];
    }

    public function __set($col, $val)
    {
        $this->assertHas($col);

        $setPrimary = $col == $this->primaryCol
                   && $this->data[$this->primaryCol] !== null;
        if ($setPrimary) {
            $class = get_class($this);
            throw new Exception("{$class}::\${$col} is immutable");
        }

        $this->data[$col] = $val;
    }

    public function __isset($col)
    {
        $this->assertHas($col);
        return isset($this->data[$col]);
    }

    public function __unset($col)
    {
        $this->assertHas($col);

        $unsetPrimary = $col == $this->primaryCol
                     && $this->data[$this->primaryCol] !== null;
        if ($unsetPrimary) {
            $class = get_class($this);
            throw new Exception("{$class}::\${$col} is immutable");
        }

        $this->data[$col] = null;
    }

    protected function assertHas($col)
    {
        if (! $this->has($col)) {
            $class = get_class($this);
            throw new Exception("{$class}::\${$col} does not exist");
        }
    }

    public function has($col)
    {
        return array_key_exists($col, $this->data);
    }

    public function init()
    {
        $this->init = $this->data;
    }

    public function getPrimaryCol()
    {
        return $this->primaryCol;
    }

    public function getPrimaryVal()
    {
        return $this->data[$this->primaryCol];
    }

    public function getArrayCopy()
    {
        return $this->data;
    }

    public function getArrayCopyForInsert()
    {
        return $this->getArrayCopy();
    }

    public function getArrayCopyForUpdate()
    {
        $copy = $this->getArrayCopy();
        foreach ($this->data as $col => $curr) {
            $init = $this->init[$col];
            $same = (is_numeric($curr) && is_numeric($init))
                 ? $curr == $init // numeric, compare loosely
                 : $curr === $init; // not numeric, compare strictly
            if ($same) {
                unset($copy[$col]);
            }
        }
        return $copy;
    }

    public function getObjectCopy()
    {
        return (object) $this->getArrayCopy();
    }
}
