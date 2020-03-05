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

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/lib/filestorage/file_system.php');
require_once(dirname(__FILE__) . '/lib/filestorage/file_system_filedir.php');
/**
 * Alternative file_system_filedir class.
 * It redirects to the production site if a file is not available in the test site.
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wwu_file_system_filedir extends file_system_filedir {

    public function readfile(stored_file $file) {
        if ($this->is_file_readable_locally_by_storedfile($file, false)) {
            $path = $this->get_local_path_from_storedfile($file, false);
        } else {
            $orig_path = $_SERVER["REQUEST_URI"];
            if (strpos($orig_path, "/LearnWebTest/" . SYSTEM_NAME . "/") === 0) {
                $new_path = str_replace("/LearnWebTest/" . SYSTEM_NAME . "/", "/LearnWeb/learnweb2/", $orig_path);
                header("Location: $new_path");
                die();
            }
            $path = $this->get_remote_path_from_storedfile($file);
        }
        readfile_allow_large($path, $file->get_filesize());
    }
}