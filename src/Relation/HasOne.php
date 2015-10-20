<?php
namespace Atlas\Relation;

use Atlas\Mapper\Related;
use Atlas\Mapper\AbstractRecord;
use Atlas\Mapper\RecordSet;

class HasOne extends AbstractRelation
{
    public function stitchIntoRecord(
        AbstractRecord $nativeRecord,
        callable $custom = null
    ) {
        $this->fix();
        $foreignVal = $nativeRecord->{$this->nativeCol};
        $foreignRecord = $this->foreignSelect($foreignVal, $custom)->fetchRecord();
        $nativeRecord->{$this->name} = $foreignRecord;
    }

    public function stitchIntoRecordSet(
        RecordSet $nativeRecordSet,
        callable $custom = null
    ) {
        $this->fix();

        $foreignVals = $this->getUniqueVals($nativeRecordSet, $this->nativeCol);
        $foreignRecords = $this->groupRecordSets(
            $this->foreignSelect($foreignVals, $custom)->fetchRecordSet(),
            $this->foreignCol
        );

        foreach ($nativeRecordSet as $nativeRecord) {
            $foreignRecord = false;
            $key = $nativeRecord->{$this->nativeCol};
            if (isset($foreignRecords[$key])) {
                $foreignRecord = $foreignRecords[$key][0];
            }
            $nativeRecord->{$this->name} = $foreignRecord;
        }
    }
}
