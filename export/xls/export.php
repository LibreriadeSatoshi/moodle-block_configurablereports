<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Configurable Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @date: 2009
 */

function export_report($report) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/lib/excellib.class.php');

    $table = $report->table;
    $report_name = $report->name ?? 'report';
    $filename = clean_filename($report_name.'_'.(time()).'.xlsx');

    // Creating a workbook.
    $workbook = new MoodleExcelWorkbook("-");
    $workbook->send($filename);
    $myxls = $workbook->add_worksheet($report_name);

    // Define Formats
    $header_format = $workbook->add_format([
        'bold' => 1,
        'size' => 14,
        'color' => 'white',
        'bg_color' => '#F7931A',
        'align' => 'center',
        'v_align' => 'vcenter',
        'border' => 1
    ]);

    $data_format = $workbook->add_format([
        'v_align' => 'top',
        'border' => 1,
        'text_wrap' => 1
    ]);

    $link_format = $workbook->add_format([
        'color' => 'blue',
        'underline' => 1,
        'v_align' => 'top',
        'border' => 1,
        'text_wrap' => 0
    ]);

    $row_idx = 0;

    // 1. Write headers
    if (!empty($table->head)) {
        $myxls->set_row($row_idx, 40);
        foreach ($table->head as $col_idx => $heading) {
            $cleaned_heading = htmlspecialchars_decode(strip_tags(nl2br($heading)));
            $myxls->write_string($row_idx, $col_idx, $cleaned_heading, $header_format);
        }
        $row_idx++;
    }

    // 2. Write data rows
    if (!empty($table->data)) {
        foreach ($table->data as $rkey => $row) {
            foreach ($row as $col_idx => $item) {
                // Check for links: <a href="URL">Label</a>
                if (preg_match('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $item, $matches)) {
                    $url = $matches[1];
                    $label = htmlspecialchars_decode(strip_tags($matches[2]));
                    
                    // Native Moodle write_url doesn't support separate labels.
                    // We use Reflection to access the private PhpSpreadsheet object.
                    $myxls->write_url($row_idx, $col_idx, $url, $link_format);
                    try {
                        $rc = new ReflectionClass($myxls);
                        $pr = $rc->getProperty('worksheet');
                        $pr->setAccessible(true);
                        $internal_ws = $pr->getValue($myxls);
                        // Set the label as the visible text, maintaining the link created by write_url.
                        $internal_ws->setCellValueByColumnAndRow($col_idx + 1, $row_idx + 1, $label);
                    } catch (Exception $e) {
                        // Fallback to URL if reflection fails.
                    }
                } else {
                    $cleaned_item = htmlspecialchars_decode(strip_tags(nl2br($item)));
                    if (is_numeric($cleaned_item) && strlen($cleaned_item) < 15) {
                        $myxls->write_number($row_idx, $col_idx, $cleaned_item, $data_format);
                    } else {
                        $myxls->write_string($row_idx, $col_idx, $cleaned_item, $data_format);
                    }
                }
            }
            $row_idx++;
        }
    }

    // 3. Dynamic Column Layout (based on Python scan of your sample)
    if (!empty($table->head)) {
        foreach ($table->head as $col_idx => $heading) {
            $name = strtolower($heading);
            if (strpos($name, 'link') !== false || strpos($name, 'recording') !== false || strpos($name, 'presentation') !== false) {
                $myxls->set_column($col_idx, $col_idx, 70); 
            } else if (strpos($name, 'topic') !== false || strpos($name, 'name') !== false) {
                $myxls->set_column($col_idx, $col_idx, 47); // Adjusted to match your sample (46.57)
            } else if (strpos($name, 'id') !== false || strpos($name, 'code') !== false) {
                $myxls->set_column($col_idx, $col_idx, 27); // Adjusted to match your sample (26.6)
            } else {
                $myxls->set_column($col_idx, $col_idx, 25); 
            }
        }
    }

    $workbook->close();
    exit;
}
