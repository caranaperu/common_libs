<?php

$p_ref_tables['fetch'] = ['sb1' => ['tb1', 'tb2'], 'sb2' => ['tb3', 'tb2']];
$p_ref_tables2['fetch'] = ['default' => 'tb1', 'tb2'];


function get_ref_Tables(array $p_ref_tables,$p_operation,$p_sub_operation): array {
    $ref_tables = isset($p_ref_tables[$p_operation]) ?? null;

    if ($ref_tables && count($ref_tables) > 0) {
        if ($p_sub_operation) {
            if (isset($p_ref_tables[$p_operation][$p_sub_operation])) {

            }

        } else {

        }
    }
}
